<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
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
}