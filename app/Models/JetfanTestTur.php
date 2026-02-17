<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JetfanTestTur extends Model
{

    protected $table = "tunel_jetfantesttur"; //tablo adi değişirse
    protected $primaryKey = 'test_id';
    use HasFactory;
    protected $guarded = [];

    public function jetfanScada()
    {
        return $this->hasMany(Jetfan::class, 'jetfan_scadatest');
    }

    public function jetfanFiziksel()
    {
        return $this->hasMany(Jetfan::class, 'jetfan_fizikseltest');
    }
}
