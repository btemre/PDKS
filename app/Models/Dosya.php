<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosya extends Model
{
    protected $table = "dosya"; //tablo adi değişirse
    protected $primaryKey = 'dosya_id';
    use HasFactory;
    protected $guarded = [];
    public function personel()
    {
        return $this->belongsTo(Personel::class, 'dosya_personel', 'personel_id');
    }
}
