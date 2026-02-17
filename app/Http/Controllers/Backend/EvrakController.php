<?php

namespace App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evrak;

class EvrakController extends Controller
{
    public function Evrak()
    {
        if (!Auth::user()->hasPermissionTo('evrak.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Gelen/Giden Evrak İşlemleri';
        $pagetitle = 'Gelen/Giden Evrak Listesi';
        if (request()->ajax()) {
            $query = Evrak::select(
                '*'
            )
                ->where('evrak_durum', '1')
                ->where('evrak_kurumid', auth()->user()->kurum_id);

            // Eğer tarih aralığı seçilmişse filtre uygula
            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('evrak_tarihi', [$startDate, $endDate]);
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.evrak.evrak-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.evrak.evrak', compact(
            'title',
            'pagetitle'
        ));
    }
    public function getNextSira(Request $request)
    {
        $kurum_id = auth()->user()->kurum_id;
        $bolge_id = auth()->user()->bolge_id;
        $yil = now()->year;

        // Dikkat: Bu sadece gösterimliktir, garantili sıra değildir!
        $maxSira = Evrak::where('evrak_kurumid', $kurum_id)
            ->where('evrak_bolgeid', $bolge_id)
            ->where('evrak_durum', '1')
            ->whereYear('evrak_eklemetarih', $yil)
            ->max('evrak_sira');

        $nextSira = $maxSira ? $maxSira + 1 : 1;

        return response()->json(['sira' => $nextSira]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'evrak_tur' => 'required|string',
            'evrak_konu' => 'required|string',
            'evrak_birim' => 'required|string',
            'evrak_sayi' => 'required|integer',
            'evrak_tarihi' => 'required|date',
            'evrak_cikistarihi' => 'required|date',
            'evrak_aciklama' => 'nullable|string',
        ]);

        $kurum_id = auth()->user()->kurum_id;
        $bolge_id = auth()->user()->bolge_id;
        $yil = now()->year;

        // Atomik işlem başlat
        $evrak = DB::transaction(function () use ($validated, $request, $kurum_id, $bolge_id, $yil) {

            // Satır kilitleyerek max sıra al (çakışmayı önler)
            $maxSira = DB::table('evrak')
                ->where('evrak_kurumid', $kurum_id)
                ->where('evrak_bolgeid', $bolge_id)
                ->where('evrak_durum', '1')
                ->whereYear('evrak_eklemetarih', $yil)
                ->lockForUpdate()
                ->max('evrak_sira');

            $nextSira = $maxSira ? $maxSira + 1 : 1;

            $validated['evrak_sira'] = $nextSira;
            $validated['evrak_eklemetarih'] = now();
            $validated['evrak_ekleyenip'] = $request->ip();
            $validated['evrak_ekleyenpersonel'] = auth()->user()->id;
            $validated['evrak_kurumid'] = $kurum_id;
            $validated['evrak_bolgeid'] = $bolge_id;
            $validated['evrak_durum'] = '1';

            $isNew = !$request->has('evrak_id');

            return Evrak::updateOrCreate(
                ['evrak_id' => $request->evrak_id],
                $validated
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Evrak Kaydı Başarıyla ' . ($request->has('evrak_id') ? 'Güncellendi!' : 'Eklendi!'),
            'data' => $evrak
        ]);
    }
    public function delete(Request $request)
    {
        $evrak = Evrak::find($request->evrak_id);

        if (!$evrak) {
            return response()->json([
                'status' => 'error',
                'message' => 'Evrak bulunamadı!'
            ], 404);
        }
        $userId = Auth::id();
        $evrak->evrak_durum = '0';
        $evrak->evrak_silenpersonel = $userId;
        $evrak->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Evrak Başarılı Bir Şekilde Silindi!',
            'data' => $evrak
        ]);
    }
}
