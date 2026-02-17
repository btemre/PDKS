<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * PDKS geçiş kayıtları için mükerrer önleme ve ekleme.
 * Manuel ekleme (PdksGecisEkle) ve cihaz sync/API aynı kuralı kullanmalı.
 */
class PdksGecisService
{
    /** Aynı kart için bu süre (saniye) içinde tekrar kayıt kabul edilmez */
    public const MUKERRER_SURE_SANIYE = 90;

    /**
     * Bu kart + (isteğe bağlı) cihaz için son MUKERRER_SURE_SANIYE içinde kayıt var mı?
     *
     * @param int         $kart_id
     * @param string      $gecis_tarihi  Y-m-d H:i:s veya datetime parse edilebilir
     * @param int|null    $cihaz_id      Null ise sadece kart_id + zaman penceresi kontrol edilir
     * @return bool true = mükerrer (kayıt eklenmemeli), false = eklenebilir
     */
    public static function isMukerrerGecis(int $kart_id, string $gecis_tarihi, ?int $cihaz_id = null): bool
    {
        $tarih = Carbon::parse($gecis_tarihi);
        $baslangic = $tarih->copy()->subSeconds(self::MUKERRER_SURE_SANIYE)->toDateTimeString();
        $bitis = $tarih->copy()->addSeconds(self::MUKERRER_SURE_SANIYE)->toDateTimeString();

        $query = DB::table('pdks_cihaz_gecisler')
            ->where('kart_id', $kart_id)
            ->whereBetween('gecis_tarihi', [$baslangic, $bitis]);

        if ($cihaz_id !== null) {
            $query->where('cihaz_id', $cihaz_id);
        }

        return $query->exists();
    }

    /**
     * Geçiş kaydı ekler; mükerrer ise eklemez.
     *
     * @param int         $kart_id
     * @param string      $gecis_tarihi
     * @param int|null    $cihaz_id      Manuel eklemede null veya 99 kullanılabilir
     * @return array ['inserted' => bool, 'skipped_duplicate' => bool]
     */
    public static function insertGecis(int $kart_id, string $gecis_tarihi, ?int $cihaz_id = null): array
    {
        if (self::isMukerrerGecis($kart_id, $gecis_tarihi, $cihaz_id)) {
            return ['inserted' => false, 'skipped_duplicate' => true];
        }

        DB::table('pdks_cihaz_gecisler')->insert([
            'kart_id' => $kart_id,
            'gecis_tarihi' => $gecis_tarihi,
            'cihaz_id' => $cihaz_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['inserted' => true, 'skipped_duplicate' => false];
    }
}
