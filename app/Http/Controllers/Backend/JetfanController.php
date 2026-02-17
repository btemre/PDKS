<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Jetfan;
use App\Models\JetfanTestTur;
use App\Models\Tunel;
use Auth;
use Illuminate\Http\Request;

class JetfanController extends Controller
{
    public function Jetfan()
    {
        if (!Auth::user()->hasPermissionTo('jetfan.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $title = 'Jetfan Listesi';
        $pagetitle = 'Jetfan';
        $tuneller = Tunel::where('tunel_durum', '1')->get();
        $testTurleri = JetfanTestTur::where('test_durum', '1')->get();

        if (request()->ajax()) {
            $query = Jetfan::with(['scadaTest', 'fizikselTest', 'tunel'])
                ->where('jetfan_kurum', auth()->user()->kurum_id);

            return DataTables()->of($query)
                ->addColumn('jetfan_tunel', function ($row) {
                    return $row->tunel ? $row->tunel->tunel_ad : '-';
                })
                ->addColumn('jetfan_scadatest', function ($row) {
                    return $row->scadaTest ? $row->scadaTest->test_tur : '-';
                })
                ->addColumn('jetfan_fizikseltest', function ($row) {
                    return $row->fizikselTest ? $row->fizikselTest->test_tur : '-';
                })
                ->addColumn('action', 'admin.backend.tunel.jetfan-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.tunel.jetfan', compact('title', 'pagetitle', 'tuneller', 'testTurleri'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jetfan_tunel' => 'required|exists:tunel,tunel_id',
            'jetfan_ad' => 'required|string',
            'jetfan_fizikseltest' => 'required|exists:tunel_jetfantesttur,test_id',
            'jetfan_scadatest' => 'required|exists:tunel_jetfantesttur,test_id',
            'jetfan_aciklama' => 'nullable|string',
            'jetfan_durum' => 'required|boolean',

        ]);


        $validated['jetfan_kurum'] = auth()->user()->kurum_id;
        $validated['jetfan_bolge'] = auth()->user()->bolge_id;
        //$validated['arac_durum'] = 1;

        $isNew = !$request->has('jetfan_id');

        $jetfan = Jetfan::updateOrCreate(
            ['jetfan_id' => $request->jetfan_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Jetfan Kaydı Başarıyla Eklendi!' : 'Jetfan Kaydı Başarıyla Güncellendi!',
            'data' => $jetfan
        ]);
    }
    public function edit(Request $request)
    {
        $jetfan = Jetfan::where('jetfan_id', $request->jetfan_id)->first();

        if (!$jetfan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jetfan bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jetfan Başarılı Bir Şekilde Güncelendi!',
            'data' => $jetfan
        ]);
    }
}
