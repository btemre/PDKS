<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gorev extends Model
{
    protected $table = "gorev"; //tablo adi değişirse
    protected $primaryKey = 'gorev_id';
    use HasFactory;
    protected $guarded = [];
}
