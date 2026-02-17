<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fatura extends Model
{
    protected $table = "fatura"; //tablo adi değişirse
    protected $primaryKey = 'fatura_id';
    use HasFactory;
    protected $guarded = [];
}
