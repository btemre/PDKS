<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Durum extends Model
{
    protected $table = "durum"; //tablo adi değişirse
    protected $primaryKey = 'durum_id';
    use HasFactory;
    protected $guarded = [];
}
