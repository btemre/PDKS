<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Izin extends Model

{
    protected $table = "izin"; //tablo adi değişirse
    protected $primaryKey = 'izin_id';
    use HasFactory;
    protected $guarded = [];
    public function izinli(int $kurum_id, int $birim_id)
    {
        $izinli_baslik = 'İzinli / Raporlu Personeller';

        //$kurum_id = auth()->user()->kurum_id;   // veya session('kullanici_kurumid')
        //$birim_id = auth()->user()->birim_id;   // veya session('kullanici_birim')
        $izinli = DB::table('izin as i')
            ->join('personel as p', 'p.personel_id', '=', 'i.izin_personel')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
            ->selectRaw("
                DATEDIFF(i.izin_isebaslayis, i.izin_baslayis) as tarihfark,
                i.izin_baslayis,
                i.izin_bitis,
                i.izin_isebaslayis,
                i.izin_yil,
                p.personel_adsoyad,
                u.unvan_ad,
                d.durum_ad,
                it.izin_ad,
                i.izin_suresi,
                i.izin_kurumid
            ")
            ->where('i.izin_isebaslayis', '>', now())  // CURDATE() karşılığı
            ->where('i.izin_durum', '1')
            ->where('i.izin_kurumid', $kurum_id)
            ->where('p.personel_birim', $birim_id)
            ->orderBy('i.izin_isebaslayis', 'asc')
            ->get();

        return [
            'izinli_baslik' => $izinli_baslik,
            'izinli' => $izinli
        ];
    }
    public function izinli2(int $kurum_id, int $birim_id)
    {
         $izinli2 = DB::table('izin as i')
            ->join('personel as p', 'p.personel_id', '=', 'i.izin_personel')
            ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('izin_turleri as it', 'it.izin_turid', '=', 'i.izin_turid')
            ->selectRaw("
                DATEDIFF(i.izin_isebaslayis, i.izin_baslayis) as tarihfark,
                i.izin_baslayis,
                i.izin_bitis,
                i.izin_isebaslayis,
                i.izin_yil,
                p.personel_adsoyad,
                u.unvan_ad,
                d.durum_ad,
                it.izin_ad,
                i.izin_suresi,
                i.izin_kurumid
            ")
            ->whereDate('i.izin_baslayis', '<=', now())
            ->whereDate('i.izin_isebaslayis', '>', now())
            ->where('i.izin_durum', '1')
            ->where('i.izin_kurumid', $kurum_id)
            ->where('p.personel_birim', $birim_id)
            ->orderBy('i.izin_isebaslayis', 'asc')
            ->get();

        return [
            'izinli2' => $izinli2
        ];
    }
}
