<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * PDKS sayfalarında kullanılan ortak referans verilerini cache'ler.
 * Süre: 10 dakika (config ile değiştirilebilir).
 */
class PdksCacheService
{
    protected const TTL_MINUTES = 10;

    protected const CACHE_KEY_BIRIM = 'pdks_cache_birim';
    protected const CACHE_KEY_UNVAN = 'pdks_cache_unvan';
    protected const CACHE_KEY_GOREV = 'pdks_cache_gorev';
    protected const CACHE_KEY_DURUM = 'pdks_cache_durum';
    protected const CACHE_KEY_IZIN_TURleri = 'pdks_cache_izin_turleri';

    public function getBirim()
    {
        return Cache::remember(self::CACHE_KEY_BIRIM, self::TTL_MINUTES * 60, function () {
            return DB::table('birim')->where('birim_durum', '1')->get();
        });
    }

    public function getUnvan()
    {
        return Cache::remember(self::CACHE_KEY_UNVAN, self::TTL_MINUTES * 60, function () {
            return DB::table('unvan')->where('unvan_durum', '1')->get();
        });
    }

    public function getGorev()
    {
        return Cache::remember(self::CACHE_KEY_GOREV, self::TTL_MINUTES * 60, function () {
            return DB::table('gorev')->where('gorev_durum', '1')->get();
        });
    }

    public function getDurum()
    {
        return Cache::remember(self::CACHE_KEY_DURUM, self::TTL_MINUTES * 60, function () {
            return DB::table('durum')->where('durum_aktif', '1')->get();
        });
    }

    public function getIzinTurleri()
    {
        return Cache::remember(self::CACHE_KEY_IZIN_TURleri, self::TTL_MINUTES * 60, function () {
            return DB::table('izin_turleri')
                ->where('izin_durum', '1')
                ->where('izin_statu', 2)
                ->orderBy('izin_ad', 'asc')
                ->get();
        });
    }

    /**
     * Tüm ortak PDKS referans verilerini tek seferde döner (cache'den).
     */
    public function getOrtakData(): array
    {
        return [
            'birim' => $this->getBirim(),
            'unvan' => $this->getUnvan(),
            'gorev' => $this->getGorev(),
            'durum' => $this->getDurum(),
            'izintur' => $this->getIzinTurleri(),
        ];
    }

    /**
     * Cache'i temizler (referans verisi güncellendiğinde çağrılabilir).
     */
    public static function clear(): void
    {
        Cache::forget(self::CACHE_KEY_BIRIM);
        Cache::forget(self::CACHE_KEY_UNVAN);
        Cache::forget(self::CACHE_KEY_GOREV);
        Cache::forget(self::CACHE_KEY_DURUM);
        Cache::forget(self::CACHE_KEY_IZIN_TURleri);
    }
}
