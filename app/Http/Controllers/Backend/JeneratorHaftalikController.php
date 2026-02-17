<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\Jenerator;
use App\Models\JeneratorKontrolHafta;
use App\Models\Tunel;
use Auth;
use DB;
use Illuminate\Http\Request;

class JeneratorHaftalikController extends Controller
{
    public function JeneratorHaftalikKontrol($id)
    {
        if (!Auth::user()->hasPermissionTo('jenerator.kontrol')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $title = 'Jenerator Listesi';
        $pagetitle = 'Jenerator Listesi';
        $jenerator = Jenerator::findOrFail($id);
    
        if (request()->ajax()) {
            $query = JeneratorKontrolHafta::where('jenerator_id', $id)
                ->with('jenerator');
    
            return DataTables()->of($query)
                ->addColumn('action', function ($row) {
                    return view('admin.backend.tunel.jenerator-kontrol-action', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.tunel.jenerator-kontrol', compact('jenerator', 'title', 'pagetitle'));
    }
    

    public function JeneratorHaftalikKontrolKaydet(Request $request, $jenerator_id)
{
    $request->validate([
        'kontrol_tarihi' => 'required|date',
        'yakit_seviyesi' => 'required|integer|min:0',
        'yakit_miktari'  => 'nullable|integer|min:0',
        'calisma_saati'  => 'required|integer|min:0',
        'yag_seviyesi'   => 'nullable|integer|min:0|max:100',
        'yag_miktari'    => 'nullable|integer|min:0',
        'frekans'        => 'required|numeric|min:0|max:200',
        'durum'          => 'nullable|boolean',
    ]);

    // Jeneratör var mı?
    $jenerator = Jenerator::findOrFail($jenerator_id);

    JeneratorKontrolHafta::create([
        'jenerator_id'   => $jenerator->jenerator_id,
        'kontrol_tarihi' => $request->kontrol_tarihi,
        'yakit_seviyesi' => $request->yakit_seviyesi,
        'yakit_miktari'  => $request->yakit_miktari,
        'calisma_saati'  => $request->calisma_saati,
        'sarj_redresoru' => $request->boolean('sarj_redresoru'),
        'aku_durumu'     => $request->boolean('aku_durumu'),
        'su_durumu'      => $request->boolean('su_durumu'),
        'temizlik'       => $request->boolean('temizlik'),
        'pano_temizlik'  => $request->boolean('pano_temizlik'),
        'sizinti_kacak'  => $request->boolean('sizinti_kacak'),
        'radyator'       => $request->boolean('radyator'),
        'isitici'        => $request->boolean('isitici'),
        'lamba'          => $request->boolean('lamba'),
        'egzoz'          => $request->boolean('egzoz'),
        'hava_filtresi'  => $request->boolean('hava_filtresi'),
        'scada_kontrolu' => $request->boolean('scada_kontrolu'),
        'yag_seviyesi'   => $request->yag_seviyesi,
        'yag_miktari'    => $request->yag_miktari,
        'frekans'        => $request->frekans,
        'aciklama'       => $request->aciklama,
        'kontrol_eden'   => Auth::id(),
        'durum'          => $request->boolean('durum', true), // varsayılan 1
    ]);

    return response()->json(['success' => 'Haftalık kontrol başarıyla kaydedildi!']);
}



    
}
