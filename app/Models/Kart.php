<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kart extends Model
{
    protected $table = "pdks_kartlar"; //tablo adi değişirse
    protected $primaryKey = 'kart_id';
    use HasFactory;
    protected $guarded = [];
    public static function BugunGelmeyen(int $birim_id)
    {
        $today = Carbon::today()->toDateString();
    
        $query = DB::table('pdks_personel_kartlar as ppk')
            ->join('personel as p', 'p.personel_id', '=', 'ppk.personel_id')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('pdks_kartlar as pk', 'pk.kart_id', '=', 'ppk.kart_id')
            ->crossJoin(DB::raw("(SELECT DISTINCT DATE(gecis_tarihi) AS tarih FROM pdks_cihaz_gecisler WHERE DATE(gecis_tarihi) = '$today') AS tarih"))
            ->leftJoin('pdks_cihaz_gecisler as pcg', function($join) use ($today) {
                $join->on('pcg.kart_id', '=', 'pk.kart_id')
                     ->whereDate('pcg.gecis_tarihi', $today);
            })
            ->leftJoin('izin as i', function($join) use ($today) {
                $join->on('i.izin_personel', '=', 'ppk.personel_id')
                     ->where('i.izin_durum', '1')
                     ->whereDate('i.izin_baslayis', '<=', $today)
                     ->whereDate('i.izin_isebaslayis', '>', $today);
            })
            ->where('p.personel_durum', '1')
            ->where('p.personel_kartkullanim', '1')
            ->whereNull('pcg.gecis_tarihi') // hiç giriş yapmamış
            ->where(function($q) {
                $q->whereNull('i.izin_id'); // izinli değil
            });
    
        if ($birim_id) {
            $query->where('p.personel_birim', $birim_id);
        }
    
        // Liste verisi
        $liste = $query->select(
            'p.personel_id',
            'p.personel_adsoyad',
            'b.birim_ad',
            'u.unvan_ad',
            'd.durum_ad'
        )->get();
    
        // Grafik verisi: liste üzerinden hesaplanıyor
        $grafik = $liste->groupBy('durum_ad')->map(function ($rows) {
            return count($rows);
        });
    
        return [
            'liste'  => $liste,
            'grafik' => $grafik
        ];
    }
    
    public static function BugunGecGelen(int $birim_id)
    {
        $today = Carbon::today()->toDateString();
    
        $query = DB::table('pdks_personel_kartlar as ppk')
            ->join('personel as p', 'p.personel_id', '=', 'ppk.personel_id')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('mesai_saati as m', 'm.mesai_id', '=', 'p.personel_mesai')
            ->join('pdks_cihaz_gecisler as pcg', 'pcg.kart_id', '=', 'ppk.kart_id')
            ->whereDate('pcg.gecis_tarihi', $today)
            ->where('p.personel_durum', '1')
            ->where('p.personel_kartkullanim', '1');
    
        if ($birim_id) {
            $query->where('p.personel_birim', $birim_id);
        }
    
        $liste = $query->select(
                'p.personel_id',
                'p.personel_adsoyad',
                'b.birim_ad',
                'u.unvan_ad',
                'd.durum_ad',
                'm.mesai_giris',
                DB::raw('MIN(pcg.gecis_tarihi) as ilk_gecis')
            )
            ->groupBy(
                'p.personel_id',
                'p.personel_adsoyad',
                'b.birim_ad',
                'u.unvan_ad',
                'd.durum_ad',
                'm.mesai_giris'
            )
            ->havingRaw('MIN(pcg.gecis_tarihi) > m.mesai_giris')
            ->orderBy('ilk_gecis', 'asc')
            ->get();
    
        // Grafik verisi: liste üzerinden hesaplanıyor
        $grafik = $liste->groupBy('durum_ad')->map(function ($rows) {
            return count($rows);
        });
    
        return [
            'liste'  => $liste,
            'grafik' => $grafik
        ];
    }
    
    
    


}
