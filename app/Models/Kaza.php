<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kaza extends Model
{
    protected $table = "kaza"; //tablo adi değişirse
    protected $primaryKey = 'kaza_id';
    use HasFactory;
    protected $guarded = [];
    public function KazaOnceki(int $kurum_id)
    {
        $kazaYil = now()->subYear()->year . ' Yılı Trafik Kaza İstatistiği';
        $kazaGrafik = DB::table('kaza as k')
            ->join('ay as a', DB::raw('a.ay_id'), '=', DB::raw('month(k.kaza_tarih)'))
            ->selectRaw("
            SUM(k.kaza_sayisi) as kaza,
            SUM(k.kaza_vefat) as vefat,
            SUM(k.kaza_yarali) as yarali,
            SUM(k.kaza_carpisma) as carp,
            a.ay_ad,
            MONTH(k.kaza_tarih) as ay")
            ->where('k.kaza_durum', 1)
            ->where('k.kaza_kurumid', $kurum_id)
            ->whereYear('k.kaza_tarih', now()->subYear()->year)
            ->groupBy('ay', 'a.ay_ad')
            ->orderBy('ay')
            ->get();
        // Toplam kaza sayısı (yıllık)
        $toplamKaza = $kazaGrafik->sum('kaza');
        return [
            'yil' => $kazaYil,
            'toplamKaza' => $toplamKaza,
            'grafik' => $kazaGrafik
        ];
    }
    public function KazaBuYil(int $kurum_id)
    {
        $kazaYil2 = now()->year . ' Yılı Trafik Kaza İstatistiği';
        $kazaGrafik2 = DB::table('kaza as k')
            ->join('ay as a', DB::raw('a.ay_id'), '=', DB::raw('month(k.kaza_tarih)'))
            ->selectRaw("
            SUM(k.kaza_sayisi) as kaza,
            SUM(k.kaza_vefat) as vefat,
            SUM(k.kaza_yarali) as yarali,
            SUM(k.kaza_carpisma) as carp,
            a.ay_ad,
            MONTH(k.kaza_tarih) as ay")
            ->where('k.kaza_durum', 1)
            ->where('k.kaza_kurumid', $kurum_id)
            ->whereYear('k.kaza_tarih', now()->year)
            ->groupBy('ay', 'a.ay_ad')
            ->orderBy('ay')
            ->get();
        // Toplam kaza sayısı (yıllık)
        $toplamKaza2 = $kazaGrafik2->sum('kaza');
        return [
            'yil' => $kazaYil2,
            'toplamKaza' => $toplamKaza2,
            'grafik' => $kazaGrafik2
        ];
    }
    public function KazaOran(int $kurum_id)
    {
        $kazaOran = DB::table('kaza as k')
            ->select(
                'k.kaza_kkno as kkno',
                DB::raw('MAX(k.kaza_km) as km'),
                DB::raw('SUM(k.kaza_sayisi) as kaza'),
                DB::raw("
                ROUND(
                    SUM(k.kaza_sayisi) / (
                        SELECT SUM(k2.kaza_sayisi)
                        FROM kaza k2
                        WHERE k2.kaza_durum = '1'
                          AND k2.kaza_kurumid = {$kurum_id}
                          AND YEAR(k2.kaza_tarih) = YEAR(CURDATE())
                    ) * 100, 1
                ) as yuzde
            ")
            )
            ->where('k.kaza_durum', '1')
            ->whereYear('k.kaza_tarih', now()->year)
            ->where('k.kaza_kurumid', $kurum_id)
            ->groupBy('k.kaza_kkno')
            ->orderBy('k.kaza_kkno')
            ->get();
        return $kazaOran;
    }
    public function resimler()
    {
        return $this->hasMany(KazaResim::class, 'kaza_id', 'kaza_id');
    }
}
