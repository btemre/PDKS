<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class IzinMazeret extends Model

{
    protected $table = "izin_mazeret"; //tablo adi değişirse
    protected $primaryKey = 'izinmazeret_id';
    use HasFactory;
    protected $guarded = [];
}
