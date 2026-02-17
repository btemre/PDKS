<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PdksGecisEkleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pdks.gecisekle');
    }

    public function rules(): array
    {
        return [
            'kart_id' => 'required|integer',
            'gecis_tarihi' => 'required|date',
            'cihaz_id' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'kart_id.required' => 'Personel/kart seçimi zorunludur.',
            'kart_id.integer' => 'Geçersiz kart seçimi.',
            'gecis_tarihi.required' => 'Geçiş tarihi zorunludur.',
            'gecis_tarihi.date' => 'Geçerli bir tarih/saat giriniz.',
        ];
    }

    /**
     * JSON response döndüğü için validation hatalarında 422 + JSON.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'durum' => 'error',
            'mesaj' => $validator->errors()->first(),
        ], 422));
    }
}
