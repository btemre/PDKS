<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use App\Models\Jenerator;
use App\Models\Tunel;
use Auth;
use DB;
use Illuminate\Http\Request;

class JeneratorController extends Controller
{
    public function Jenerator3()
    {
        // Yetki kontrolü
        if (!Auth::user()->hasPermissionTo('jenerator.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
    
        $kurum_id = auth()->user()->kurum_id;
    
        // Sadece aktif binaları çek
        $bina = DB::table('bina')->where('bina_durum', '1')->get();
    
        $title = 'Jenerator Listesi';
        $pagetitle = 'Jenerator Listesi';
    
        if (request()->ajax()) {
            $query = Jenerator::with('haftalikKontroller') // Haftalık kontroller ilişkisini ekledik
                ->where('jenerator_kurumid', $kurum_id);
    
            return DataTables()->of($query)
                ->editColumn('jenerator_bina', function ($row) {
                    return $row->bina ? $row->bina->bina_adi : '-';
                })
                ->addColumn('jenerator_yakitseviyesi', function ($row) {
                    // Son haftalık kontrol
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    $fullCapacity = $row->jenerator_yakitseviyesi;
                    $currentLevel = $lastKontrol ? $lastKontrol->yakit_seviyesi : $fullCapacity;
                    $percent = $fullCapacity > 0 ? round(($currentLevel / $fullCapacity) * 100) : 0;
                    if ($percent >= 75) {
                        $color = 'success'; // yeşil
                    } elseif ($percent >= 50) {
                        $color = 'warning'; // turuncu
                    } else {
                        $color = 'danger'; // kırmızı
                    }
                    return '<span class="badge bg-' . $color . ' fs-6 fw-semibold p-2">' 
                    . $currentLevel . '/' . $fullCapacity . ' (' . $percent . '%)</span>';
                })
                ->addColumn('calisma_saati', function($row) {
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    return $lastKontrol ? $lastKontrol->calisma_saati : 0;
                })
                ->addColumn('son_kontrol_tarihi', function($row) {
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    return $lastKontrol ? $lastKontrol->kontrol_tarihi : 0;
                })
                ->addColumn('jenerator_hacim', function ($row) {
                    if ($row->jenerator_cap && $row->jenerator_uzunluk) {
                        $yaricap = $row->jenerator_cap / 2;
                        $hacim_m3 = pi() * pow($yaricap, 2) * $row->jenerator_uzunluk; // m³
                        $hacim_litre = $hacim_m3 * 1000; // litre
                        //return round($hacim_m3, 2) . ' m³ (' . round($hacim_litre) . ' L)';
                        return round($hacim_litre) . ' L';
                    }
                    return '-';
                })
                
                ->addColumn('action', 'admin.backend.tunel.jenerator-action')
                ->rawColumns(['action', 'jenerator_yakitseviyesi'])
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.tunel.jenerator', compact(
            'title',
            'pagetitle',
            'bina'
        ));
    }
    public function Jenerator2()
    {
        // Yetki kontrolü
        if (!Auth::user()->hasPermissionTo('jenerator.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $kurum_id = auth()->user()->kurum_id;

        // Sadece aktif binaları çek
        $bina = DB::table('bina')->where('bina_durum', '1')->get();

        $title = 'Jenerator Listesi';
        $pagetitle = 'Jenerator Listesi';

        if (request()->ajax()) {
            $query = Jenerator::with('haftalikKontroller') // Haftalık kontroller ilişkisini ekledik
                ->where('jenerator_kurumid', $kurum_id);

            return DataTables()->of($query)
                ->editColumn('jenerator_bina', function ($row) {
                    return $row->bina ? $row->bina->bina_adi : '-';
                })
                ->addColumn('jenerator_yakitseviyesi', function ($row) {
                    // Son haftalık kontrol
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    $fullCapacity = $row->jenerator_yakitseviyesi;
                    $currentLevel = $lastKontrol ? $lastKontrol->yakit_seviyesi : $fullCapacity;
                    $percent = $fullCapacity > 0 ? round(($currentLevel / $fullCapacity) * 100) : 0;
                    
                    if ($percent >= 75) {
                        $color = 'success'; // yeşil
                    } elseif ($percent >= 50) {
                        $color = 'warning'; // turuncu
                    } else {
                        $color = 'danger'; // kırmızı
                    }

                    return '<span class="badge bg-' . $color . ' fs-6 fw-semibold p-2">' 
                        . $currentLevel . '/' . $fullCapacity . ' (' . $percent . '%)</span>';
                })
                ->addColumn('calisma_saati', function($row) {
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    return $lastKontrol ? $lastKontrol->calisma_saati : 0;
                })
                ->addColumn('son_kontrol_tarihi', function($row) {
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    return $lastKontrol ? $lastKontrol->kontrol_tarihi : 0;
                })
                ->addColumn('jenerator_hacim', function ($row) {
                    // --- Hacim hesaplama kısmı ---
                    if (!$row->jenerator_tur) return '-';

                    $hacim_litre = null;

                    switch ($row->jenerator_tur) {
                        case 1: // Yatay silindir
                            if ($row->jenerator_cap && $row->jenerator_uzunluk) {
                                $yaricap = $row->jenerator_cap / 2;
                                // πr²h
                                $hacim_m3 = pi() * pow($yaricap, 2) * $row->jenerator_uzunluk;
                                $hacim_litre = $hacim_m3 * 1000;
                            }
                            break;

                        case 2: // Dikey silindir
                            if ($row->jenerator_cap && $row->jenerator_yukseklik) {
                                $yaricap = $row->jenerator_cap / 2;
                                // πr²h
                                $hacim_m3 = pi() * pow($yaricap, 2) * $row->jenerator_yukseklik;
                                $hacim_litre = $hacim_m3 * 1000;
                            }
                            break;

                        case 3: // Dikdörtgen prizma
                            if ($row->jenerator_en && $row->jenerator_boy && $row->jenerator_yukseklik) {
                                // en * boy * yükseklik
                                $hacim_m3 = $row->jenerator_en * $row->jenerator_boy * $row->jenerator_yukseklik;
                                $hacim_litre = $hacim_m3 * 1000;
                            }
                            break;

                        default:
                            return '-';
                    }

                    return $hacim_litre ? round($hacim_litre) . ' L' : '-';
                })
                ->addColumn('action', 'admin.backend.tunel.jenerator-action')
                ->rawColumns(['action', 'jenerator_yakitseviyesi'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.tunel.jenerator', compact(
            'title',
            'pagetitle',
            'bina'
        ));
    }
    public function Jenerator()
    {
        // Yetki kontrolü
        if (!Auth::user()->hasPermissionTo('jenerator.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $kurum_id = auth()->user()->kurum_id;

        // Sadece aktif binaları çek
        $bina = DB::table('bina')->where('bina_durum', '1')->get();
        $toplamYakit = DB::table('jenerator_kontrol_hafta')
        ->where('durum', '1')
        ->where('created_at', '>=', now()->subWeek()) // Son 1 hafta
        ->sum('yakit_miktari');

        $title = 'Jenerator Listesi';
        $pagetitle = 'Jenerator Listesi';

        if (request()->ajax()) {
            $query = Jenerator::with('haftalikKontroller')
                ->where('jenerator_kurumid', $kurum_id);

            return DataTables()->of($query)
                ->editColumn('jenerator_bina', function ($row) {
                    return $row->bina ? $row->bina->bina_adi : '-';
                })
                ->addColumn('jenerator_yakitseviyesi', function ($row) {
                    // BU BÖLÜM DOĞRU ÇALIŞIYOR.
                    // Accessor'dan gelen $row->jenerator_yakitseviyesi (cm) değerini
                    // $lastKontrol->yakit_seviyesi (cm) ile karşılaştırıyor.
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    $fullCapacity = $row->jenerator_yakitseviyesi; // Accessor sayesinde 160 (cm) gelir
                    $currentLevel = $lastKontrol ? $lastKontrol->yakit_seviyesi : $fullCapacity; // Bu da cm
                    $percent = $fullCapacity > 0 ? round(($currentLevel / $fullCapacity) * 100) : 0;
                    
                    if ($percent >= 75) {
                        $color = 'success'; // yeşil
                    } elseif ($percent >= 50) {
                        $color = 'warning'; // turuncu
                    } else {
                        $color = 'danger'; // kırmızı
                    }

                    return '<span class="badge bg-' . $color . ' fs-6 fw-semibold p-2">' 
                        . $currentLevel . 'cm / ' . $fullCapacity . 'cm (' . $percent . '%)</span>';
                })
                ->addColumn('calisma_saati', function($row) {
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    return $lastKontrol ? $lastKontrol->calisma_saati : 0;
                })
                ->addColumn('son_kontrol_tarihi', function($row) {
                    $lastKontrol = $row->haftalikKontroller()->latest('kontrol_tarihi')->first();
                    return $lastKontrol ? $lastKontrol->kontrol_tarihi : null; // 0 yerine null döndürmek daha iyi
                })
                ->addColumn('jenerator_hacim', function ($row) {
                    // --- Hacim hesaplama kısmı ---
                    if (!$row->jenerator_tur) return '-';

                    $hacim_litre = null;

                    // getRawOriginal() kullanarak Accessor'ları (cm'ye çevirmeyi) atlıyoruz
                    // ve doğrudan veritabanındaki metre (örn: 1.60) değerini alıyoruz.
                    $cap_m = $row->getRawOriginal('jenerator_cap');
                    $uzunluk_m = $row->getRawOriginal('jenerator_uzunluk');
                    $en_m = $row->getRawOriginal('jenerator_en');
                    $boy_m = $row->getRawOriginal('jenerator_boy');
                    $yukseklik_m = $row->getRawOriginal('jenerator_yukseklik');


                    switch ($row->jenerator_tur) {
                        case 1: // Yatay silindir
                            if ($cap_m && $uzunluk_m) {
                                $yaricap_m = $cap_m / 2;
                                // πr²h (metre küp cinsinden)
                                $hacim_m3 = pi() * pow($yaricap_m, 2) * $uzunluk_m;
                                $hacim_litre = $hacim_m3 * 1000;
                            }
                            break;

                        case 2: // Dikey silindir
                            // Dikey silindir de 'jenerator_uzunluk' sütununu kullanır.
                            if ($cap_m && $uzunluk_m) {
                                $yaricap_m = $cap_m / 2;
                                // πr²h (metre küp cinsinden)
                                $hacim_m3 = pi() * pow($yaricap_m, 2) * $uzunluk_m;
                                $hacim_litre = $hacim_m3 * 1000;
                            }
                            break;

                        case 3: // Dikdörtgen prizma
                            if ($en_m && $boy_m && $yukseklik_m) {
                                // en * boy * yükseklik (metre küp cinsinden)
                                $hacim_m3 = $en_m * $boy_m * $yukseklik_m;
                                $hacim_litre = $hacim_m3 * 1000;
                            }
                            break;

                        default:
                            return '-';
                    }

                    return $hacim_litre ? round($hacim_litre) . ' L' : '-';
                })
                ->addColumn('action', 'admin.backend.tunel.jenerator-action') // Bu dosyanın var olduğundan emin olun
                ->rawColumns(['action', 'jenerator_yakitseviyesi'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.tunel.jenerator', compact(
            'title',
            'pagetitle',
            'bina',
            'toplamYakit'
        ));
    }
    public function store2(Request $request)
    {
        $validated = $request->validate([
            'jenerator_bina'          => 'required|integer|exists:bina,bina_id',
            'jenerator_ad'            => 'required|string|max:100',
            'jenerator_marka'         => 'nullable|string|max:100',
            'jenerator_model'         => 'nullable|string|max:100',
            'jenerator_yil'           => 'required|digits:4|integer',
            'jenerator_kva'           => 'nullable|string|max:10',
            'jenerator_tck'           => 'nullable|string|max:100',
            'jenerator_yakitmiktari'  => 'nullable|string|max:50',
            'jenerator_yakitseviyesi' => 'nullable|string|max:50',
            'jenerator_cap' => 'nullable|numeric|min:0', // Validasyona numeric ve min:0 ekleyin
            'jenerator_uzunluk' => 'nullable|numeric|min:0', // Validasyona numeric ve min:0 ekleyin
            'jenerator_en' => 'nullable|numeric|min:0', // Validasyona numeric ve min:0 ekleyin
            'jenerator_boy' => 'nullable|numeric|min:0', // Validasyona numeric ve min:0 ekleyin
            'jenerator_yukseklik' => 'nullable|numeric|min:0', // Validasyona numeric ve min:0 ekleyin
            'jenerator_kod'           => 'nullable|string|max:100',
            'jenerator_durum'         => 'required|in:0,1',
            'jenerator_aciklama'      => 'nullable|string|max:500',
            'jenerator_akutarihi' => 'required|date',
            'jenerator_bakimtarihi' => 'required|date',
            'jenerator_tur' => 'required'
        ]);
        // Girilen verileri metre cinsine çevirme
        if (isset($validated['jenerator_cap'])) {
            $validated['jenerator_cap'] = $validated['jenerator_cap'] / 100;
        }

        if (isset($validated['jenerator_uzunluk'])) {
            $validated['jenerator_uzunluk'] = $validated['jenerator_uzunluk'] / 100;
        }

        $validated['jenerator_kurumid'] = auth()->user()->kurum_id;
        $isNew = !$request->has('jenerator_id');
        $jenerator = Jenerator::updateOrCreate(
            ['jenerator_id' => $request->jenerator_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Jenerator Kaydı Başarıyla Eklendi!' : 'Jenerator Kaydı Başarıyla Güncellendi!',
            'data' => $jenerator
        ]);
    }

    public function store(Request $request)
    {
        // 1. Gelişmiş Validasyon Kuralları
        $validated = $request->validate([
            'jenerator_bina'        => 'required|integer|exists:bina,bina_id',
            'jenerator_ad'          => 'required|string|max:100',
            'jenerator_marka'       => 'nullable|string|max:100',
            'jenerator_model'       => 'nullable|string|max:100',
            'jenerator_yil'         => 'nullable|digits:4|integer',
            'jenerator_kva'         => 'nullable|string|max:10',
            'jenerator_tck'         => 'nullable|string|max:100',
            'jenerator_yakitseviyesi' => 'required|numeric|min:0', // 'numeric' olmalı
            'jenerator_akutarihi'   => 'required|date',
            'jenerator_bakimtarihi' => 'required|date',
            'jenerator_tur'         => 'required|in:1,2,3',

            // --- Koşullu Validasyon ---
            // Tank tipi 1 (Yatay Silindir) veya 2 (Dikey Silindir) ise bu alanlar zorunludur.
            'jenerator_cap'         => 'required_if:jenerator_tur,1,2|nullable|numeric|min:0',
            'jenerator_uzunluk'     => 'required_if:jenerator_tur,1,2|nullable|numeric|min:0',
            
            // Tank tipi 3 (Dikdörtgen) ise bu alanlar zorunludur.
            'jenerator_en'          => 'required_if:jenerator_tur,3|nullable|numeric|min:0',
            'jenerator_boy'         => 'required_if:jenerator_tur,3|nullable|numeric|min:0',
            'jenerator_yukseklik'   => 'required_if:jenerator_tur,3|nullable|numeric|min:0',
            
            'jenerator_durum'       => 'required|in:0,1',
            'jenerator_aciklama'    => 'nullable|string|max:500',
        ]);

        // 2. Veritabanı İşlemi
        // Modeldeki Mutator'lar sayesinde CM -> M dönüşümü OTOMATİK olarak yapılacak.
        // Burada herhangi bir "/ 100" işlemi yapmıyoruz!
        
        $validated['jenerator_kurumid'] = auth()->user()->kurum_id;

        $jenerator = Jenerator::updateOrCreate(
            ['jenerator_id' => $request->jenerator_id], // Bu ID'ye göre bul veya yeni oluştur
            $validated // Bu verilerle işlem yap
        );

        // 3. Başarılı Yanıt
        $message = $request->jenerator_id ? 'Jeneratör Kaydı Başarıyla Güncellendi!' : 'Jeneratör Kaydı Başarıyla Eklendi!';

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }
    public function edit(Request $request)
    {
        $jenerator = Jenerator::where('jenerator_id', $request->jenerator_id)->first();

        if (!$jenerator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenerator bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jenerator Başarılı Bir Şekilde Güncelendi!',
            'data' => $jenerator
        ]);
    }
    
}
