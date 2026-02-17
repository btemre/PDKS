<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendDailyReport extends Command
{
    protected $signature = 'report:daily
                            {--date= : Rapor tarihi (Y-m-d)}
                            {--no-mail : Sadece özet; mail gönderme}
                            {--emails= : Virgülle ayrılmış mail adresleri (boşsa tüm aktif mailler)}
                            {--detail : Ayrıntılı çıktı}';

    protected $description = 'PDKS günlük raporu: Gelmeyen, Giriş-Çıkış, Geç Gelen, Erken Çıkan, Çıkış Yapmayan, Kart Tanımsız. Mobil uyumlu mail gönderir.';

    /** Rapor bölüm anahtarları (tek kaynak) */
    public const SECTION_GIRIS_CIKIS = 'Giriş-Çıkış Kayıtları';
    public const SECTION_GELMEYEN = 'Gelmeyen Personeller';
    public const SECTION_GEC_GELEN = 'Geç Gelen Personeller';
    public const SECTION_ERKEN_CIKAN = 'Erken Çıkan Personeller';
    public const SECTION_CIKIS_YAPMAYAN = 'Çıkış İşlemi Yapmayan Personeller';
    public const SECTION_KART_TANIMSIZ = 'Kart Tanımsız Personeller';

    /** Mail ve konsol için bölüm sırası */
    public const SECTION_ORDER = [
        self::SECTION_GIRIS_CIKIS,
        self::SECTION_GELMEYEN,
        self::SECTION_GEC_GELEN,
        self::SECTION_ERKEN_CIKAN,
        self::SECTION_CIKIS_YAPMAYAN,
        self::SECTION_KART_TANIMSIZ,
    ];

    private bool $verbose = false;

    public function handle(): int
    {
        $this->verbose = $this->option('detail');
        $targetDate = $this->getTargetDate();

        $this->newLine();
        $this->header('PDKS Günlük Rapor', $targetDate);

        try {
            $allReports = $this->buildAllReports($targetDate);
            $this->displaySummary($allReports, $targetDate);

            if (!$this->option('no-mail')) {
                $this->sendBirimBazliReports($allReports, $targetDate);
            } else {
                $this->warn('Mail gönderimi atlandı (--no-mail).');
            }

            $this->footer('Rapor tamamlandı.');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->footer('Hata.', true);
            $this->error($e->getMessage());
            if ($this->verbose) {
                $this->error($e->getTraceAsString());
            }
            return Command::FAILURE;
        }
    }

    private function getTargetDate(): string
    {
        $date = $this->option('date');
        if ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        }
        return now()->format('Y-m-d');
    }

    private function isWorkDay(string $date): bool
    {
        $carbon = Carbon::parse($date);
        if ($carbon->isWeekend()) {
            return false;
        }
        $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
        return !in_array($date, $tatiller, true);
    }

    private function header(string $title, string $date): void
    {
        $this->line('╔════════════════════════════════════════════╗');
        $this->line('║  ' . $title . ' · ' . Carbon::parse($date)->locale('tr')->translatedFormat('d F Y') . '  ║');
        $this->line('╚════════════════════════════════════════════╝');
        $this->newLine();
    }

    private function footer(string $message, bool $error = false): void
    {
        $this->newLine();
        if ($error) {
            $this->error('── ' . $message . ' ──');
        } else {
            $this->info('── ' . $message . ' ──');
        }
        $this->newLine();
    }

    /** Tüm rapor bölümlerini oluşturur. */
    private function buildAllReports(string $targetDate): array
    {
        $sections = [
            self::SECTION_GIRIS_CIKIS     => fn () => $this->getGirisCikisKayitlari($targetDate),
            self::SECTION_GELMEYEN       => fn () => $this->getGelmeyenPersonel($targetDate),
            self::SECTION_GEC_GELEN      => fn () => $this->getGecGelenPersonel($targetDate),
            self::SECTION_ERKEN_CIKAN    => fn () => $this->getErkenCikanPersonel($targetDate),
            self::SECTION_CIKIS_YAPMAYAN => fn () => $this->getCikisIslemiYapmayanPersonel($targetDate),
            self::SECTION_KART_TANIMSIZ  => fn () => $this->getKartTanimsizPersonel($targetDate),
        ];

        $all = [];
        foreach (self::SECTION_ORDER as $key) {
            if ($this->verbose) {
                $this->line('  · ' . $key . ' hesaplanıyor...');
            }
            $all[$key] = $sections[$key]();
        }
        return $all;
    }

    private function displaySummary(array $reports, string $targetDate): void
    {
        $this->info('Özet');
        $this->line('────────────────────────────────────────────');

        foreach (self::SECTION_ORDER as $key) {
            $data = $reports[$key] ?? [];
            $count = $data instanceof Collection ? $data->count() : (is_countable($data) ? count($data) : 0);
            $this->line(sprintf('  %-32s %3d kişi', $key, $count));
        }

        $this->line('────────────────────────────────────────────');
        $this->newLine();
    }

    private function getKartTanimsizPersonel(string $targetDate): Collection
    {
        if (!$this->isWorkDay($targetDate)) {
            return collect();
        }

        $personeller = DB::table('personel as p')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                $join->on('ppk.personel_id', '=', 'p.personel_id')
                    ->where('p.personel_kartkullanim', '1');
            })
            ->where('p.personel_durum', '1')
            ->whereNull('ppk.kart_id')
            ->select('p.personel_adsoyad', 'b.birim_ad', 'b.birim_id', 'u.unvan_ad')
            ->get();

        return $personeller->map(fn ($p) => (object) [
            'personel_adsoyad' => $p->personel_adsoyad,
            'birim_ad' => $p->birim_ad,
            'birim_id' => $p->birim_id,
            'unvan_ad' => $p->unvan_ad,
            'tarih' => $targetDate,
            'giris' => null,
            'cikis' => null,
            'durum' => 'KART TANIMSIZ',
            'gecikme_suresi' => null,
        ])->sortBy('personel_adsoyad')->values();
    }

    private function getGelmeyenPersonel(string $targetDate): Collection
    {
        if (!$this->isWorkDay($targetDate)) {
            return collect();
        }

        $personeller = DB::table('personel as p')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->join('pdks_personel_kartlar as ppk', function ($join) {
                $join->on('ppk.personel_id', '=', 'p.personel_id')
                    ->where('p.personel_kartkullanim', '1');
            })
            ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
            ->where('p.personel_durum', '1')
            ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'b.birim_id', 'u.unvan_ad', 'ppk.kart_id')
            ->get();

        $izinliler = DB::table('izin as i')
            ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
            ->where('i.izin_durum', '1')
            ->whereDate('i.izin_baslayis', '<=', $targetDate)
            ->whereDate('i.izin_bitis', '>=', $targetDate)
            ->select('i.izin_personel', 'it.izin_ad')
            ->get()
            ->keyBy('izin_personel');

        $gelenKartlar = DB::table('pdks_cihaz_gecisler')
            ->leftJoin('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
            ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $targetDate)
            ->whereIn(DB::raw('COALESCE(pdks_cihazlar.cihaz_gecistipi, 3)'), [1, 3])
            ->pluck('pdks_cihaz_gecisler.kart_id')
            ->unique()
            ->toArray();

        $gelmeyenler = collect();
        foreach ($personeller as $personel) {
            if ($izinliler->has($personel->personel_id)) {
                $durum = 'İZİNLİ (' . $izinliler[$personel->personel_id]->izin_ad . ')';
            } elseif (!in_array($personel->kart_id, $gelenKartlar, true)) {
                $durum = 'GELMEDİ';
            } else {
                continue;
            }
            $gelmeyenler->push((object) [
                'personel_adsoyad' => $personel->personel_adsoyad,
                'birim_ad' => $personel->birim_ad,
                'birim_id' => $personel->birim_id,
                'unvan_ad' => $personel->unvan_ad,
                'tarih' => $targetDate,
                'giris' => null,
                'cikis' => null,
                'durum' => $durum,
                'gecikme_suresi' => null,
            ]);
        }

        return $gelmeyenler->sortBy('durum')->values();
    }

    private function getGirisCikisKayitlari(string $targetDate): Collection
    {
        $rows = DB::table('personel')
            ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
            ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->leftJoin('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
            ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
            ->leftJoin('izin_mazeret', function ($join) use ($targetDate) {
                $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                    ->whereDate('izin_mazeret.izinmazeret_baslayis', $targetDate);
            })
            ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
            ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $targetDate)
            ->where('personel.personel_durum', '1')
            ->select(
                'personel.personel_adsoyad',
                'birim.birim_ad',
                'birim.birim_id',
                'unvan.unvan_ad',
                DB::raw("'{$targetDate}' as tarih"),
                DB::raw('GROUP_CONCAT(pdks_cihaz_gecisler.gecis_tarihi ORDER BY pdks_cihaz_gecisler.gecis_tarihi ASC) as gecisler'),
                DB::raw('MIN(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (1, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as ilk_giris'),
                DB::raw('MAX(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (2, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as son_cikis'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_turu')
            )
            ->groupBy('personel.personel_id')
            ->get();

        return $rows->map(function ($record) {
            $girisZamani = $record->ilk_giris ? Carbon::parse($record->ilk_giris) : null;
            $cikisZamani = $record->son_cikis ? Carbon::parse($record->son_cikis) : null;

            // Çıkış yapmamış kayıtlar: tek geçiş veya ilk_giris == son_cikis ise (cihaz tipi 3 tek kayıtta hem giriş hem çıkış sayılıyor)
            // gerçek çıkış yok, cikisZamani'yı null say
            $gercekCikisVar = $cikisZamani && $girisZamani && $cikisZamani->gt($girisZamani);
            if (!$gercekCikisVar && $cikisZamani) {
                $cikisZamani = null;
            }

            $mesaiGiris = Carbon::parse($record->tarih . ' ' . $record->mesai_giris);
            $mesaiCikis = Carbon::parse($record->tarih . ' ' . $record->mesai_cikis);

            $durum = 'NORMAL';
            $gecikme = null;

            if ($girisZamani && $girisZamani->gt($mesaiGiris)) {
                $gecikme = $girisZamani->diff($mesaiGiris);
                $durum = 'GEÇ GELDİ';
            }
            if ($cikisZamani && $cikisZamani->lt($mesaiCikis)) {
                $durum = $durum === 'GEÇ GELDİ' ? 'GEÇ GELDİ & ERKEN ÇIKTI' : 'ERKEN ÇIKTI';
            }
            if (!$cikisZamani && $girisZamani) {
                $durum = $durum === 'GEÇ GELDİ' ? 'GEÇ GELDİ & ÇIKIŞ YAPMADI' : 'ÇIKIŞ YAPMADI';
            }
            if ($record->izin_turu) {
                $durum = 'İZİNLİ (' . $record->izin_turu . ')';
            }

            $record->giris = $girisZamani ? $girisZamani->format('H:i:s') : null;
            $record->cikis = $cikisZamani ? $cikisZamani->format('H:i:s') : null;
            $record->durum = $durum;
            $record->gecikme_suresi = $gecikme ? $gecikme->format('%H:%I') : null;
            return $record;
        });
    }

    private function getErkenCikanPersonel(string $targetDate): Collection
    {
        $rows = DB::table('personel')
            ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
            ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->leftJoin('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
            ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
            ->leftJoin('izin_mazeret', function ($join) use ($targetDate) {
                $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                    ->whereDate('izin_mazeret.izinmazeret_baslayis', $targetDate);
            })
            ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $targetDate)
            ->where('personel.personel_durum', '1')
            ->whereNull('izin_mazeret.izinmazeret_id')
            ->select(
                'personel.personel_adsoyad',
                'birim.birim_ad',
                'birim.birim_id',
                'unvan.unvan_ad',
                DB::raw("'{$targetDate}' as tarih"),
                DB::raw('MIN(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (1, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as giris'),
                DB::raw('MAX(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (2, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as cikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis')
            )
            ->groupBy('personel.personel_id')
            ->havingRaw('MAX(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (2, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) IS NOT NULL')
            ->havingRaw('TIME(MAX(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (2, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END)) < MAX(mesai_saati.mesai_cikis)')
            ->get();

        return $rows->filter(function ($record) {
            // Giriş ve çıkış saati aynıysa (tek geçiş, çıkış yapmamış) erken çıkan listesine dahil etme
            $girisZamani = $record->giris ? Carbon::parse($record->giris) : null;
            $cikisZamani = $record->cikis ? Carbon::parse($record->cikis) : null;
            if (!$girisZamani || !$cikisZamani) {
                return false;
            }
            return $cikisZamani->gt($girisZamani);
        })->map(function ($record) {
            $cikisZamani = Carbon::parse($record->cikis);
            $mesaiCikis = Carbon::parse($record->tarih . ' ' . $record->mesai_cikis);
            $erkenlik = $mesaiCikis->diff($cikisZamani);
            $record->durum = 'ERKEN ÇIKTI';
            $record->gecikme_suresi = $erkenlik->format('%H:%I') . ' erken';
            $record->giris = Carbon::parse($record->giris)->format('H:i:s');
            $record->cikis = Carbon::parse($record->cikis)->format('H:i:s');
            return $record;
        });
    }

    private function getCikisIslemiYapmayanPersonel(string $targetDate): Collection
    {
        if (!$this->isWorkDay($targetDate)) {
            return collect();
        }

        $rows = DB::table('personel')
            ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
            ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->leftJoin('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
            ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
            ->leftJoin('izin_mazeret', function ($join) use ($targetDate) {
                $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                    ->whereDate('izin_mazeret.izinmazeret_baslayis', $targetDate);
            })
            ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $targetDate)
            ->where('personel.personel_durum', '1')
            ->whereNull('izin_mazeret.izinmazeret_id')
            ->select(
                'personel.personel_adsoyad',
                'birim.birim_ad',
                'birim.birim_id',
                'unvan.unvan_ad',
                DB::raw("'{$targetDate}' as tarih"),
                DB::raw('MIN(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (1, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as giris'),
                DB::raw('MAX(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (2, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as cikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis')
            )
            ->groupBy('personel.personel_id')
            ->havingRaw('MIN(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (1, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) IS NOT NULL')
            ->get();

        return $rows->filter(function ($record) {
            $girisZamani = $record->giris ? Carbon::parse($record->giris) : null;
            $cikisZamani = $record->cikis ? Carbon::parse($record->cikis) : null;
            // Çıkış yok veya giriş ile aynı (tek geçiş) = çıkış işlemi yapmamış
            return $girisZamani && (!$cikisZamani || !$cikisZamani->gt($girisZamani));
        })->map(function ($record) {
            $record->durum = 'ÇIKIŞ YAPMADI';
            $record->gecikme_suresi = null;
            $record->giris = Carbon::parse($record->giris)->format('H:i:s');
            $record->cikis = null;
            return $record;
        })->sortBy('personel_adsoyad')->values();
    }

    private function getGecGelenPersonel(string $targetDate): Collection
    {
        return DB::table('personel')
            ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
            ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->leftJoin('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
            ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
            ->leftJoin('izin_mazeret', function ($join) use ($targetDate) {
                $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                    ->whereDate('izin_mazeret.izinmazeret_baslayis', $targetDate);
            })
            ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $targetDate)
            ->where('personel.personel_durum', '1')
            ->whereNull('izin_mazeret.izinmazeret_id')
            ->select(
                'personel.personel_adsoyad',
                'birim.birim_ad',
                'birim.birim_id',
                'unvan.unvan_ad',
                DB::raw("'{$targetDate}' as tarih"),
                DB::raw('MIN(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (1, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as giris'),
                DB::raw('MAX(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (2, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END) as cikis'),
                DB::raw("'GEÇ GELDİ' as durum"),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris')
            )
            ->groupBy('personel.personel_id')
            ->havingRaw('TIME(MIN(CASE WHEN COALESCE(pdks_cihazlar.cihaz_gecistipi, 3) IN (1, 3) THEN pdks_cihaz_gecisler.gecis_tarihi END)) > MAX(mesai_saati.mesai_giris)')
            ->get()
            ->map(function ($record) {
                $girisZamani = Carbon::parse($record->giris);
                $mesaiGiris = Carbon::parse($record->tarih . ' ' . $record->mesai_giris);
                $record->gecikme_suresi = $girisZamani->diff($mesaiGiris)->format('%H:%I');
                return $record;
            });
    }

    private function sendBirimBazliReports(array $allReports, string $targetDate): void
    {
        // ID 1 olan mail her zaman alıcılara eklenir (arayüzde görünmez)
        $hiddenRecipientEmail = DB::table('rapor_mail')
            ->where('id', 1)
            ->where('durum', 1)
            ->value('email');

        $query = DB::table('rapor_mail')
            ->join('birim', 'birim.birim_id', '=', 'rapor_mail.birim')
            ->where('rapor_mail.durum', 1)
            ->where('rapor_mail.id', '!=', 1)
            ->select('rapor_mail.email', 'rapor_mail.birim', 'birim.birim_ad');

        $emailsOption = $this->option('emails');
        if (!empty($emailsOption)) {
            $emailList = array_map('trim', explode(',', $emailsOption));
            $query->whereIn('rapor_mail.email', $emailList);
        }

        $mailListesi = $query->get();

        if ($mailListesi->isEmpty()) {
            $this->warn('Aktif rapor mail adresi bulunamadı.');
            return;
        }

        $this->info('Mail gönderimi');
        $this->line('────────────────────────────────────────────');

        $birimBazliMailler = $mailListesi->groupBy('birim');
        $gonderimSayisi = 0;
        $formattedDate = Carbon::parse($targetDate)->locale('tr')->translatedFormat('d.m.Y');

        foreach ($birimBazliMailler as $birimId => $birimMailleri) {
            $birimAd = $birimMailleri->first()->birim_ad;

            $birimRaporlari = [];
            foreach (self::SECTION_ORDER as $key) {
                $birimRaporlari[$key] = ($allReports[$key] ?? collect())->filter(
                    fn ($kayit) => ($kayit->birim_id ?? null) == $birimId
                );
            }

            $toplamKayit = collect($birimRaporlari)->sum->count();
            if ($toplamKayit === 0) {
                if ($this->verbose) {
                    $this->line("  · {$birimAd}: kayıt yok, atlandı.");
                }
                continue;
            }

            $birimEmails = $birimMailleri->pluck('email')->toArray();
            // ID 1 olan mail her zaman alıcı listesine eklenir (arayüzde görünmez)
            if ($hiddenRecipientEmail && !in_array($hiddenRecipientEmail, $birimEmails, true)) {
                $birimEmails[] = $hiddenRecipientEmail;
            }
            $subject = "PDKS {$birimAd} · {$formattedDate}";

            try {
                foreach ($birimEmails as $email) {
                    Mail::to($email)->send(new DailyReportMail(
                        $birimRaporlari,
                        $subject,
                        self::SECTION_ORDER
                    ));
                    $gonderimSayisi++;
                }
                $this->line("  ✓ {$birimAd}: {$toplamKayit} kayıt, " . count($birimEmails) . " adres");
            } catch (\Throwable $e) {
                $this->error("  ✗ {$birimAd}: " . $e->getMessage());
            }
        }

        $this->line('────────────────────────────────────────────');
        $this->info("Toplam {$gonderimSayisi} mail gönderildi.");
        $this->newLine();
    }
}
