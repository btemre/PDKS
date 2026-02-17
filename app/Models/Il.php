<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Il extends Model
{
    use HasFactory;

    protected $table = 'il';
    protected $primaryKey = 'il_id';
    public $timestamps = true; // created_at, updated_at var
    protected $fillable = ['il_ad', 'il_plaka', 'il_durum'];

    /**
     * İlin ilçeleri (1:N)
     */
    public function ilceler()
    {
        return $this->hasMany(Ilce::class, 'ilce_ilkodu', 'il_plaka');
    }
}
