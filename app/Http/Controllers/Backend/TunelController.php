<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Jetfan;
use App\Models\JetfanTestTur;
use App\Models\Tunel;
use Auth;
use DB;
use Illuminate\Http\Request;

class TunelController extends Controller
{
    public function Jetfan11()
    {
        if (!Auth::user()->hasPermissionTo('jetfan.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Jetfan Listesi';
        $pagetitle = 'Jetfan';
        if (request()->ajax()) {
            $query = Jetfan::select(
                '*'
            )
                //->where('ayar_durum', '1')
                ->where('jetfan_kurum', auth()->user()->kurum_id);

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.tunel.jetfan-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.backend.tunel.jetfan', compact(
            'title',
            'pagetitle'
        ));
    }
    public function Jetfan22()
    {
        if (!Auth::user()->hasPermissionTo('jetfan.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $title = 'Jetfan Listesi';
        $pagetitle = 'Jetfan';

        if (request()->ajax()) {
            $query = Jetfan::with(['scadaTest', 'fizikselTest'])
                ->where('jetfan_kurum', auth()->user()->kurum_id);

            return DataTables()->of($query)
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

        return view('admin.backend.tunel.jetfan', compact('title', 'pagetitle'));
    }
    public function Jetfan222()
    {
        if (!Auth::user()->hasPermissionTo('jetfan.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $fizikselDurumlar = Jetfan::with('fizikselTest')
            ->selectRaw('jetfan_fizikseltest, COUNT(*) as toplam')
            ->groupBy('jetfan_fizikseltest')
            ->pluck('toplam', 'jetfan_fizikseltest');

        $scadaDurumlar = Jetfan::with('scadaTest')
            ->selectRaw('jetfan_scadatest, COUNT(*) as toplam')
            ->groupBy('jetfan_scadatest')
            ->pluck('toplam', 'jetfan_scadatest');
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

        return view('admin.backend.tunel.jetfan', compact('title', 'pagetitle', 'tuneller', 'testTurleri', 'fizikselDurumlar', 'scadaDurumlar'));
    }
    public function Jetfan()
    {
        if (!Auth::user()->hasPermissionTo('jetfan.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        // Fiziksel: test_tur bazında adetler
        $fizikselDurumlar = DB::table('tunel_jetfan as j')
            ->leftJoin('tunel_jetfantesttur as tt', 'tt.test_id', '=', 'j.jetfan_fizikseltest')
            ->where('j.jetfan_kurum', auth()->user()->kurum_id)
            ->selectRaw('COALESCE(tt.test_tur, "Tanımsız") as label, COUNT(*) as value')
            ->groupBy('label')
            ->orderByDesc('value')
            ->get();

        // Scada: test_tur bazında adetler
        $scadaDurumlar = DB::table('tunel_jetfan as j')
            ->leftJoin('tunel_jetfantesttur as tt', 'tt.test_id', '=', 'j.jetfan_scadatest')
            ->where('j.jetfan_kurum', auth()->user()->kurum_id)
            ->selectRaw('COALESCE(tt.test_tur, "Tanımsız") as label, COUNT(*) as value')
            ->groupBy('label')
            ->orderByDesc('value')
            ->get();

        $title = 'Jetfan Listesi';
        $pagetitle = 'Jetfan';
        $tuneller = Tunel::where('tunel_durum', '1')->get();
        $testTurleri = JetfanTestTur::where('test_durum', '1')->get();
        // Jetfan durumu - Tunel bazlı aktif/pasif sayıları ve yüzdelik
        $durumlar = DB::table('tunel_jetfan as j')
            ->join('tunel as t', 't.tunel_id', '=', 'j.jetfan_tunel')
            ->leftJoin('tunel_bina as tb', 'tb.bina_id', '=', 'j.jetfan_bina')
            ->leftJoin('tunel_jetfantesttur as tt1', 'tt1.test_id', '=', 'j.jetfan_fizikseltest')
            ->leftJoin('tunel_jetfantesttur as tt2', 'tt2.test_id', '=', 'j.jetfan_scadatest')
            ->selectRaw("
        t.tunel_kod,
        t.tunel_ad,
        COUNT(CASE WHEN j.jetfan_durum = '1' THEN 1 END) AS aktif,
        COUNT(CASE WHEN j.jetfan_durum = '0' THEN 1 END) AS pasif,
        ROUND(100.0 * COUNT(CASE WHEN j.jetfan_durum = '1' THEN 1 END) / COUNT(*), 2) AS yuzde,
        COUNT(*) AS toplam
    ")
            ->groupBy('t.tunel_kod', 't.tunel_ad')
            ->orderBy('t.tunel_kod')
            ->get();

        // Alt toplam ve yüzdesi
        $toplamAktif = $durumlar->sum('aktif');
        $toplamPasif = $durumlar->sum('pasif');
        $toplamToplam = $durumlar->sum('toplam');
        $toplamYuzde = $toplamToplam > 0 ? round(($toplamAktif / $toplamToplam) * 100, 2) : 0;


        if (request()->ajax()) {
            $query = Jetfan::with(['scadaTest', 'fizikselTest', 'tunel'])
                ->where('jetfan_kurum', auth()->user()->kurum_id);

            return DataTables()->of($query)
                ->addColumn('jetfan_tunel', fn($row) => $row->tunel->tunel_ad ?? '-')
                ->addColumn('jetfan_scadatest', fn($row) => $row->scadaTest->test_tur ?? '-')
                ->addColumn('jetfan_fizikseltest', fn($row) => $row->fizikselTest->test_tur ?? '-')
                ->addColumn('action', 'admin.backend.tunel.jetfan-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.tunel.jetfan', compact(
            'title',
            'pagetitle',
            'tuneller',
            'testTurleri',
            'fizikselDurumlar',
            'scadaDurumlar',
            'durumlar',
            'toplamAktif',
            'toplamPasif',
            'toplamToplam',
            'toplamYuzde'
        ));
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
