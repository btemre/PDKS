<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JeneratorKontrolHafta extends Model
{
    protected $table = "jenerator_kontrol_hafta";
    protected $primaryKey = "kontrol_id";
    protected $guarded = [];

    public function jenerator()
    {
        return $this->belongsTo(Jenerator::class, 'jenerator_id', 'jenerator_id');
    }

    // Hesaplanmış alan: yakıt oranı
    public function getYakitOraniAttribute()
    {
        if (!$this->jenerator || !$this->yakit_seviyesi) {
            return null;
        }
        $fullSeviye = $this->jenerator->jenerator_yakitseviyesi; // örneğin %100 değer
        return round(($this->yakit_seviyesi / $fullSeviye) * 100, 2);
    }
}

