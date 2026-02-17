<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jenerator extends Model
{
    protected $table = "jenerator"; //tablo adi değişirse
    protected $primaryKey = 'jenerator_id';
    use HasFactory;
    protected $guarded = [];
        // İlişki: jeneratör -> bina
    public function bina()
    {
        return $this->belongsTo(Bina::class, 'jenerator_bina', 'bina_id');
    }
        public function haftalikKontroller()
    {
        return $this->hasMany(JeneratorKontrolHafta::class, 'jenerator_id', 'jenerator_id');
    }
    // --- MUTATORS (Veriyi veritabanına YAZARKEN çalışır) ---
    // Controller'dan gelen 160 değerini, veritabanına yazmadan HEMEN ÖNCE 1.60'a çevirirler.

    /**
     * jenerator_cap değerini veritabanına kaydetmeden önce 100'e böl.
     */
    public function setJeneratorCapAttribute($value)
    {
        $this->attributes['jenerator_cap'] = $value ? $value / 100 : null;
    }

    /**
     * jenerator_uzunluk değerini veritabanına kaydetmeden önce 100'e böl.
     */
    public function setJeneratorUzunlukAttribute($value)
    {
        $this->attributes['jenerator_uzunluk'] = $value ? $value / 100 : null;
    }
    
    /**
     * jenerator_en değerini veritabanına kaydetmeden önce 100'e böl.
     */
    public function setJeneratorEnAttribute($value)
    {
        $this->attributes['jenerator_en'] = $value ? $value / 100 : null;
    }

    /**
     * jenerator_boy değerini veritabanına kaydetmeden önce 100'e böl.
     */
    public function setJeneratorBoyAttribute($value)
    {
        $this->attributes['jenerator_boy'] = $value ? $value / 100 : null;
    }

    /**
     * jenerator_yukseklik değerini veritabanına kaydetmeden önce 100'e böl.
     */
    public function setJeneratorYukseklikAttribute($value)
    {
        $this->attributes['jenerator_yukseklik'] = $value ? $value / 100 : null;
    }

    // --- ACCESSORS (Veriyi veritabanından OKURKEN çalışır) ---
    // Bunları da eklemeniz, edit (düzenleme) formunuzu açtığınızda
    // veritabanındaki 1.60 değerinin formda otomatik olarak 160 görünmesini sağlar.

    public function getJeneratorCapAttribute($value)
    {
        return $value ? $value * 100 : null;
    }

    public function getJeneratorUzunlukAttribute($value)
    {
        return $value ? $value * 100 : null;
    }

    public function getJeneratorEnAttribute($value)
    {
        return $value ? $value * 100 : null;
    }

    public function getJeneratorBoyAttribute($value)
    {
        return $value ? $value * 100 : null;
    }

    public function getJeneratorYukseklikAttribute($value)
    {
        return $value ? $value * 100 : null;
    }

}
