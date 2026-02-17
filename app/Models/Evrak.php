<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evrak extends Model
{
    protected $table = "evrak"; //tablo adi değişirse
    protected $primaryKey = 'evrak_id';
    use HasFactory;
    protected $guarded = [];
}
