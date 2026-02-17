<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IzinMazeretEkleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pdks.izinekle');
    }

    public function rules(): array
    {
        return [
            'izinmazeret_personel' => 'required|integer',
            'izinmazeret_turid' => 'required|integer',
            'izinmazeret_baslayis' => 'required|date',
            'izinmazeret_baslayissaat' => 'required|date_format:H:i',
            'izinmazeret_bitissaat' => 'required|date_format:H:i|after:izinmazeret_baslayissaat',
            'izinmazeret_aciklama' => 'required|string|max:500',
            'izinmazeret_id' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'izinmazeret_personel.required' => 'Personel seçimi zorunludur.',
            'izinmazeret_turid.required' => 'İzin türü seçimi zorunludur.',
            'izinmazeret_baslayis.required' => 'Başlangıç tarihi zorunludur.',
            'izinmazeret_baslayissaat.required' => 'Başlangıç saati zorunludur.',
            'izinmazeret_bitissaat.required' => 'Bitiş saati zorunludur.',
            'izinmazeret_bitissaat.after' => 'Bitiş saati başlangıç saatinden sonra olmalıdır.',
            'izinmazeret_aciklama.required' => 'Açıklama zorunludur.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }
}
