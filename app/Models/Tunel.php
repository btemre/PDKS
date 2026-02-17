<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tunel extends Model
{
    protected $table = "tunel"; //tablo adi değişirse
    protected $primaryKey = 'tunel_id';
    use HasFactory;
    protected $guarded = [];
    public function scadaTest()
    {
        return $this->belongsTo(JetfanTestTur::class, 'jetfan_scadatest');
    }

    public function fizikselTest()
    {
        return $this->belongsTo(JetfanTestTur::class, 'jetfan_fizikseltest');
    }
    public function jetfans()
    {
        return $this->hasMany(Jetfan::class, 'jetfan_tunel', 'tunel_id');
    }
}
