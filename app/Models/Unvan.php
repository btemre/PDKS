<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unvan extends Model
{
    protected $table = "unvan"; //tablo adi değişirse
    protected $primaryKey = 'unvan_id';
    use HasFactory;
    protected $guarded = [];
}
