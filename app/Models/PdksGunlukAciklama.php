<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdksGunlukAciklama extends Model
{
    protected $table = 'pdks_gunluk_aciklama';

    protected $fillable = [
        'personel_id',
        'tarih',
        'tip',
        'aciklama',
        'ekleyen_user_id',
    ];

    protected $casts = [
        'tarih' => 'date',
    ];

    public const TIP_GEC_GELEN = 'gec_gelen';
    public const TIP_ERKEN_CIKAN = 'erken_cikan';

    public function personel(): BelongsTo
    {
        return $this->belongsTo(Personel::class, 'personel_id', 'personel_id');
    }

    /**
     * Belirli personel ve tarih için not getirir (tip: gec_gelen veya erken_cikan).
     */
    public static function notGetir(int $personelId, string $tarih, string $tip): ?string
    {
        $row = self::where('personel_id', $personelId)
            ->where('tarih', $tarih)
            ->where('tip', $tip)
            ->first();

        return $row?->aciklama;
    }
}
