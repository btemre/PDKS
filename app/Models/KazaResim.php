<?php

// app/Models/KazaResim.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KazaResim extends Model
{
    use HasFactory;

    protected $table = 'kaza_resim';
    protected $primaryKey = 'resim_id';

    protected $fillable = [
        'kaza_id',
        'resim_yolu',
    ];

    // Bir resmin bir kazaya ait olduğunu belirten ilişki
    public function kaza()
    {
        return $this->belongsTo(Kaza::class, 'kaza_id', 'kaza_id');
    }
}