<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bina extends Model
{
    use HasFactory;

    protected $table = 'bina';
    protected $primaryKey = 'bina_id';
    protected $guarded = [];

    public function jenerators()
    {
        return $this->hasMany(Jenerator::class, 'jenerator_bina', 'bina_id');
    }
}
