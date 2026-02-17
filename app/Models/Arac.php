<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arac extends Model
{
    protected $table = "arac"; //tablo adi değişirse
    protected $primaryKey = 'arac_id';
    use HasFactory;
    protected $guarded = [];
}
