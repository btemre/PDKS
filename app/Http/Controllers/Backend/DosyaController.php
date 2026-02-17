<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dosya;
use App\Models\Personel;
use Illuminate\Support\Facades\Auth;

class DosyaController extends Controller
{
    public function store(Request $request, $personel_id)
    {
        // İlk olarak client-side validation
        $request->validate([
            'dosya' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480', // 20MB
        ]);
    
        $file = $request->file('dosya');
    
        // Dosya boyutunu taşımadan önce al
        $sizeKB = round($file->getSize() / 1024, 2);
    
        if (!$file->isValid()) {
            return back()->with('error', 'Dosya yüklenirken bir hata oluştu.');
        }
    
        $personel = Personel::findOrFail($personel_id);
    
        $path = 'upload/dosya/' . $personel->personel_kurumid . '/' . $personel->personel_birim . '/' . $personel->personel_sicilno;
    
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }
    
    // Dosya adındaki boşlukları alt çizgi ile değiştir
    $originalFileName = $file->getClientOriginalName();
    $cleanFileName = str_replace(' ', '_', $originalFileName);
    $fileName = time() . '_' . $cleanFileName;

    $file->move(public_path($path), $fileName);
    
        Dosya::create([
            'dosya_ad'      => $fileName,
            'dosya_personel'=> $personel->personel_id,
            'dosya_sicilno' => $personel->personel_sicilno,
            'dosya_yol'     => $path . '/' . $fileName,
            'dosya_aciklama'=> $request->input('dosya_aciklama', ''),
            'dosya_boyut'   => $sizeKB . ' KB',
            'dosya_tur'     => $file->getClientOriginalExtension(),
            'dosya_tarih'   => now(),
            'dosya_bolge'   => $personel->personel_bolge,
            'dosya_kurum'   => $personel->personel_kurumid,
            'dosya_birim'   => $personel->personel_birim,
            'dosya_kullanici' => Auth::id(),
        ]);
    
        return back()->with('success', 'Dosya başarıyla yüklendi.');
    }
    public function destroy($dosya_id)
    {
        $dosya = Dosya::findOrFail($dosya_id);

        // Dosya fiziksel olarak sil
        if (file_exists(public_path($dosya->dosya_yol))) {
            unlink(public_path($dosya->dosya_yol));
        }

        $dosya->delete();

        return redirect()->back()->with('success', 'Dosya başarıyla silindi.');
    }

}
