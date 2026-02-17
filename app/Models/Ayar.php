<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayar extends Model
{
    protected $table = "ayar"; //tablo adi değişirse
    protected $primaryKey = 'ayar_id';
    use HasFactory;
    protected $guarded = [];
}
