<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PDKS giriş/çıkış listesi satırı için API Resource.
 * İleride mobil veya harici rapor API'sinde kullanılabilir.
 */
class PdksGirisCikisRowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->resource;

        return [
            'personel_id' => $this->val($item, 'personel_id'),
            'personel_adsoyad' => $this->val($item, 'personel_adsoyad'),
            'birim_ad' => $this->val($item, 'birim_ad'),
            'durum_ad' => $this->val($item, 'durum_ad'),
            'unvan_ad' => $this->val($item, 'unvan_ad'),
            'tarih' => $this->val($item, 'tarih'),
            'giris' => $this->val($item, 'giris'),
            'cikis' => $this->val($item, 'cikis'),
            'izin_ad' => $this->val($item, 'izin_ad'),
            'izinmazeret_aciklama' => $this->val($item, 'izinmazeret_aciklama'),
        ];
    }

    private function val($item, string $key)
    {
        if (is_array($item)) {
            return $item[$key] ?? null;
        }
        return $item->{$key} ?? null;
    }
}
