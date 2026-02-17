<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ilce extends Model
{
    use HasFactory;

    protected $table = 'ilce';
    protected $primaryKey = 'ilce_id';
    public $timestamps = true;
    protected $fillable = ['ilce_ad', 'ilce_ilkodu', 'ilce_durum'];

    /**
     * İlçe -> İl ilişkisi (N:1)
     */
    public function il()
    {
        return $this->belongsTo(Il::class, 'ilce_ilkodu', 'il_plaka');
    }
}
