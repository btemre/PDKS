<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RaporMail extends Model
{

    protected $table = "rapor_mail"; //tablo adi değişirse
    protected $primaryKey = 'id';
    use HasFactory;
    protected $guarded = [];
}
