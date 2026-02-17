<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Personel extends Model
{
    protected $table = "personel"; //tablo adi değişirse
    protected $primaryKey = 'personel_id';
    use HasFactory;
    protected $guarded = [];
   /* protected $fillable = [
        'personel_adsoyad',
        'personel_tc',
        'personel_sicilno',
        'personel_telefon',
        'personel_durumid',
        'personel_gorev',
        'personel_unvan',
        'personel_birim',
        'personel_dogumtarihi',
        'personel_isegiristarih',
        'personel_eposta',
        'personel_siralama',
        'personel_il',
        'personel_ilce',
        'personel_derece',
        'personel_kademe',
        'personel_sozlesmelimi',
        'personel_engellimi',
        'personel_mesai',
        'personel_kan',
        'personel_ogrenim',
        'personel_okul',
        'personel_adres',
        'personel_aciklama'
    ];*/
    public function unvan()
    {
        return $this->belongsTo(Unvan::class, 'personel_unvan', 'unvan_id');
    }

    public function durum()
    {
        return $this->belongsTo(Durum::class, 'personel_durumid', 'durum_id');
    }

    public function gorev()
    {
        return $this->belongsTo(Gorev::class, 'personel_gorev', 'gorev_id');
    }

    public function birim()
    {
        return $this->belongsTo(Birim::class, 'personel_birim', 'birim_id');
    }
    public function il()
    {
        return $this->belongsTo(Il::class, 'personel_il', 'il_plaka');
    }
    public function ilce()
    {
        return $this->belongsTo(Ilce::class, 'personel_ilce', 'ilce_id');
    }
    public function dosyalar()
    {
        return $this->hasMany(Dosya::class, 'dosya_personel', 'personel_id');
    }
    public function ogrenim()
    {
        return $this->belongsTo(Ogrenim::class, 'personel_ogrenim', 'ogrenim_id');
    }

    /**
     * PDKS listelerinde yetkiye göre filtre: yönetici ise bölge, değilse birim.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User|object  $user  bolge_id ve birim_id, yonetici (1/0) içermeli
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePdksYetki($query, $user)
    {
        if (!empty($user->yonetici) && (int) $user->yonetici === 1) {
            return $query->where('personel_bolge', $user->bolge_id);
        }
        return $query->where('personel_birim', $user->birim_id);
    }
}
