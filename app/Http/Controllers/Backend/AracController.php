<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Arac;
use Auth;
use Illuminate\Http\Request;

class AracController extends Controller
{
    public function Arac()
    {
        if (!Auth::user()->hasPermissionTo('arac.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Araç Listesi';
        $pagetitle = 'Araç Listesi';
        if (request()->ajax()) {
            $query = Arac::select(
                '*'
            )
                ->where('arac_durum', '1')
                ->where('arac_kurumid', auth()->user()->kurum_id);

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.arac.arac-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.backend.arac.arac', compact(
            'title',
            'pagetitle'
        ));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'arac_cins' => 'required|string',
            'arac_marka' => 'required|string',
            'arac_surucusu' => 'required|string',
            'arac_tck' => 'required|string',
            'arac_plaka' => 'required|string',
            'arac_ilkmuayene' => 'required|date',
            'arac_ilksigorta' => 'required|date',
            'arac_sase' => 'nullable|string',
            'arac_model' => 'required|string',
            'arac_kod' => 'nullable|string',
        ]);


        $validated['arac_kurumid'] = auth()->user()->kurum_id;
        $validated['arac_durum'] = '1';

        $isNew = !$request->has('arac_id');

        $arac = Arac::updateOrCreate(
            ['arac_id' => $request->arac_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Araç Kaydı Başarıyla Eklendi!' : 'Araç Kaydı Başarıyla Güncellendi!',
            'data' => $arac
        ]);
    }
    public function edit(Request $request)
    {
        $arac = Arac::where('arac_id', $request->arac_id)->first();

        if (!$arac) {
            return response()->json([
                'status' => 'error',
                'message' => 'Araç bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Araç Başarılı Bir Şekilde Güncelendi!',
            'data' => $arac
        ]);
    }
    public function delete(Request $request)
    {
        $arac = Arac::find($request->arac_id);

        if (!$arac) {
            return response()->json([
                'status' => 'error',
                'message' => 'Araç bulunamadı!'
            ], 404);
        }
        // Silme işlemi (durumu pasif yapıyoruz)
        $arac->arac_durum = '0';
        $arac->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Araç başarılı bir şekilde silindi!',
            'data' => $arac
        ]);
    }
}
