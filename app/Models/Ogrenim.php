<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ogrenim extends Model
{
    protected $table = "ogrenim"; //tablo adi değişirse
    protected $primaryKey = 'ogrenim_id';
    use HasFactory;
    protected $guarded = [];
}
