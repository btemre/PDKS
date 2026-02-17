<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BilgisayarEnvanter extends Model
{
    protected $table = "bilgisayar_envanter"; //tablo adi değişirse
    protected $primaryKey = 'id';
    use HasFactory;
    protected $guarded = [];
}