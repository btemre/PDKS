<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonelKartGecmisi extends Model
{
    protected $table = 'personel_kart_gecmisi';
    protected $fillable = [
        'personel_id',
        'kart_id',
        'baslangic_tarihi',
        'bitis_tarihi'
    ];

    public function personel()
    {
        return $this->belongsTo(Personel::class, 'personel_id', 'personel_id');
    }

    public function kart()
    {
        return $this->belongsTo(Kart::class, 'kart_id', 'kart_id');
    }
}
