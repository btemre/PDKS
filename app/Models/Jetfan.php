<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jetfan extends Model
{
    protected $table = "tunel_jetfan"; //tablo adi değişirse
    protected $primaryKey = 'jetfan_id';
    use HasFactory;
    protected $guarded = [];
    public function scadaTest()
    {
        return $this->belongsTo(JetfanTestTur::class, 'jetfan_scadatest', 'test_id');
    }
    public function fizikselTest()
    {
        return $this->belongsTo(JetfanTestTur::class, 'jetfan_fizikseltest', 'test_id');
    }
    public function tunel()
    {
        return $this->belongsTo(Tunel::class, 'jetfan_tunel', 'tunel_id');
    }
}
