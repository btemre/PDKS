<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Birim extends Model
{
    protected $table = "birim"; //tablo adi değişirse
    protected $primaryKey = 'birim_id';
    use HasFactory;
    protected $guarded = [];
}
