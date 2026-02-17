<?php

namespace App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Personel;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\PdksGecisService;
use App\Services\PdksCacheService;
use App\Http\Requests\PdksGecisEkleRequest;
use App\Models\PdksGunlukAciklama;

class PdksController extends Controller
{
    protected PdksCacheService $pdksCache;

    public function __construct(PdksCacheService $pdksCache)
    {
        $this->pdksCache = $pdksCache;
    }
    public function Bugun2()
    {
        if (!Auth::user()->hasPermissionTo('pdks.bugun')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->bolge_id;
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            //->where('personel.personel_kurumid', $kurum_id)
            ->where('personel.personel_birim', $birim_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Bugün Yapılan Giriş Çıkış Kayıtları';
        $pagetitle = 'PDKS Bugün Yapılan Giriş Çıkış Kayıtları';
        if (request()->ajax()) {
            $today = Carbon::today()->toDateString();
            return DataTables()->of(
                Personel::select(
                    'personel.personel_id',
                    DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                    DB::raw('MAX(birim.birim_ad) as birim_ad'),
                    DB::raw('MAX(durum.durum_ad) as durum_ad'),
                    DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                    DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                    DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                    DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                    DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                    DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                    DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                    DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                    DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                    DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                    DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                    DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                    DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                    DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                    DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
                )
                    ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                    ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                    ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                    ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                    ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                    ->leftJoin('izin_mazeret', function ($join) {
                        $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                            ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                    })
                    ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                    ->where('personel.personel_durum', '1')
                    ->where('personel.personel_birim', $birim_id)
                    ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $today)  // Burada bugüne ait kayıtları filtreliyoruz

                    ->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'))

            )
                ->addColumn('action', 'admin.backend.pdks.bugun-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.bugun', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function Bugun()
    {
        $user = auth()->user();
        $bolge_id = $user->bolge_id;
        $birim_id = $user->birim_id;

        $birim = $this->pdksCache->getBirim();
        $unvan = $this->pdksCache->getUnvan();
        $gorev = $this->pdksCache->getGorev();
        $durum = $this->pdksCache->getDurum();
        $izintur = $this->pdksCache->getIzinTurleri();

        $personel = Personel::pdksYetki($user)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();

        $personelkart = Personel::pdksYetki($user)
            ->where('personel_durum', '1')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Bugün Yapılan Giriş Çıkış Kayıtları';
        $pagetitle = 'PDKS Bugün Yapılan Giriş Çıkış Kayıtları';

        // 🔹 AJAX çağrısı (DataTables)
        if (request()->ajax()) {
            $today = Carbon::today()->toDateString();

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $today)
                ->pdksYetki($user)
                ->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            return DataTables()
                ->of($query)
                ->addColumn('action', 'admin.backend.pdks.bugun-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.bugun', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
        }
    public function GirisCikisFiltresiz()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_kurumid', $kurum_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_kurumid', $kurum_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Giriş Çıkış Kayıtları';
        $pagetitle = 'PDKS Giriş Çıkış Kayıtları';
        if (request()->ajax()) {
            $today = Carbon::today()->toDateString();
            return DataTables()->of(
                Personel::select(
                    'personel.personel_id',
                    DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                    DB::raw('MAX(birim.birim_ad) as birim_ad'),
                    DB::raw('MAX(durum.durum_ad) as durum_ad'),
                    DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                    DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                    DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                    DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                    DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                    DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                    DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                    DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                    DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                    DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                    DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                    DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                    DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                    DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                    DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                    DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
                )
                    ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                    ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                    ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                    ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                    ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                    ->leftJoin('izin_mazeret', function ($join) {
                        $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                            ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                    })
                    ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                    ->where('personel.personel_durum', '1')
                    ->where('personel.personel_kurumid', $kurum_id)
                    //->whereDate('pdks_cihaz_gecisler.gecis_tarihi', $today)  // Burada bugüne ait kayıtları filtreliyoruz

                    ->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'))

            )
                ->addColumn('action', 'admin.backend.pdks.giriscikis-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.giriscikis', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function GirisCikis2()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_birim', $birim_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Giriş Çıkış Kayıtları';
        $pagetitle = 'PDKS Giriş Çıkış Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->where('personel.personel_birim', $birim_id);

            //  TARİH ARALIĞI FİLTRESİ BURAYA EKLENDİ
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
                }
            }
            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.giriscikis-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.giriscikis', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function GirisCikis()
    {
    $user = auth()->user();
    $birim_id = $user->birim_id;
    $bolge_id = $user->bolge_id;
    $isYonetici = $user->yonetici == 1;

    $birim = $this->pdksCache->getBirim();
    $unvan = $this->pdksCache->getUnvan();
    $gorev = $this->pdksCache->getGorev();
    $durum = $this->pdksCache->getDurum();
    $izintur = $this->pdksCache->getIzinTurleri();

    $personel = Personel::pdksYetki($user)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();

    $personelkart = Personel::pdksYetki($user)
        ->where('personel_durum', '1')
        ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
        ->orderBy('personel.personel_adsoyad', 'asc')
        ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
        ->get();

    $title = 'PDKS Giriş Çıkış Kayıtları';
    $pagetitle = 'PDKS Giriş Çıkış Kayıtları';

    if (request()->ajax()) {
        $dateRange = request('date_range');

        $query = Personel::select(
            'personel.personel_id',
            DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
            DB::raw('MAX(birim.birim_ad) as birim_ad'),
            DB::raw('MAX(durum.durum_ad) as durum_ad'),
            DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
            DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
            DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
            DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
            DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
            DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
            DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
            DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
            DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
            DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
            DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
            DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
            DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
            DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
            DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
            DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
            DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
            DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
            DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
        )
            ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
            ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
            ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
            ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
            ->leftJoin('izin_mazeret', function ($join) {
                $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                    ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
            })
            ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
            ->where('personel.personel_durum', '1');

        $query->pdksYetki($user);

        // Tarih aralığı filtresi
        $start = null;
        $end = null;
        if (!empty($dateRange)) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $start = $dates[0];
                $end = $dates[1];
                $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
            }
        }

        $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

        // Sorgu sonuçlarını Collection olarak al
        $results = $query->get()->map(function ($item) {
            $data = $item->getAttributes();
            $data['is_hafta_sonu'] = 0;
            return $data;
        });

        // Hafta sonu günlerini tüm personel için ekle
        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

            $today = Carbon::today()->toDateString();
            $weekendDates = [];
            foreach ($period as $date) {
                if ($date->isWeekend() && $date->toDateString() <= $today) {
                    $weekendDates[] = $date->toDateString();
                }
            }

            if (count($weekendDates) > 0) {
                $allPersonel = Personel::pdksYetki($user)
                    ->where('personel.personel_durum', '1')
                    ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->select(
                    'personel.personel_id',
                    'personel.personel_adsoyad',
                    'birim.birim_ad',
                    'durum.durum_ad',
                    'unvan.unvan_ad'
                )->get();

                $existingKeys = [];
                foreach ($results as $item) {
                    $existingKeys[$item['personel_id'] . '_' . $item['tarih']] = true;
                }

                foreach ($allPersonel as $person) {
                    foreach ($weekendDates as $weekendDate) {
                        $key = $person->personel_id . '_' . $weekendDate;
                        if (!isset($existingKeys[$key])) {
                            $results->push([
                                'personel_id' => $person->personel_id,
                                'personel_adsoyad' => $person->personel_adsoyad,
                                'birim_ad' => $person->birim_ad,
                                'durum_ad' => $person->durum_ad,
                                'unvan_ad' => $person->unvan_ad,
                                'gecis_id' => null,
                                'izin_ad' => null,
                                'izin_durum' => null,
                                'izinmazeret_baslayis' => null,
                                'izinmazeret_baslayissaat' => null,
                                'izinmazeret_bitissaat' => null,
                                'izinmazeret_aciklama' => null,
                                'mesai_id' => null,
                                'mesai_giris' => null,
                                'mesai_oglengiris' => null,
                                'mesai_oglencikis' => null,
                                'mesai_cikis' => null,
                                'kart_id' => null,
                                'tarih' => $weekendDate,
                                'giris' => null,
                                'oglengiris' => null,
                                'oglencikis' => null,
                                'cikis' => null,
                                'is_hafta_sonu' => 1,
                            ]);
                        }
                    }
                }
            }
        }

        return DataTables()->of($results)
            ->addColumn('action', 'admin.backend.pdks.giriscikis-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    return view('admin.backend.pdks.giriscikis', compact(
        'title',
        'pagetitle',
        'durum',
        'gorev',
        'unvan',
        'birim',
        'personel',
        'izintur',
        'personelkart'
    ));
    }

    /**
     * Giriş-Çıkış sayfası Excel export - seçilen tarih aralığındaki tüm verileri döner.
     */
    public function GirisCikisExport(Request $request)
    {
        $user = auth()->user();
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        $dateRange = $request->get('date_range');
        if (empty($dateRange)) {
            return redirect()->route('pdks.giriscikis')
                ->with('error', 'Excel export için lütfen tarih aralığı seçin.');
        }

        $dates = explode(' - ', $dateRange);
        if (count($dates) !== 2) {
            return redirect()->route('pdks.giriscikis')
                ->with('error', 'Geçersiz tarih aralığı.');
        }
        $start = $dates[0];
        $end = $dates[1];

        // Doğrudan ham SQL ile GROUP BY - CLI debug ile doğrulandı
        $bolgeOrBirimFilter = $isYonetici
            ? "AND personel.personel_bolge = " . intval($bolge_id)
            : "AND personel.personel_birim = " . intval($birim_id);

        $sql = "
            SELECT
                personel.personel_id,
                MAX(personel.personel_adsoyad) as personel_adsoyad,
                MAX(birim.birim_ad) as birim_ad,
                MAX(durum.durum_ad) as durum_ad,
                DATE(g.gecis_tarihi) as tarih,
                MIN(g.gecis_tarihi) as giris,
                MAX(g.gecis_tarihi) as cikis,
                MAX(izin_turleri.izin_ad) as izin_ad,
                MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama
            FROM pdks_cihaz_gecisler g
            JOIN pdks_kartlar k ON k.kart_id = g.kart_id
            JOIN personel ON personel.personel_id = k.kart_personelid
            JOIN birim ON birim.birim_id = personel.personel_birim
            JOIN durum ON durum.durum_id = personel.personel_durumid
            LEFT JOIN izin_mazeret ON izin_mazeret.izinmazeret_personel = personel.personel_id
                AND DATE(izin_mazeret.izinmazeret_baslayis) = DATE(g.gecis_tarihi)
            LEFT JOIN izin_turleri ON izin_turleri.izin_turid = izin_mazeret.izinmazeret_turid
            WHERE personel.personel_durum = '1'
              AND DATE(g.gecis_tarihi) BETWEEN ? AND ?
              {$bolgeOrBirimFilter}
            GROUP BY personel.personel_id, DATE(g.gecis_tarihi)
            ORDER BY DATE(g.gecis_tarihi) DESC, MAX(personel.personel_adsoyad) ASC
        ";

        $rows = DB::select($sql, [$start, $end]);

        // Hafta sonu günlerini ekle
        $rowsCollection = collect($rows);
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        $today = Carbon::today()->toDateString();
        $weekendDates = [];
        foreach ($period as $date) {
            if ($date->isWeekend() && $date->toDateString() <= $today) {
                $weekendDates[] = $date->toDateString();
            }
        }

        if (count($weekendDates) > 0) {
            $allPersonelQuery = DB::table('personel')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->where('personel.personel_durum', '1');

            if ($isYonetici) {
                $allPersonelQuery->where('personel.personel_bolge', $bolge_id);
            } else {
                $allPersonelQuery->where('personel.personel_birim', $birim_id);
            }

            $allPersonel = $allPersonelQuery->select(
                'personel.personel_id',
                'personel.personel_adsoyad',
                'birim.birim_ad',
                'durum.durum_ad'
            )->get();

            $existingKeys = [];
            foreach ($rowsCollection as $row) {
                $existingKeys[$row->personel_id . '_' . $row->tarih] = true;
            }

            foreach ($allPersonel as $person) {
                foreach ($weekendDates as $weekendDate) {
                    $key = $person->personel_id . '_' . $weekendDate;
                    if (!isset($existingKeys[$key])) {
                        $rowsCollection->push((object) [
                            'personel_id' => $person->personel_id,
                            'personel_adsoyad' => $person->personel_adsoyad,
                            'birim_ad' => $person->birim_ad,
                            'durum_ad' => $person->durum_ad,
                            'tarih' => $weekendDate,
                            'giris' => null,
                            'cikis' => null,
                            'izin_ad' => 'HAFTA SONU',
                            'izinmazeret_aciklama' => null,
                        ]);
                    }
                }
            }
        }

        $rows = $rowsCollection->sortBy([
            ['tarih', 'desc'],
            ['personel_adsoyad', 'asc'],
        ])->values()->all();

        $filename = 'giriscikis_' . $start . '_' . $end . '.csv';

        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $out = $bom;
        $out .= implode(';', ['Sıra', 'Birim', 'Statu', 'Personel', 'Tarih', 'Giriş', 'Çıkış', 'Açıklama']) . "\r\n";

        foreach ($rows as $index => $row) {
            $girisStr = $row->giris ?? '';
            $cikisStr = $row->cikis ?? '';

            $tarih = $row->tarih ? date('d-m-Y', strtotime($row->tarih)) : '';
            $giris = $girisStr ? date('H:i', strtotime($girisStr)) : '';
            $cikis = $cikisStr ? date('H:i', strtotime($cikisStr)) : '';

            // Giriş ve çıkış aynı anda ise (tek geçiş) veya fark 3 dk'dan azsa
            if ($girisStr && $cikisStr) {
                $farkSaniye = abs(strtotime($cikisStr) - strtotime($girisStr));
                if ($farkSaniye < 180) {
                    $cikis = 'Çıkış Yapılmadı';
                }
            }

            $aciklama = !empty($row->izin_ad) ? $row->izin_ad : (!empty($row->izinmazeret_aciklama) ? $row->izinmazeret_aciklama : '');
            $line = [
                $index + 1,
                $row->birim_ad ?? '',
                $row->durum_ad ?? '',
                $row->personel_adsoyad ?? '',
                $tarih,
                $giris,
                $cikis,
                $aciklama,
            ];
            $out .= implode(';', array_map(function ($v) {
                return '"' . str_replace('"', '""', (string) $v) . '"';
            }, $line)) . "\r\n";
        }

        return response($out, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function GecGelen22()
    {
        $kurum_id = auth()->user()->kurum_id;

        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_kurumid', $kurum_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_kurumid', $kurum_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Geç Gelen Personel Kayıtları';
        $pagetitle = 'PDKS Geç Gelen Personel Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->where('personel.personel_kurumid', $kurum_id);

            //  TARİH ARALIĞI FİLTRESİ BURAYA EKLENDİ
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
                }
            }
            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.gecgelen-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gecgelen', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function GecGelen2()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_birim', $birim_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Geç Gelen Personel Kayıtları';
        $pagetitle = 'PDKS Geç Gelen Personel Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->where('personel.personel_birim', $birim_id);

            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
                }
            }

            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            // Geç gelen personelleri filtrele
            $query->havingRaw('TIME(MIN(pdks_cihaz_gecisler.gecis_tarihi)) > MAX(mesai_saati.mesai_giris)');

            // İzinli olanları hariç tutmak istersen:
            $query->where(function ($q) {
                $q->whereNull('izin_mazeret.izinmazeret_id')
                    ->orWhere('izin_mazeret.izinmazeret_durum', '=', '1');
                    //->orWhere('izin_mazeret.izinmazeret_durum', '!=', '1');
            });

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.gecgelen-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gecgelen', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function GecGelen()
    {
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;
    
        // Sabit veriler
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $izintur = DB::table('izin_turleri')
            ->where('izin_durum', '1')
            ->where('izin_statu', 2)
            ->orderBy('izin_ad', 'asc')
            ->get();
    
        // Personel listesi
        $personel = DB::table('personel')
            ->where('personel_durum', '1')
            ->when($isYonetici, function ($q) use ($bolge_id) {
                $q->where('personel_bolge', $bolge_id);
            }, function ($q) use ($birim_id) {
                $q->where('personel_birim', $birim_id);
            })
            ->orderBy('personel_adsoyad', 'asc')
            ->get();
    
        // Personel kart listesi
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_durum', '1')
            ->when($isYonetici, function ($q) use ($bolge_id) {
                $q->where('personel.personel_bolge', $bolge_id);
            }, function ($q) use ($birim_id) {
                $q->where('personel.personel_birim', $birim_id);
            })
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();
    
        $title = 'PDKS Geç Gelen Personel Kayıtları';
        $pagetitle = 'PDKS Geç Gelen Personel Kayıtları';
    
        if (request()->ajax()) {
            $dateRange = request('date_range');
    
            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->when($isYonetici, function ($q) use ($bolge_id) {
                    $q->where('personel.personel_bolge', $bolge_id);
                }, function ($q) use ($birim_id) {
                    $q->where('personel.personel_birim', $birim_id);
                });
    
            // Tarih aralığı filtresi
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$dates[0], $dates[1]]);
                }
            }
    
            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'))
                  ->havingRaw('TIME(MIN(pdks_cihaz_gecisler.gecis_tarihi)) > MAX(mesai_saati.mesai_giris)')
                  ->where(function ($q) {
                      $q->whereNull('izin_mazeret.izinmazeret_id')
                        ->orWhere('izin_mazeret.izinmazeret_durum', '=', '1');
                  });
    
            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.gecgelen-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.pdks.gecgelen', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function ErkenCikan22()
    {
        $kurum_id = auth()->user()->kurum_id;

        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_kurumid', $kurum_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_kurumid', $kurum_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Erken Çıkan Personel Kayıtları';
        $pagetitle = 'PDKS Erken Çıkan Personel Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MIN(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglengiris'),
                DB::raw('MAX(CASE WHEN TIME(pdks_cihaz_gecisler.gecis_tarihi) BETWEEN mesai_saati.mesai_oglengiris AND mesai_saati.mesai_oglencikis THEN pdks_cihaz_gecisler.gecis_tarihi ELSE NULL END) as oglencikis'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->where('personel.personel_kurumid', $kurum_id);

            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
                }
            }

            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            //  Erken çıkan personelleri filtrele: çıkış saati mesai saatinden küçükse
            $query->havingRaw('TIME(MAX(pdks_cihaz_gecisler.gecis_tarihi)) < MAX(mesai_saati.mesai_cikis)');

            //  İzinlileri hariç tut
            $query->where(function ($q) {
                $q->whereNull('izin_mazeret.izinmazeret_id')
                    ->orWhere('izin_mazeret.izinmazeret_durum', '!=', '1');
            });

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.erkencikan-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.erkencikan', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function ErkenCikan2()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;

        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $personelkart = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_birim', $birim_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad')
            ->get();

        $title = 'PDKS Erken Çıkan Personel Kayıtları';
        $pagetitle = 'PDKS Erken Çıkan Personel Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_oglengiris) as mesai_oglengiris'),
                DB::raw('MAX(mesai_saati.mesai_oglencikis) as mesai_oglencikis'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('MAX(pdks_kartlar.kart_id) as kart_id'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis'),
                DB::raw('COUNT(pdks_cihaz_gecisler.gecis_tarihi) as toplam_gecis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('pdks_gecis_turu', 'pdks_gecis_turu.gecis_id', '=', 'pdks_cihazlar.cihaz_gecistipi')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1')
                ->where('personel.personel_birim', $birim_id);

            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
                }
            }

            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            // Giriş-çıkışı olan ve mesai saatinden erken çıkanları getir
            $query->havingRaw("
            COUNT(pdks_cihaz_gecisler.gecis_tarihi) > 1
            AND TIME(MIN(pdks_cihaz_gecisler.gecis_tarihi)) < TIME(MAX(pdks_cihaz_gecisler.gecis_tarihi))
            AND TIME(MAX(pdks_cihaz_gecisler.gecis_tarihi)) < MAX(mesai_saati.mesai_cikis)
        ");

            // İzinlileri hariç tut
            $query->where(function ($q) {
                $q->whereNull('izin_mazeret.izinmazeret_id')
                    ->orWhere('izin_mazeret.izinmazeret_durum', '=', '1');
            });

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.erkencikan-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.erkencikan', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function ErkenCikan()
    {
        $user = auth()->user();

        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        // Ortak listeler (view için)
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();

        // Personel listesi
        $personelQuery = DB::table('personel')
            ->where('personel_durum', '1')
            ->orderBy('personel_adsoyad', 'asc');

        if ($isYonetici) {
            $personelQuery->where('personel_bolge', $bolge_id);
        } else {
            $personelQuery->where('personel_birim', $birim_id);
        }

        $personel = $personelQuery->get();

        // İzin türleri ve personel kartları
        $izintur = DB::table('izin_turleri')
            ->where('izin_durum', '1')
            ->where('izin_statu', 2)
            ->orderBy('izin_ad', 'asc')
            ->get();

        $personelkartQuery = DB::table('personel')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_id', 'personel.personel_adsoyad');

        if ($isYonetici) {
            $personelkartQuery->where('personel.personel_bolge', $bolge_id);
        } else {
            $personelkartQuery->where('personel.personel_birim', $birim_id);
        }

        $personelkart = $personelkartQuery->get();

        $title = 'PDKS Erken Çıkan Personel Kayıtları';
        $pagetitle = 'PDKS Erken Çıkan Personel Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = Personel::select(
                'personel.personel_id',
                DB::raw('MAX(personel.personel_adsoyad) as personel_adsoyad'),
                DB::raw('MAX(birim.birim_ad) as birim_ad'),
                DB::raw('MAX(durum.durum_ad) as durum_ad'),
                DB::raw('MAX(unvan.unvan_ad) as unvan_ad'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_id) as gecis_id'),
                DB::raw('MAX(izin_turleri.izin_ad) as izin_ad'),
                DB::raw('MAX(izin_mazeret.izinmazeret_durum) as izin_durum'),
                DB::raw('MAX(izin_mazeret.izinmazeret_baslayis) as izinmazeret_baslayis'),
                DB::raw('MIN(izin_mazeret.izinmazeret_baslayissaat) as izinmazeret_baslayissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_bitissaat) as izinmazeret_bitissaat'),
                DB::raw('MAX(izin_mazeret.izinmazeret_aciklama) as izinmazeret_aciklama'),
                DB::raw('MAX(mesai_saati.mesai_id) as mesai_id'),
                DB::raw('MAX(mesai_saati.mesai_giris) as mesai_giris'),
                DB::raw('MAX(mesai_saati.mesai_cikis) as mesai_cikis'),
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis'),
                DB::raw('COUNT(pdks_cihaz_gecisler.gecis_tarihi) as toplam_gecis')
            )
                ->join('pdks_kartlar', 'pdks_kartlar.kart_personelid', '=', 'personel.personel_id')
                ->join('pdks_cihaz_gecisler', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_gecisler.cihaz_id')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('mesai_saati', 'mesai_saati.mesai_id', '=', 'personel.personel_mesai')
                ->leftJoin('izin_mazeret', function ($join) {
                    $join->on('izin_mazeret.izinmazeret_personel', '=', 'personel.personel_id')
                        ->on(DB::raw('DATE(izin_mazeret.izinmazeret_baslayis)'), '=', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));
                })
                ->leftJoin('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin_mazeret.izinmazeret_turid')
                ->where('personel.personel_durum', '1');

            // Yönetici kontrolü (bölge veya birim)
            if ($isYonetici) {
                $query->where('personel.personel_bolge', $bolge_id);
            } else {
                $query->where('personel.personel_birim', $birim_id);
            }

            // Tarih aralığı
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'), [$start, $end]);
                }
            }

            $query->groupBy('personel.personel_id', DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'));

            // Erken çıkanları filtrele
            $query->havingRaw("
                COUNT(pdks_cihaz_gecisler.gecis_tarihi) > 1
                AND TIME(MAX(pdks_cihaz_gecisler.gecis_tarihi)) < MAX(mesai_saati.mesai_cikis)
            ");

            // İzinlileri hariç tut
            $query->where(function ($q) {
                $q->whereNull('izin_mazeret.izinmazeret_id')
                    ->orWhere('izin_mazeret.izinmazeret_durum', '=', '1');
            });

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.pdks.erkencikan-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.erkencikan', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'personel',
            'izintur',
            'personelkart'
        ));
    }
    public function GecisLog2()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $bolge_id = auth()->user()->bolge_id;

        $title = 'PDKS Geçiş Kayıtları';
        $pagetitle = 'PDKS Geçiş Kayıtları';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = DB::table('pdks_cihaz_gecisler as pcg')
                ->join('pdks_personel_kartlar as pk', 'pk.kart_id', '=', 'pcg.kart_id')
                ->join('personel as p', 'p.personel_id', '=', 'pk.personel_id')
                ->join('pdks_cihazlar as pc', 'pc.cihaz_id', '=', 'pcg.cihaz_id')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->select(
                    'b.birim_ad as birim_ad',
                    'd.durum_ad as durum_ad',
                    'u.unvan_ad as unvan_ad',
                    'p.personel_adsoyad as personel_adsoyad',
                    'pcg.gecis_tarihi as gecis_tarihi',
                    'pc.cihaz_adi as cihaz_adi',
                    'pcg.gecis_id'
                );
                if (auth()->user()->yonetici == 1) {
                    $query->where('p.personel_bolge', $bolge_id);
                } else {
                    $query->where('p.personel_birim', $birim_id);
                }
            // Tarih aralığı filtresi
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pcg.gecis_tarihi)'), [$start, $end]);
                }
            }

            return DataTables()->of($query)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gecislog', compact('title', 'pagetitle'));
    }
    public function GecisLog(Request $request)
    {
        $user = auth()->user();
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;

        $title = 'PDKS Geçiş Kayıtları';
        $pagetitle = 'PDKS Geçiş Kayıtları';

        if ($request->ajax()) {
            $query = DB::table('pdks_cihaz_gecisler as pcg')
                ->leftJoin('pdks_personel_kartlar as pk', 'pk.kart_id', '=', 'pcg.kart_id')
                ->leftJoin('personel as p', 'p.personel_id', '=', 'pk.personel_id')
                ->leftJoin('pdks_cihazlar as pc', 'pc.cihaz_id', '=', 'pcg.cihaz_id')
                ->leftJoin('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->leftJoin('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->select(
                    'pcg.gecis_id',
                    'b.birim_ad',
                    'd.durum_ad',
                    'u.unvan_ad',
                    'p.personel_adsoyad',
                    DB::raw("DATE_FORMAT(pcg.gecis_tarihi, '%Y-%m-%d %H:%i:%s') as gecis_tarihi"),
                    'pc.cihaz_adi'
                );

            // Bölge/Birim filtreleri
            if ($user->yonetici == 1) {
                $query->where('p.personel_bolge', $bolge_id);
            } else {
                $query->where('p.personel_birim', $birim_id);
            }

            // Tarih aralığı filtresi
            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('Y-m-d', trim($dates[0]))->startOfDay();
                        $end = \Carbon\Carbon::createFromFormat('Y-m-d', trim($dates[1]))->endOfDay();
                        $query->whereBetween('pcg.gecis_tarihi', [$start, $end]);
                    } catch (\Exception $e) {
                        // Tarih formatı uyuşmazsa hata vermesin
                    }
                }
            }

            return datatables()
                ->of($query)
                ->addIndexColumn()
                ->filter(function ($instance) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $search = $request->search['value'];
                        $instance->where(function ($q) use ($search) {
                            $q->where('p.personel_adsoyad', 'like', "%{$search}%")
                                ->orWhere('b.birim_ad', 'like', "%{$search}%")
                                ->orWhere('d.durum_ad', 'like', "%{$search}%")
                                ->orWhere('u.unvan_ad', 'like', "%{$search}%")
                                ->orWhere('pc.cihaz_adi', 'like', "%{$search}%");
                        });
                    }
                })
                ->make(true);
        }

        return view('admin.backend.pdks.gecislog', compact('title', 'pagetitle'));
    }
    public function Bugun22()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $title = 'PDKS Bugün Yapılan Giriş Çıkış Kayıtları';
        $pagetitle = 'PDKS Bugün Yapılan Giriş Çıkış Kayıtları';

        if (request()->ajax()) {
            $today = date('Y-m-d');

            $subQuery = DB::table('pdks_cihaz_gecisler as gecis')
                ->select(
                    'kart.kart_personelid',
                    DB::raw('DATE(gecis.gecis_tarihi) as tarih'),
                    DB::raw('MIN(gecis.gecis_tarihi) as giris_saati'),
                    DB::raw('MAX(gecis.gecis_tarihi) as cikis_saati')
                )
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->whereDate('gecis.gecis_tarihi', $today)
                ->groupBy('kart.kart_personelid', DB::raw('DATE(gecis.gecis_tarihi)'));

            $data = DB::table('personel')
                ->joinSub($subQuery, 'gecis', function ($join) {
                    $join->on('personel.personel_id', '=', 'gecis.kart_personelid');
                })
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(
                    'personel.personel_id',
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'durum.durum_ad',
                    'unvan.unvan_ad',
                    'birim.birim_ad',
                    'gorev.gorev_ad',
                    'gecis.giris_saati',
                    'gecis.cikis_saati',
                    'gecis.tarih'
                );

            return DataTables()->of($data)
                ->addColumn('action', 'admin.backend.pdks.bugun-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.bugun', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim'
        ));
    }
    /**
     * Geçiş Ekle modalında Select2 ajax için personel/kart arama (lazy yükleme).
     * GET: q veya search → Select2 formatında { results: [ {id, text} ] }
     */
    public function PersonelKartAra(Request $request)
    {
        $q = $request->get('q', $request->get('search', '')) ?: '';
        $user = auth()->user();

        $query = Personel::pdksYetki($user)
            ->where('personel_durum', '1')
            ->join('pdks_personel_kartlar', 'personel.personel_id', '=', 'pdks_personel_kartlar.personel_id')
            ->select('pdks_personel_kartlar.kart_id', 'personel.personel_adsoyad')
            ->orderBy('personel.personel_adsoyad');

        if (strlen($q) >= 1) {
            $query->where('personel.personel_adsoyad', 'like', '%' . $q . '%');
        }

        $items = $query->limit(30)->get();

        $results = $items->map(fn ($row) => ['id' => (string) $row->kart_id, 'text' => $row->personel_adsoyad]);

        return response()->json(['results' => $results]);
    }

    public function PdksGecisEkle(PdksGecisEkleRequest $request)
    {
        try {
            $cihazId = $request->filled('cihaz_id') ? (int) $request->cihaz_id : 99;
            $result = PdksGecisService::insertGecis(
                (int) $request->kart_id,
                $request->gecis_tarihi,
                $cihazId
            );

            if ($result['skipped_duplicate']) {
                return response()->json([
                    'durum' => 'warning',
                    'mesaj' => 'Mükerrer kayıt engellendi. Aynı kart için bu zaman diliminde zaten geçiş kaydı bulunuyor.',
                ]);
            }

            if ($result['inserted']) {
                return response()->json([
                    'durum' => 'success',
                    'mesaj' => 'Geçiş Ekleme İşlemi Başarılı!',
                ]);
            }

            return response()->json([
                'durum' => 'error',
                'mesaj' => 'Geçiş Eklenemedi!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => 'error',
                'mesaj' => 'Hata oluştu: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Geç gelen / erken çıkan için günlük not getir (4.4).
     */
    public function GunlukAciklamaGetir(Request $request)
    {
        $request->validate([
            'personel_id' => 'required|integer',
            'tarih' => 'required|date',
            'tip' => 'required|in:gec_gelen,erken_cikan',
        ]);
        $tarihStr = \Carbon\Carbon::parse($request->tarih)->format('Y-m-d');
        $aciklama = PdksGunlukAciklama::notGetir(
            (int) $request->personel_id,
            $tarihStr,
            $request->tip
        );
        return response()->json(['aciklama' => $aciklama]);
    }

    /**
     * Geç gelen / erken çıkan için günlük not kaydet (4.4).
     */
    public function GunlukAciklamaKaydet(Request $request)
    {
        $request->validate([
            'personel_id' => 'required|integer',
            'tarih' => 'required|date',
            'tip' => 'required|in:gec_gelen,erken_cikan',
            'aciklama' => 'nullable|string|max:500',
        ]);
        $tarihStr = \Carbon\Carbon::parse($request->tarih)->format('Y-m-d');
        PdksGunlukAciklama::updateOrCreate(
            [
                'personel_id' => $request->personel_id,
                'tarih' => $tarihStr,
                'tip' => $request->tip,
            ],
            [
                'aciklama' => $request->aciklama ?: null,
                'ekleyen_user_id' => auth()->id(),
            ]
        );
        return response()->json(['durum' => 'success', 'mesaj' => 'Not kaydedildi.']);
    }

    /**
     * Cihazdan gelen geçiş kaydı (sync/API). Mükerrer kuralı PdksGecisService ile uygulanır.
     * POST: kart_id, gecis_tarihi (Y-m-d H:i:s), cihaz_id (zorunlu)
     */
    public function CihazGecisKayit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kart_id' => 'required|integer',
            'gecis_tarihi' => 'required|date',
            'cihaz_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'durum' => 'error',
                'mesaj' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $result = PdksGecisService::insertGecis(
                (int) $request->kart_id,
                $request->gecis_tarihi,
                (int) $request->cihaz_id
            );

            if ($result['skipped_duplicate']) {
                return response()->json([
                    'durum' => 'warning',
                    'mesaj' => 'Mükerrer kayıt engellendi.',
                    'inserted' => false,
                ]);
            }

            if ($result['inserted']) {
                return response()->json([
                    'durum' => 'success',
                    'mesaj' => 'Geçiş kaydı alındı.',
                    'inserted' => true,
                ]);
            }

            return response()->json([
                'durum' => 'error',
                'mesaj' => 'Kayıt eklenemedi.',
                'inserted' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'durum' => 'error',
                'mesaj' => 'Hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Gelmeye2n()
    {
        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // 1. Giriş yapan personel ID'leri
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(
                    DB::raw('DATE(gecis.gecis_tarihi) as tarih'),
                    'personel.personel_id'
                )
                ->distinct()
                ->get()
                ->groupBy('tarih');

            // 2. Günlük izinli personel ID'leri
            $gunlukIzinliler = DB::table('izin')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin_personel'
                )
                ->get();

            // 3. Belirtilen tarih aralığındaki her gün için gelmeyen personelleri topla
            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $gelmeyenler = collect();

            foreach ($days as $day) {
                $tarih = $day->toDateString();

                // Giriş yapanlar (o gün)
                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                // O gün izinli olanlar
                $izinliler = $gunlukIzinliler->filter(function ($item) use ($tarih) {
                    return $item->baslangic <= $tarih && $item->bitis >= $tarih;
                })->pluck('izin_personel')->unique()->toArray();

                // Haricinde kalanları al
                $haricTut = array_merge($gelenler, $izinliler);

                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', $haricTut)
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get();

                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelmeyenddd()
    {
        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // Giriş yapanlar
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(
                    DB::raw('DATE(gecis.gecis_tarihi) as tarih'),
                    'personel.personel_id'
                )
                ->distinct()
                ->get()
                ->groupBy('tarih');

            // İzinli personeller (izin_turleri ile birlikte)
            $gunlukIzinliler = DB::table('izin')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin.izin_personel',
                    'izin_turleri.izin_ad'
                )
                ->get();

            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $gelmeyenler = collect();

            foreach ($days as $day) {
                $tarih = $day->toDateString();
                // GELECEK TARİHLERİ ATLAMAK
                if ($tarih > now()->toDateString()) {
                    continue;
                }
                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                $gununIzinlileri = $gunlukIzinliler->filter(function ($item) use ($tarih) {
                    return $item->baslangic <= $tarih && $item->bitis >= $tarih;
                });

                $izinliIDs = $gununIzinlileri->pluck('izin_personel')->unique()->toArray();
                $izinTurleriMap = $gununIzinlileri->keyBy('izin_personel');

                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', array_merge($gelenler, [])) // gelenler dışında
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get()
                    ->map(function ($personel) use ($izinliIDs, $izinTurleriMap) {
                        if (in_array($personel->personel_id, $izinliIDs)) {
                            $personel->gelmedi = $izinTurleriMap[$personel->personel_id]->izin_ad;
                        } else {
                            $personel->gelmedi = 'GELMEDİ';
                        }
                        return $personel;
                    });

                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelmeyenwwww()
    {
        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // Giriş yapanlar
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(
                    DB::raw('DATE(gecis.gecis_tarihi) as tarih'),
                    'personel.personel_id'
                )
                ->distinct()
                ->get()
                ->groupBy('tarih');

            // İzinli personeller (izin_turleri ile birlikte)
            $gunlukIzinliler = DB::table('izin')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin.izin_personel',
                    'izin_turleri.izin_ad'
                )
                ->get();

            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $gelmeyenler = collect();

            foreach ($days as $day) {
                $tarih = $day->toDateString();

                // GELECEK TARİHLERİ ATLAMAK
                if ($tarih > now()->toDateString()) {
                    continue;
                }

                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                $gununIzinlileri = $gunlukIzinliler->filter(function ($item) use ($tarih) {
                    return $item->baslangic <= $tarih && $item->bitis >= $tarih;
                });

                $izinliIDs = $gununIzinlileri->pluck('izin_personel')->unique()->toArray();
                $izinTurleriMap = $gununIzinlileri->keyBy('izin_personel');

                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', array_merge($gelenler, []))
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        'personel.personel_kartkullanim', // Kart kullanımı alanını ekle
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get()
                    ->map(function ($personel) use ($izinliIDs, $izinTurleriMap) {
                        // Kart kullanım durumu kontrolü
                        if ($personel->personel_kartkullanim == 0) {
                            $personel->gelmedi = 'TANIMSIZ'; // Tanımsız olarak işaretle
                            $personel->kartDurum = 'bg-warning'; // Sarı renk
                        } elseif (in_array($personel->personel_id, $izinliIDs)) {
                            $personel->gelmedi = $izinTurleriMap[$personel->personel_id]->izin_ad;
                            $personel->kartDurum = 'bg-success'; // Yeşil renk, izinli
                        } else {
                            $personel->gelmedi = 'GELMEDİ'; // Diğer durumlar
                            $personel->kartDurum = 'bg-danger'; // Kırmızı renk
                        }
                        return $personel;
                    });

                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->addColumn('kartDurum', function ($personel) {
                    return $personel->kartDurum; // kartDurum bilgisiyle sarı, yeşil veya kırmızı renk döndür
                })
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelmeyewwn()
    {

        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // Giriş yapanlar
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(
                    DB::raw('DATE(gecis.gecis_tarihi) as tarih'),
                    'personel.personel_id'
                )
                ->distinct()
                ->get()
                ->groupBy('tarih');

            $resmiTatiller = DB::table('tatil')
                ->whereBetween('tatil_tarih', [$startDate, $endDate])
                ->pluck('tatil_tarih')
                ->map(fn($date) => \Carbon\Carbon::parse($date)->toDateString())
                ->toArray();


            // İzinli personeller (izin_turleri ile birlikte)
            $gunlukIzinliler = DB::table('izin')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin.izin_personel',
                    'izin_turleri.izin_ad'
                )
                ->get();

            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $gelmeyenler = collect();

            foreach ($days as $day) {
                $tarih = $day->toDateString();

                // GELECEK TARİHLERİ ATLAMAK
                if ($tarih > now()->toDateString()) {
                    continue;
                }

                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                $gununIzinlileri = $gunlukIzinliler->filter(function ($item) use ($tarih) {
                    return $item->baslangic <= $tarih && $item->bitis >= $tarih;
                });

                $izinliIDs = $gununIzinlileri->pluck('izin_personel')->unique()->toArray();
                $izinTurleriMap = $gununIzinlileri->keyBy('izin_personel');

                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', $gelenler)
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'personel.personel_kartkullanim',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get()
                    ->map(function ($personel) use ($izinliIDs, $izinTurleriMap, $tarih, $resmiTatiller) {
                        $carbonDate = \Carbon\Carbon::parse($tarih);

                        if (in_array($tarih, $resmiTatiller)) {
                            $personel->durum_turu = 'resmitatil';
                            $personel->durum_label = 'RESMİ TATİL';
                            $personel->durum_renk = 'bg-primary text-white';
                        } elseif ($personel->personel_kartkullanim == 0) {
                            $personel->durum_turu = 'tanimsiz';
                            $personel->durum_label = 'TANIMSIZ';
                            $personel->durum_renk = 'bg-warning text-dark';
                        } elseif (in_array($personel->personel_id, $izinliIDs)) {
                            $izinAd = $izinTurleriMap[$personel->personel_id]->izin_ad ?? 'İzinli';
                            $personel->durum_turu = 'izinli';
                            $personel->durum_label = $izinAd;
                            $personel->durum_renk = 'bg-success text-white';
                        } elseif ($carbonDate->isWeekend()) {
                            $personel->durum_turu = 'haftasonu';
                            $personel->durum_label = 'HAFTA SONU';
                            $personel->durum_renk = 'bg-secondary text-white';
                        } else {
                            $personel->durum_turu = 'gelmedi';
                            $personel->durum_label = 'GELMEDİ';
                            $personel->durum_renk = 'bg-danger text-white';
                        }

                        return $personel;
                    });



                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelssmeyen()
    {
        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // Giriş yapanlar
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(DB::raw('DATE(gecis.gecis_tarihi) as tarih'), 'personel.personel_id')
                ->distinct()
                ->get()
                ->groupBy('tarih');

            // Resmi tatiller
            $resmiTatiller = DB::table('tatil')
                ->whereBetween('tatil_tarih', [$startDate, $endDate])
                ->pluck('tatil_tarih')
                ->map(fn($t) => \Carbon\Carbon::parse($t)->toDateString())
                ->toArray();

            // İzinli personeller
            $gunlukIzinliler = DB::table('izin')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin.izin_personel',
                    'izin_turleri.izin_ad'
                )
                ->get();

            $gelmeyenler = collect();
            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($days as $day) {
                $tarih = $day->toDateString();

                // Gelecekteki tarihler atlanır
                if ($tarih > now()->toDateString())
                    continue;

                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                $gununIzinlileri = $gunlukIzinliler->filter(fn($i) => $i->baslangic <= $tarih && $i->bitis >= $tarih);
                $izinliIDs = $gununIzinlileri->pluck('izin_personel')->unique()->toArray();
                $izinTurleriMap = $gununIzinlileri->keyBy('izin_personel');

                // GELMEYENLER
                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', $gelenler)
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'personel.personel_kartkullanim',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get()
                    ->map(function ($personel) use ($izinliIDs, $izinTurleriMap, $tarih, $resmiTatiller) {
                        $carbonDate = \Carbon\Carbon::parse($tarih);

                        if (in_array($personel->personel_id, $izinliIDs)) {
                            // 1. Öncelik: İzinli
                            $label = $izinTurleriMap[$personel->personel_id]->izin_ad ?? 'İzinli';
                            $color = 'bg-success text-white';
                            $turu = 'izinli';
                        } elseif (in_array($tarih, $resmiTatiller)) {
                            // 2. Öncelik: Resmi Tatil
                            $label = 'RESMİ TATİL';
                            $color = 'bg-primary text-white';
                            $turu = 'resmitatil';
                        } elseif ($carbonDate->isWeekend()) {
                            // 3. Öncelik: Hafta Sonu
                            $label = 'HAFTA SONU';
                            $color = 'bg-secondary text-white';
                            $turu = 'haftasonu';
                        } elseif ($personel->personel_kartkullanim == 0) {
                            // 4. Öncelik: Kart tanımsız
                            $label = 'TANIMSIZ';
                            $color = 'bg-warning text-dark';
                            $turu = 'tanimsiz';
                        } else {
                            // 5. Son seçenek: Gelmedi
                            $label = 'GELMEDİ';
                            $color = 'bg-danger text-white';
                            $turu = 'gelmedi';
                        }

                        $personel->durum_label = $label;
                        $personel->durum_renk = $color;
                        $personel->durum_turu = $turu;
                        return $personel;
                    });


                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelqmeyen()
    {
        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // Giriş yapanlar
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(DB::raw('DATE(gecis.gecis_tarihi) as tarih'), 'personel.personel_id')
                ->distinct()
                ->get()
                ->groupBy('tarih');

            // Resmi tatiller
            $resmiTatiller = DB::table('tatil')
                ->whereBetween('tatil_tarih', [$startDate, $endDate])
                ->pluck('tatil_tarih')
                ->map(fn($t) => \Carbon\Carbon::parse($t)->toDateString())
                ->toArray();

            // İzinli personeller
            $gunlukIzinliler = DB::table('izin')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin.izin_personel',
                    'izin_turleri.izin_ad'
                )
                ->get();

            $gelmeyenler = collect();
            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($days as $day) {
                $tarih = $day->toDateString();

                if ($tarih > now()->toDateString())
                    continue;

                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                $gununIzinlileri = $gunlukIzinliler->filter(fn($i) => $i->baslangic <= $tarih && $i->bitis >= $tarih);
                $izinliIDs = $gununIzinlileri->pluck('izin_personel')->unique()->toArray();
                $izinTurleriMap = $gununIzinlileri->keyBy('izin_personel');

                // Resmi tatilse ve giriş yapmayan/izinli olmayanlar varsa onları tamamen atla
                if (in_array($tarih, $resmiTatiller)) {
                    $istisnalar = array_merge($gelenler, $izinliIDs);
                    $tumAktifler = DB::table('personel')
                        ->where('personel_kurumid', $kurum_id)
                        ->where('personel_durum', '1')
                        ->pluck('personel_id')
                        ->toArray();

                    // Eğer o gün hiç istisna yoksa (ne giriş ne izin), devam et (listeleme)
                    if (count(array_diff($tumAktifler, $istisnalar)) == 0) {
                        continue;
                    }
                }

                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', $gelenler)
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'personel.personel_kartkullanim',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get()
                    ->map(function ($personel) use ($izinliIDs, $izinTurleriMap, $tarih, $resmiTatiller) {
                        $carbonDate = \Carbon\Carbon::parse($tarih);

                        if (in_array($personel->personel_id, $izinliIDs)) {
                            $label = $izinTurleriMap[$personel->personel_id]->izin_ad ?? 'İzinli';
                            $color = 'bg-success text-white';
                            $turu = 'izinli';
                        } elseif (in_array($tarih, $resmiTatiller)) {
                            $label = 'RESMİ TATİL';
                            $color = 'bg-primary text-white';
                            $turu = 'resmitatil';
                        } elseif ($carbonDate->isWeekend()) {
                            $label = 'HAFTA SONU';
                            $color = 'bg-secondary text-white';
                            $turu = 'haftasonu';
                        } elseif ($personel->personel_kartkullanim == 0) {
                            $label = 'TANIMSIZ';
                            $color = 'bg-warning text-dark';
                            $turu = 'tanimsiz';
                        } else {
                            $label = 'GELMEDİ';
                            $color = 'bg-danger text-white';
                            $turu = 'gelmedi';
                        }

                        $personel->durum_label = $label;
                        $personel->durum_renk = $color;
                        $personel->durum_turu = $turu;
                        return $personel;
                    })
                    ->filter(); // null'ları at (gerekirse)

                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelmdswdeyen()
    {
        $kurum_id = auth()->user()->kurum_id;

        if (request()->ajax()) {
            $dateRange = request('date_range');
            $dates = explode(' - ', $dateRange);

            $startDate = $dates[0] ?? now()->toDateString();
            $endDate = $dates[1] ?? now()->toDateString();

            // Giriş yapanlar
            $girisYapanlar = DB::table('pdks_cihaz_gecisler as gecis')
                ->join('pdks_kartlar as kart', 'kart.kart_id', '=', 'gecis.kart_id')
                ->join('personel', 'personel.personel_id', '=', 'kart.kart_personelid')
                ->whereBetween(DB::raw('DATE(gecis.gecis_tarihi)'), [$startDate, $endDate])
                ->where('personel.personel_kurumid', $kurum_id)
                ->where('personel.personel_durum', '1')
                ->select(
                    DB::raw('DATE(gecis.gecis_tarihi) as tarih'),
                    'personel.personel_id'
                )
                ->distinct()
                ->get()
                ->groupBy('tarih');

            // İzinli personeller (izin_turleri ile birlikte)
            $gunlukIzinliler = DB::table('izin')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween(DB::raw('DATE(izin_baslayis)'), [$startDate, $endDate])
                        ->orWhereBetween(DB::raw('DATE(izin_bitis)'), [$startDate, $endDate])
                        ->orWhere(function ($qq) use ($startDate, $endDate) {
                            $qq->whereDate('izin_baslayis', '<=', $startDate)
                                ->whereDate('izin_bitis', '>=', $endDate);
                        });
                })
                ->select(
                    DB::raw('DATE(izin_baslayis) as baslangic'),
                    DB::raw('DATE(izin_bitis) as bitis'),
                    'izin.izin_personel',
                    'izin_turleri.izin_ad'
                )
                ->get();

            $days = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $gelmeyenler = collect();

            foreach ($days as $day) {
                $tarih = $day->toDateString();

                // GELECEK TARİHLERİ ATLAMAK
                if ($tarih > now()->toDateString()) {
                    continue;
                }

                $gelenler = $girisYapanlar->get($tarih)?->pluck('personel_id')->unique()->toArray() ?? [];

                $gununIzinlileri = $gunlukIzinliler->filter(function ($item) use ($tarih) {
                    return $item->baslangic <= $tarih && $item->bitis >= $tarih;
                });

                $izinliIDs = $gununIzinlileri->pluck('izin_personel')->unique()->toArray();
                $izinTurleriMap = $gununIzinlileri->keyBy('izin_personel');

                $gununGelmeyenleri = DB::table('personel')
                    ->leftJoin('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                    ->leftJoin('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                    ->leftJoin('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                    ->where('personel.personel_kurumid', $kurum_id)
                    ->where('personel.personel_durum', '1')
                    ->whereNotIn('personel.personel_id', $gelenler)
                    ->select(
                        'personel.personel_id',
                        'personel.personel_adsoyad',
                        'personel.personel_kartkullanim',
                        'birim.birim_ad',
                        'unvan.unvan_ad',
                        'durum.durum_ad',
                        DB::raw("'$tarih' as tarih")
                    )
                    ->get()
                    ->map(function ($personel) use ($izinliIDs, $izinTurleriMap) {
                        if ($personel->personel_kartkullanim == 0) {
                            $personel->durum_turu = 'tanimsiz';
                            $personel->durum_label = 'TANIMSIZ';
                            $personel->durum_renk = 'bg-warning text-dark';
                        } elseif (in_array($personel->personel_id, $izinliIDs)) {
                            $izinAd = $izinTurleriMap[$personel->personel_id]->izin_ad ?? 'İzinli';
                            $personel->durum_turu = 'izinli';
                            $personel->durum_label = $izinAd;
                            $personel->durum_renk = 'bg-success text-white';
                        } else {
                            $personel->durum_turu = 'gelmedi';
                            $personel->durum_label = 'GELMEDİ';
                            $personel->durum_renk = 'bg-danger text-white';
                        }
                        return $personel;
                    });

                $gelmeyenler = $gelmeyenler->merge($gununGelmeyenleri);
            }

            return DataTables()->of($gelmeyenler)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', [
            'title' => 'PDKS Gelmeyen Personeller',
            'pagetitle' => 'PDKS Gelmeyen Personeller'
        ]);
    }
    public function Gelmeyen_asil()
    {
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Gelmeyen Personeller';
        $pagetitle = 'Gelmeyen Personeller';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            // Tarih aralığını üret
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;
            $allDates = collect();
            for ($date = $start; $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }

            // Tüm personelleri çek
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                    $join->on('ppk.personel_id', '=', 'p.personel_id')
                        ->where('p.personel_kartkullanim', '1'); // aktif kart
                })
                ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
                ->where('p.personel_kurumid', $kurum_id)
                ->where('p.personel_durum', '1')
                ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'u.unvan_ad', 'd.durum_ad', 'ppk.kart_id')
                ->get();

            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();

            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->where('i.izin_durum', '1')
                ->where('i.izin_kurumid', $kurum_id)
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();

                $gecisler = DB::table('pdks_cihaz_gecisler')
                ->select('kart_id', DB::raw('DATE(gecis_tarihi) as gun'))
                ->get()
                ->groupBy('gun')
                ->map(function ($rows) {
                    return $rows->pluck('kart_id')->toArray();
                });
            

            $data = [];
            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';
                    // Devredışı veya kart yok
                    if (!$personel->kart_id) {
                        $durum = 'TANIMSIZ';
                    }
                    // Tatil
                    elseif (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    }
                    // Hafta sonu
                    elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    }
                    // İzin
                    else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id &&
                                $gun >= $i->izin_baslayis && $gun <= $i->izin_bitis;
                        });
                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        }
                        // Gelmiş mi?
                        elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                            $durum = 'GELDİ';
                        }
                         else {
                            $durum = 'GELMEDİ';
                        }
                    }

                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun
                    ];
                }
            }

            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', compact('title', 'pagetitle'));
    }
    public function Gelmeddyen()
    {
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Gelmeyen Personeller';
        $pagetitle = 'Gelmeyen Personeller';
    
        if (request()->ajax()) {
            $dateRange = request('date_range');
    
            // Tarih aralığını üret
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;
    
            $allDates = collect();
            for ($date = $start; $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }
    
            // Tüm personeller
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->where('p.personel_kurumid', $kurum_id)
                ->where('p.personel_durum', '1')
                ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'u.unvan_ad', 'd.durum_ad')
                ->get();
    
            // Tatiller
            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
    
            // İzinler
            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->where('i.izin_durum', '1')
                ->where('i.izin_kurumid', $kurum_id)
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();
    
            // Geçişler (kart basma kayıtları)
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->select('kart_id', DB::raw('DATE(gecis_tarihi) as gun'))
                ->get()
                ->groupBy('gun')
                ->map(function ($rows) {
                    return $rows->pluck('kart_id')->toArray();
                });
    
            $data = [];
            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';
    
                    // 🔹 Önce personelin o gün kartlı mı olduğuna bakalım
                    $kartGecmisi = DB::table('personel_kart_gecmisi')
                        ->where('personel_id', $personel->personel_id)
                        ->whereDate('baslangic_tarihi', '<=', $gun)
                        ->where(function ($q) use ($gun) {
                            $q->whereNull('bitis_tarihi')
                              ->orWhereDate('bitis_tarihi', '>=', $gun);
                        })
                        ->first();
    
                    if (!$kartGecmisi) {
                        $durum = 'TANIMSIZ'; // O gün için kartlı değil
                    }
                    elseif (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    }
                    elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    }
                    else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id &&
                                $gun >= $i->izin_baslayis && $gun <= $i->izin_bitis;
                        });
    
                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        }
                        elseif (isset($gecisler[$gun]) && in_array($kartGecmisi->kart_id, $gecisler[$gun])) {
                            $durum = 'GELDİ';
                        }
                        else {
                            $durum = 'GELMEDİ';
                        }
                    }
    
                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun
                    ];
                }
            }
    
            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.pdks.gelmeyen', compact('title', 'pagetitle'));
    }
    public function Geldsdmeyen()
    {
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Gelmeyen Personeller';
        $pagetitle = 'Gelmeyen Personeller';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            // Tarih aralığı
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;

            // Tüm günler
            $allDates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }

            // Personeller
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->where('p.personel_kurumid', $kurum_id)
                ->where('p.personel_durum', '1')
                ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'u.unvan_ad', 'd.durum_ad')
                ->get();

            // Tatiller
            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->map(fn($t)=>Carbon::parse($t)->toDateString())->toArray();

            // İzinler
            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->where('i.izin_durum', '1')
                ->where('i.izin_kurumid', $kurum_id)
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();

            // Personel -> Kart ID eşlemesi
            $personelKartlar = DB::table('pdks_personel_kartlar')
                ->pluck('kart_id', 'personel_id'); // [personel_id => kart_id]

            // Giriş-çıkış kayıtları
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->select('kart_id', DB::raw('DATE(gecis_tarihi) as gun'))
                ->get()
                ->groupBy('gun')
                ->map(function ($rows) {
                    return $rows->pluck('kart_id')->toArray();
                });

            // Kart kullanım geçmişleri (tek query)
            $kartGecmisi = DB::table('personel_kart_gecmisi')
                ->get()
                ->groupBy('personel_id');

            $data = [];
            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';
            
                    // 1️⃣ Önce resmi tatil / hafta sonu kontrolü
                    if (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    }
                    elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    }
                    else {
                        // 2️⃣ Personelin o tarihte izinli mi?
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id &&
                                Carbon::parse($gun)->between(
                                    Carbon::parse($i->izin_baslayis),
                                    Carbon::parse($i->izin_bitis)
                                );
                        });
            
                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        }
                        else {
                            // 3️⃣ Kart tanımlı mı?
                            $kartGecmisi = DB::table('personel_kart_gecmisi')
                                ->where('personel_id', $personel->personel_id)
                                ->whereDate('baslangic_tarihi', '<=', $gun)
                                ->where(function ($q) use ($gun) {
                                    $q->whereNull('bitis_tarihi')
                                    ->orWhereDate('bitis_tarihi', '>=', $gun);
                                })
                                ->first();
            
                            if (!$kartGecmisi) {
                                $durum = 'TANIMSIZ';
                            }
                            elseif (isset($gecisler[$gun]) && in_array($kartGecmisi->kart_id, $gecisler[$gun])) {
                                $durum = 'GELDİ';
                            }
                            else {
                                $durum = 'GELMEDİ';
                            }
                        }
                    }
            
                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun
                    ];
                }
            }
            

            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', compact('title', 'pagetitle'));
    }
    public function Gelmeyen2() {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $title = 'Gelmeyen Personeller';
        $pagetitle = 'Gelmeyen Personeller';
    
        if (request()->ajax()) {
            $dateRange = request('date_range');
    
            // Tarih aralığını üret
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
    
            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;
    
            $allDates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }
    
            // Tüm personelleri çek
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                    $join->on('ppk.personel_id', '=', 'p.personel_id')
                        ->where('p.personel_kartkullanim', '1'); // aktif kart
                })
                ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
                ->where('p.personel_birim', $birim_id)
                ->where('p.personel_durum', '1')
                ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'u.unvan_ad', 'd.durum_ad', 'ppk.kart_id')
                ->get();
    
            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
    
            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->where('i.izin_durum', '1')
                ->where('i.izin_birim', $birim_id)
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();
    
            // GEÇİŞLER - Tarih bazında gruplandır
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->whereBetween(DB::raw('DATE(gecis_tarihi)'), [$start->toDateString(), $end->toDateString()])
                ->selectRaw('DATE(gecis_tarihi) as gecis_gun, kart_id')
                ->get()
                ->groupBy('gecis_gun')
                ->map(function ($group) {
                    return $group->pluck('kart_id')->unique()->toArray();
                })
                ->toArray();
    
            $data = [];
    
            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';
    
                    // Devredışı veya kart yok - Tanımsızları çıkar
                    if (!$personel->kart_id) {
                        continue;
                    }
                    // Tatil
                    elseif (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    }
                    // Hafta sonu
                    elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    }
                    // İzin
                    else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id
                                && $gun >= $i->izin_baslayis
                                && $gun <= $i->izin_bitis;
                        });
    
                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        }
                        // Gelmiş mi?
                        elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                            continue; // GELDİ durumunu çıkar
                        }
                        else {
                            $durum = 'GELMEDİ';
                        }
                    }
    
                    // Durumu ekle (TANIMSIZ ve GELDİ hariç)
                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun
                    ];
                }
            }
    
            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.pdks.gelmeyen', compact('title', 'pagetitle'));
    }
    public function Gelmeyen_yoneticisiz()
    {
    $kurum_id = auth()->user()->kurum_id;
    $birim_id = auth()->user()->birim_id;
    $title = 'Gelmeyen Personeller';
    $pagetitle = 'Gelmeyen Personeller';

    if (request()->ajax()) {
        $dateRange = request('date_range');

        // Tarih aralığını belirle
        if (!empty($dateRange)) {
            $dates = explode(' - ', $dateRange);
            $start = Carbon::parse($dates[0]);
            $end = Carbon::parse($dates[1]);
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;

        // Aralıktaki tüm günleri oluştur
        $allDates = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $allDates->push($date->toDateString());
        }

        // Personel + kart bilgilerini çek
        $personeller = DB::table('personel as p')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                $join->on('ppk.personel_id', '=', 'p.personel_id')
                    ->where('p.personel_kartkullanim', '1'); // aktif kart
            })
            ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
            ->where('p.personel_birim', $birim_id)
            ->where('p.personel_durum', '1')
            ->select(
                'p.personel_id',
                'p.personel_adsoyad',
                'b.birim_ad',
                'u.unvan_ad',
                'd.durum_ad',
                'ppk.kart_id',
                'pk.kart_eklemetarihi'
            )
            ->get();

        $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();

        $izinler = DB::table('izin as i')
            ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
            ->where('i.izin_durum', '1')
            ->where('i.izin_birim', $birim_id)
            ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
            ->get();

        // Geçişleri tarih bazında grupla
        $gecisler = DB::table('pdks_cihaz_gecisler')
            ->whereBetween(DB::raw('DATE(gecis_tarihi)'), [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE(gecis_tarihi) as gecis_gun, kart_id')
            ->get()
            ->groupBy('gecis_gun')
            ->map(function ($group) {
                return $group->pluck('kart_id')->unique()->toArray();
            })
            ->toArray();

        $data = [];

        foreach ($personeller as $personel) {
            foreach ($allDates as $gun) {
                $durum = '';

                // Eğer kart yoksa (tanımsız)
                if (!$personel->kart_id) {
                    continue;
                }

                // Kart ekleme tarihinden önceki günleri dahil etme
                if (!empty($personel->kart_eklemetarihi) && Carbon::parse($gun)->lt(Carbon::parse($personel->kart_eklemetarihi))) {
                    continue; // bu tarih kart tanımlanmadan önce
                }

                // Tatil
                if (in_array($gun, $tatiller)) {
                    $durum = 'RESMİ TATİL';
                }
                // Hafta sonu
                elseif (Carbon::parse($gun)->isWeekend()) {
                    $durum = 'HAFTA SONU';
                }
                // İzin
                else {
                    $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                        return $i->izin_personel == $personel->personel_id &&
                            $gun >= $i->izin_baslayis &&
                            $gun <= $i->izin_bitis;
                    });

                    if ($izinli) {
                        $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                    }
                    // Gelmiş mi?
                    elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                        continue; // geldiği günleri listeye ekleme
                    } else {
                        $durum = 'GELMEDİ';
                    }
                }

                // Tanımsız veya GELDİ durumları hariç
                $data[] = [
                    'birim_ad' => $personel->birim_ad,
                    'durum_ad' => $durum,
                    'unvan_ad' => $personel->unvan_ad,
                    'personel_adsoyad' => $personel->personel_adsoyad,
                    'tarih' => $gun,
                ];
            }
        }

        return DataTables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    return view('admin.backend.pdks.gelmeyen', compact('title', 'pagetitle'));
    }
    public function Gelmeyen()
    {
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $isYonetici = $user->yonetici == 1;

        $title = 'Gelmeyen Personeller';
        $pagetitle = 'Gelmeyen Personeller';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            // Tarih aralığını belirle
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;

            // Aralıktaki tüm günleri oluştur
            $allDates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }

            // Personel + kart bilgilerini çek
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                    $join->on('ppk.personel_id', '=', 'p.personel_id')
                        ->where('p.personel_kartkullanim', '1'); // aktif kart
                })
                ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
                ->when($isYonetici, function ($query) use ($kurum_id) {
                    return $query->where('p.personel_kurumid', $kurum_id);
                }, function ($query) use ($birim_id) {
                    return $query->where('p.personel_birim', $birim_id);
                })
                ->where('p.personel_durum', '1')
                ->select(
                    'p.personel_id',
                    'p.personel_adsoyad',
                    'b.birim_ad',
                    'u.unvan_ad',
                    'd.durum_ad',
                    'ppk.kart_id',
                    'pk.kart_eklemetarihi'
                )
                ->get();

            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();

            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->where('i.izin_durum', '1')
                ->where('i.izin_birim', $birim_id)
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();

            // Geçişleri tarih bazında grupla
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->whereBetween(DB::raw('DATE(gecis_tarihi)'), [$start->toDateString(), $end->toDateString()])
                ->selectRaw('DATE(gecis_tarihi) as gecis_gun, kart_id')
                ->get()
                ->groupBy('gecis_gun')
                ->map(function ($group) {
                    return $group->pluck('kart_id')->unique()->toArray();
                })
                ->toArray();

            $data = [];

            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';

                    // Eğer kart yoksa (tanımsız)
                    if (!$personel->kart_id) {
                        continue;
                    }

                    // Kart ekleme tarihinden önceki günleri dahil etme
                    if (!empty($personel->kart_eklemetarihi) && Carbon::parse($gun)->lt(Carbon::parse($personel->kart_eklemetarihi))) {
                        continue;
                    }

                    // Tatil
                    if (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    }
                    // Hafta sonu
                    elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    }
                    // İzin
                    else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id &&
                                $gun >= $i->izin_baslayis &&
                                $gun <= $i->izin_bitis;
                        });

                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        }
                        // Gelmiş mi?
                        elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                            continue; // geldiği günleri listeye ekleme
                        } else {
                            $durum = 'GELMEDİ';
                        }
                    }

                    // Tanımsız veya GELDİ durumları hariç
                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun,
                    ];
                }
            }

            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.gelmeyen', compact('title', 'pagetitle'));
    }
    public function Harekets2() {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $title = 'Tüm Hareketler';
        $pagetitle = 'Tüm Hareketler';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            // Tarih aralığını üret
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;

            $allDates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }

            // Tüm personelleri çek
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                    $join->on('ppk.personel_id', '=', 'p.personel_id')
                        ->where('p.personel_kartkullanim', '1'); // aktif kart
                })
                ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
                ->where('p.personel_birim', $birim_id)
                ->where('p.personel_durum', '1')
                ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'u.unvan_ad', 'd.durum_ad', 'ppk.kart_id')
                ->get();

            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();

            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->where('i.izin_durum', '1')
                ->where('i.izin_birim', $birim_id)
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();

            // GEÇİŞLER - DÜZELTME: Tarih bazında gruplandır
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->whereBetween(DB::raw('DATE(gecis_tarihi)'), [$start->toDateString(), $end->toDateString()])
                ->selectRaw('DATE(gecis_tarihi) as gecis_gun, kart_id')
                ->get()
                ->groupBy('gecis_gun')
                ->map(function ($group) {
                    return $group->pluck('kart_id')->unique()->toArray();
                })
                ->toArray();

            $data = [];

            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';

                    // Devredışı veya kart yok
                    if (!$personel->kart_id) {
                        $durum = 'TANIMSIZ';
                    }
                    // Tatil
                    elseif (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    }
                    // Hafta sonu
                    elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    }
                    // İzin
                    else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id
                                && $gun >= $i->izin_baslayis
                                && $gun <= $i->izin_bitis;
                        });

                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        }
                        // Gelmiş mi? - DÜZELTME: in_array kullan
                        elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                            $durum = 'GELDİ';
                        }
                        else {
                            $durum = 'GELMEDİ';
                        }
                    }

                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun
                    ];
                }
            }

            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.hareket', compact('title', 'pagetitle'));
    }
    public function Hareket2()
    {
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        $title = 'Tüm Hareketler';
        $pagetitle = 'Tüm Hareketler';

        if (request()->ajax()) {
            $dateRange = request('date_range');

            // Tarih aralığı
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;

            // Tüm günleri oluştur
            $allDates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }

            // 🔹 Personelleri çek (yöneticiye göre filtreli)
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                    $join->on('ppk.personel_id', '=', 'p.personel_id')
                        ->where('p.personel_kartkullanim', '1'); // aktif kart
                })
                ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('p.personel_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('p.personel_birim', $birim_id);
                })
                ->where('p.personel_durum', '1')
                ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'u.unvan_ad', 'd.durum_ad', 'ppk.kart_id')
                ->get();

            // Tatiller
            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();

            // İzinler (yöneticiye göre filtreli)
            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('i.izin_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('i.izin_birim', $birim_id);
                })
                ->where('i.izin_durum', '1')
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();

            // GEÇİŞLER
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->whereBetween(DB::raw('DATE(gecis_tarihi)'), [$start->toDateString(), $end->toDateString()])
                ->selectRaw('DATE(gecis_tarihi) as gecis_gun, kart_id')
                ->get()
                ->groupBy('gecis_gun')
                ->map(fn($group) => $group->pluck('kart_id')->unique()->toArray())
                ->toArray();

            // 🔹 Verileri oluştur
            $data = [];

            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';

                    if (!$personel->kart_id) {
                        $durum = 'TANIMSIZ';
                    } elseif (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    } elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    } else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id
                                && $gun >= $i->izin_baslayis
                                && $gun <= $i->izin_bitis;
                        });

                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        } elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                            $durum = 'GELDİ';
                        } else {
                            $durum = 'GELMEDİ';
                        }
                    }

                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun
                    ];
                }
            }

            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.pdks.hareket', compact('title', 'pagetitle'));
    }
    public function Hareket()
    {
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;
    
        $title = 'Tüm Hareketler';
        $pagetitle = 'Tüm Hareketler';
    
        if (request()->ajax()) {
    
            $dateRange = request('date_range'); // Tarih aralığı
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                $start = Carbon::parse($dates[0]);
                $end = Carbon::parse($dates[1]);
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
    
            $end = $end->gt(Carbon::today()) ? Carbon::today() : $end;
    
            // Tüm günleri oluştur
            $allDates = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates->push($date->toDateString());
            }
    
            // 🔹 Personelleri çek
            $personeller = DB::table('personel as p')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->leftJoin('pdks_personel_kartlar as ppk', function ($join) {
                    $join->on('ppk.personel_id', '=', 'p.personel_id')
                        ->where('p.personel_kartkullanim', '1');
                })
                ->leftJoin('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('p.personel_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('p.personel_birim', $birim_id);
                })
                ->where('p.personel_durum', '1')
                ->select(
                    'p.personel_id',
                    'p.personel_adsoyad',
                    'b.birim_ad',
                    'u.unvan_ad',
                    'd.durum_ad',
                    'ppk.kart_id',
                    'pk.kart_eklemetarihi'
                )
                ->get();
    
            // Tatiller
            $tatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
    
            // İzinler
            $izinler = DB::table('izin as i')
                ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('i.izin_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('i.izin_birim', $birim_id);
                })
                ->where('i.izin_durum', '1')
                ->select('i.izin_personel', 'i.izin_baslayis', 'i.izin_bitis', 'it.izin_ad')
                ->get();
    
            // GEÇİŞLER
            $gecisler = DB::table('pdks_cihaz_gecisler')
                ->whereBetween(DB::raw('DATE(gecis_tarihi)'), [$start->toDateString(), $end->toDateString()])
                ->selectRaw('DATE(gecis_tarihi) as gecis_gun, kart_id')
                ->get()
                ->groupBy('gecis_gun')
                ->map(fn($group) => $group->pluck('kart_id')->unique()->toArray())
                ->toArray();
    
            // 🔹 Verileri oluştur
            $data = [];
            foreach ($personeller as $personel) {
                foreach ($allDates as $gun) {
                    $durum = '';
    
                    // Tarih ile kart ekleme tarihi karşılaştırması
                    $kartEklemeTarihi = $personel->kart_eklemetarihi ? Carbon::parse($personel->kart_eklemetarihi)->toDateString() : null;
    
                    if (!$personel->kart_id) {
                        $durum = 'TANIMSIZ';
                    } elseif ($kartEklemeTarihi && $gun < $kartEklemeTarihi) {
                        // Kart ekleme tarihinden önceki günler
                        $durum = 'KART YOK';
                    } elseif (in_array($gun, $tatiller)) {
                        $durum = 'RESMİ TATİL';
                    } elseif (Carbon::parse($gun)->isWeekend()) {
                        $durum = 'HAFTA SONU';
                    } else {
                        $izinli = $izinler->first(function ($i) use ($personel, $gun) {
                            return $i->izin_personel == $personel->personel_id && $gun >= $i->izin_baslayis && $gun <= $i->izin_bitis;
                        });
    
                        if ($izinli) {
                            $durum = 'İZİNLİ (' . $izinli->izin_ad . ')';
                        } elseif (isset($gecisler[$gun]) && in_array($personel->kart_id, $gecisler[$gun])) {
                            $durum = 'GELDİ';
                        } else {
                            $durum = 'GELMEDİ';
                        }
                    }
    
                    $data[] = [
                        'birim_ad' => $personel->birim_ad,
                        'durum_ad' => $durum,
                        'unvan_ad' => $personel->unvan_ad,
                        'personel_adsoyad' => $personel->personel_adsoyad,
                        'tarih' => $gun,
                    ];
                }
            }
    
            return DataTables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.pdks.hareket', compact('title', 'pagetitle'));
    }
    
    
}


