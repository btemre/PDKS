<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cihaz extends Model
{
    protected $table = "pdks_cihazlar"; //tablo adi değişirse
    protected $primaryKey = 'cihaz_id';
    use HasFactory;
    protected $guarded = [];

    public function Cihaz(int $kurum_id)
    {
        $cihazlar = Cihaz::where('cihaz_durum', '1')
            ->where('cihaz_kurumid', $kurum_id)
            ->where('baglanti_durumu', '0')
            ->get();
        return $cihazlar;
    }

}
