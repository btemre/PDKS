<?php

namespace App\Http\Controllers\Backend;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kaza;
use Illuminate\Support\Facades\Auth;

use App\Models\KazaResim; // Yeni KazaResim modelini ekleyin

use Illuminate\Support\Facades\File; // Dosya işlemleri için
use Illuminate\Support\Str; // Benzersiz isimler için
class KazaController extends Controller
{
    public function Kaza()
    {
        if (!Auth::user()->hasPermissionTo('trafik.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $kkno = DB::table('kkno')->where('kkno_kurumid', $kurum_id)->where('kkno_durum', '1')->orderBy('kkno_ad', 'asc')->get();
        $araccins = DB::table('araccins')->where('araccins_durum', '1')->get();
        $title = 'Trafik Kazaları';
        $pagetitle = 'Trafik Kaza Listesi';

        if (request()->ajax()) {
            $veriler = DB::table('kaza as k')
                ->join('ay as a', DB::raw('MONTH(k.kaza_tarih)'), '=', 'a.ay_id')
                ->where('k.kaza_durum', '1')
                ->where('k.kaza_kurumid', $kurum_id)
                ->select(
                    DB::raw('(SELECT SUM(kaza_sayisi) FROM kaza WHERE kaza_durum = 1 AND kaza_kurumid = ' . $kurum_id . ' AND YEAR(kaza_tarih) = YEAR(CURDATE())) as toplam'),
                    DB::raw('YEAR(k.kaza_tarih) as yil'),
                    'a.ay_ad',
                    'a.ay_id',
                    DB::raw('MONTH(k.kaza_tarih) as ay'),
                    DB::raw('SUM(k.kaza_sayisi) as kaza'),
                    DB::raw('SUM(k.kaza_vefat) as vefat'),
                    DB::raw('SUM(k.kaza_yarali) as yarali'),
                    DB::raw('SUM(k.kaza_carpisma) as carp'),
                    DB::raw('SUM(k.kaza_devrilme) as devril'),
                    DB::raw('SUM(k.kaza_cismecarpma) as cism'),
                    DB::raw('SUM(k.kaza_duranaracacarpma) as duran'),
                    DB::raw('SUM(k.kaza_yayacarpma) as yaya'),
                    DB::raw('SUM(k.kaza_aractandusme) as aracdus'),
                    DB::raw('SUM(k.kaza_diger) as diger')
                )
                ->groupBy(DB::raw('YEAR(k.kaza_tarih), MONTH(k.kaza_tarih), a.ay_ad, a.ay_id'))
                ->orderBy(DB::raw('a.ay_id'), 'desc')
                ->get();
            return datatables()->of($veriler)
                ->addColumn('action', 'admin.backend.kaza.kaza-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.kaza.kaza', compact('title', 'pagetitle', 'kkno', 'araccins'));
    }
    public function KazaDetay($yil, $ay)
    {
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Aylık Trafik Kazaları';
        $pagetitle = "$yil Yılı $ay. Ay Trafik Kaza Listesi";
        $araccins = DB::table('araccins')->where('araccins_durum', '1')->get();
        $kkno = DB::table('kkno')->where('kkno_kurumid', $kurum_id)->where('kkno_durum', '1')->orderBy('kkno_ad', 'asc')->get();

        if (request()->ajax()) {
            return DataTables()->of(
                Kaza::where('kaza_durum', '1')
                    ->where('kaza_kurumid', $kurum_id)
                    ->whereYear('kaza_tarih', $yil)
                    ->whereMonth('kaza_tarih', $ay)
            )
                ->addColumn('action', function ($row) {
                    // kaza_tarih alanından yıl ve ay çekiliyor
                    $tarih = \Carbon\Carbon::parse($row->kaza_tarih);
                    $yil = $tarih->format('Y');
                    $ay = $tarih->format('m');

                    return view('admin.backend.kaza.kaza-detay-action', compact('row', 'yil', 'ay'))->render();
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.kaza.kaza-detay', compact('title', 'pagetitle', 'yil', 'ay' , 'araccins', 'kkno'));
    }
    public function KazaIstatistik()
    {
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Trafik Kaza İstatistiği';
        $pagetitle = "Trafik Kaza Listesi";

        if (request()->ajax()) {
            return DataTables()->of(
                Kaza::where('kaza_durum', '1')
                    ->where('kaza_kurumid', $kurum_id)
            )
                //->addColumn('action', 'admin.backend.kaza.kaza-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.kaza.kaza-istatistik', compact('title', 'pagetitle'));
    }
    public function store1(Request $request)
    {
        $validated = $request->validate([
            'kaza_tarih' => 'required|date',
            'kaza_saat' => 'required|string',
            'kaza_plaka' => 'nullable|string|max:100',
            'kaza_arac' => 'nullable|array',
            'kaza_arac.*' => 'string',
            'kaza_kkno' => 'required|string|max:50',
            'kaza_km' => 'required|string',
            'kaza_sayisi' => 'string|integer',
            'kaza_vefat' => 'string|integer',
            'kaza_yarali' => 'string|integer',
            'kaza_carpisma' => 'string|integer',
            'kaza_devrilme' => 'string|integer',
            'kaza_cismecarpma' => 'string|integer',
            'kaza_duranaracacarpma' => 'string|integer',
            'kaza_yayacarpma' => 'string|integer',
            'kaza_aractandusme' => 'string|integer',
            'kaza_diger' => 'string|integer',
            'kaza_maddihasar' => 'string|numeric',
            'kaza_yeri' => 'string|string',
            'kaza_istikamet' => 'string|string',
            'kaza_aciklama' => 'nullable|string'
        ]);

        // Plaka formatlaması
        if ($request->has('kaza_plaka') && !empty($request->kaza_plaka)) {
            $plakalar = preg_split('/\s+/', $request->kaza_plaka); // boşluklara göre ayır
            $duzenliPlakalar = array_map(function ($plaka) {
                return strtoupper(preg_replace('/\s+/', '', $plaka)); // tüm boşlukları kaldır ve büyüt
            }, $plakalar);
            $validated['kaza_plaka'] = implode('-', $duzenliPlakalar);
        }

        // Araç cinslerini '-' ile birleştir
        if ($request->has('kaza_arac') && is_array($request->kaza_arac)) {
            $validated['kaza_arac'] = implode('-', $request->kaza_arac);
        }

        $validated['kaza_ekleyenip'] = $request->ip();
        $validated['kaza_ekleyenkullanici'] = auth()->user()->id;
        $validated['kaza_kurumid'] = auth()->user()->kurum_id;
        $validated['kaza_bolgeid'] = auth()->user()->bolge_id;
        $validated['kaza_durum'] = 1;

        $isNew = !$request->has('kaza_id');

        $kaza = Kaza::updateOrCreate(
            ['kaza_id' => $request->kaza_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Kaza Kaydı Başarıyla Eklendi!' : 'Kaza Kaydı Başarıyla Güncellendi!',
            'data' => $kaza
        ]);
    }
   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kaza_tarih' => 'required|date',
            'kaza_saat' => 'required|string',
            'kaza_plaka' => 'nullable|string|max:100',
            'kaza_arac' => 'nullable|array',
            'kaza_arac.*' => 'string',
            'kaza_kkno' => 'required|string|max:50',
            'kaza_km' => 'required|string',
            'kaza_sayisi' => 'nullable|integer',
            'kaza_vefat' => 'nullable|integer',
            'kaza_yarali' => 'nullable|integer',
            'kaza_carpisma' => 'nullable|integer',
            'kaza_devrilme' => 'nullable|integer',
            'kaza_cismecarpma' => 'nullable|integer',
            'kaza_duranaracacarpma' => 'nullable|integer',
            'kaza_yayacarpma' => 'nullable|integer',
            'kaza_aractandusme' => 'nullable|integer',
            'kaza_diger' => 'nullable|integer',
            'kaza_maddihasar' => 'nullable|numeric',
            'kaza_yeri' => 'nullable|string',
            'kaza_istikamet' => 'nullable|string',
            'kaza_aciklama' => 'nullable|string',
            // --- YENİ RESİM DOĞRULAMA KURALLARI ---
            'kaza_resimleri' => 'nullable|array|max:10', // En fazla 10 resim
            'kaza_resimleri.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Her bir dosyanın resim ve max 2MB olması
        ]);

        // Plaka formatlaması
        if ($request->has('kaza_plaka') && !empty($request->kaza_plaka)) {
            $plakalar = preg_split('/\s+/', $request->kaza_plaka);
            $duzenliPlakalar = array_map(function ($plaka) {
                return strtoupper(preg_replace('/\s+/', '', $plaka));
            }, $plakalar);
            $validated['kaza_plaka'] = implode('-', $duzenliPlakalar);
        }

        // Araç cinslerini '-' ile birleştir
        if ($request->has('kaza_arac') && is_array($request->kaza_arac)) {
            $validated['kaza_arac'] = implode('-', $request->kaza_arac);
        }

        // kaza_resimleri'ni validated dizisinden çıkaralım, çünkü bu kazalar tablosunda bir sütun değil
        unset($validated['kaza_resimleri']);

        $validated['kaza_ekleyenip'] = $request->ip();
        $validated['kaza_ekleyenkullanici'] = auth()->user()->id;
        $validated['kaza_kurumid'] = auth()->user()->kurum_id;
        $validated['kaza_bolgeid'] = auth()->user()->bolge_id;
        $validated['kaza_durum'] = 1;

        $isNew = !$request->has('kaza_id') || empty($request->kaza_id);

        $kaza = Kaza::updateOrCreate(
            ['kaza_id' => $request->kaza_id],
            $validated
        );

        // --- YENİ RESİM YÜKLEME BÖLÜMÜ ---
        if ($request->hasFile('kaza_resimleri')) {
            // Hedef klasörü belirle
            $kurumId = $kaza->kaza_kurumid;
            $hedefKlasor = public_path('upload/kaza/' . $kurumId);

            // Klasör yoksa oluştur
            if (!File::isDirectory($hedefKlasor)) {
                File::makeDirectory($hedefKlasor, 0777, true, true);
            }

            foreach ($request->file('kaza_resimleri') as $resim) {
                // Benzersiz bir dosya adı oluştur
                $resimAdi = Str::uuid() . '.' . $resim->getClientOriginalExtension();
                
                // Resmi hedef klasöre taşı
                $resim->move($hedefKlasor, $resimAdi);

                // Veritabanına kaydet
                KazaResim::create([
                    'kaza_id' => $kaza->kaza_id,
                    'resim_yolu' => 'upload/kaza/' . $kurumId . '/' . $resimAdi,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Kaza Kaydı Başarıyla Eklendi!' : 'Kaza Kaydı Başarıyla Güncellendi!',
            'data' => $kaza
        ]);
    }
    public function show(Request $request)
    {
        // Kazayı, ilişkili 'resimler' ile birlikte bul
        // Modelinizdeki resimler ilişkisinin adının 'resimler' olduğundan emin olun
        $kaza = Kaza::with('resimler')->find($request->kaza_id);

        if (!$kaza) {
            return response()->json(['status' => 'error', 'message' => 'Kaza bulunamadı!'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kaza
        ]);
    }
    public function edit(Request $request)
    {
        $kaza = Kaza::where('kaza_id', $request->kaza_id)->first();

        if (!$kaza) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kaza bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kaza Başarılı Bir Şekilde Güncelendi!',
            'data' => $kaza
        ]);
    }
    public function delete(Request $request)
    {
        $kaza = Kaza::find($request->kaza_id);

        if (!$kaza) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kaza bulunamadı!'
            ], 404);
        }
        //$userId = Auth::id();
        $kaza->kaza_durum = '0';
        //$personel->personel_silen = $userId;
        $kaza->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Trafik Kazası Başarılı Bir Şekilde Silindi!',
            'data' => $kaza
        ]);
    }
    

}
