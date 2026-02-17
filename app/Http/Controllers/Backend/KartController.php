<?php

namespace App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Personel;
use Illuminate\Http\Request;
//use DB;

class KartController extends Controller
{
    public function Karts()
    {
        if (!Auth::user()->hasPermissionTo('personel.kartlistesi')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $title = 'Kart Listesi';
        $pagetitle = 'Kart Listesi';
        // AJAX DataTables isteği kontrolü
        if (request()->ajax()) {
            $veriler = DB::table('personel')
                ->join('pdks_personel_kartlar', 'pdks_personel_kartlar.personel_id', '=', 'personel.personel_id')
                ->join('pdks_kartlar', 'pdks_kartlar.kart_id', '=', 'pdks_personel_kartlar.kart_id')
                ->join('pdks_cihaz_yetkiler', 'pdks_cihaz_yetkiler.kart_id', '=', 'pdks_kartlar.kart_id')
                ->join('pdks_cihazlar', 'pdks_cihazlar.cihaz_id', '=', 'pdks_cihaz_yetkiler.cihaz_id')
                ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
                ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
                ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
                //->where('personel.personel_birim', $birim_id)
                ->where('personel.personel_kurumid', $kurum_id)
                ->select(
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'personel.personel_birim',
                    'durum.durum_ad',
                    'birim.birim_ad',
                    'unvan.unvan_ad',
                    'pdks_kartlar.kart_id',
                    'pdks_kartlar.kart_numarasi',
                    'pdks_kartlar.yetkili',
                    DB::raw('GROUP_CONCAT(pdks_cihazlar.cihaz_id SEPARATOR ",") as cihaz_id'),
                    DB::raw('GROUP_CONCAT(pdks_cihazlar.cihaz_adi SEPARATOR ",") as cihaz_adi')
                )
                ->groupBy(
                    'pdks_kartlar.kart_id',
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'personel.personel_birim',
                    'durum.durum_ad',
                    'unvan.unvan_ad',
                    'birim.birim_ad',
                    'pdks_kartlar.kart_numarasi',
                    'pdks_kartlar.yetkili'
                )
                ->orderByDesc('pdks_kartlar.kart_id')
                ->latest('pdks_kartlar.kart_id')
                ->limit(10)
                ->get();
            return datatables()->of($veriler)
                ->addColumn('action', 'admin.backend.kart.kart-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        // Sayfa ilk yüklenince personel ve cihaz bilgileri için
        $personel = DB::table('personel')
            ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->select('personel.personel_id', 'personel.personel_adsoyad', 'birim.birim_ad', 'durum.durum_ad')
            //->where('personel.personel_birim', $birim_id)
            ->where('personel.personel_kurumid', $kurum_id)
            ->where('personel.personel_durum', '1')
            ->orderBy('personel.personel_adsoyad', 'asc')
            ->get();
        $cihaz2 = DB::table('pdks_cihazlar')
            ->where('cihaz_kurumid', $kurum_id)
            ->where('cihaz_durum', '1')
            ->orderBy('cihaz_adi', 'asc')
            ->get();
        return view('admin.backend.kart.kart', compact(
            'title',
            'pagetitle',
            'personel',
            'cihaz2'
        ));
    }
    public function Karst()
    {
        if (!Auth::user()->hasPermissionTo('personel.kartlistesi')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $kurum_id  = auth()->user()->kurum_id;
        $title     = 'Kart Listesi';
        $pagetitle = 'Kart Listesi';

        if (request()->ajax()) {

            // Cihazları kart bazında tek satıra indir (pre-aggregate)
            $cihazAgg = DB::table('pdks_cihaz_yetkiler as y')
                ->join('pdks_cihazlar as c', 'c.cihaz_id', '=', 'y.cihaz_id')
                ->select(
                    'y.kart_id',
                    DB::raw('GROUP_CONCAT(c.cihaz_id SEPARATOR ",")  as cihaz_id'),
                    DB::raw('GROUP_CONCAT(c.cihaz_adi SEPARATOR ",") as cihaz_adi')
                )
                ->groupBy('y.kart_id');

            // Ana sorgu
            $query = DB::table('pdks_kartlar as k')
                ->join('pdks_personel_kartlar as pk', 'pk.kart_id', '=', 'k.kart_id')
                ->join('personel as p', 'p.personel_id', '=', 'pk.personel_id')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->leftJoinSub($cihazAgg, 'agg', function ($join) {
                    $join->on('agg.kart_id', '=', 'k.kart_id');
                })
                ->where('p.personel_kurumid', $kurum_id)
                ->select([
                    'p.personel_adsoyad',
                    'p.personel_sicilno',
                    'p.personel_birim',
                    'd.durum_ad',
                    'b.birim_ad',
                    'u.unvan_ad',
                    'k.kart_id',
                    'k.kart_numarasi',
                    'k.yetkili',
                    DB::raw('COALESCE(agg.cihaz_id, "") as cihaz_id'),
                    DB::raw('COALESCE(agg.cihaz_adi, "") as cihaz_adi'),
                ])
                ->orderByDesc('k.kart_id'); // varsayılan sıralama

            return datatables()
                ->query($query)

                // Arama (isteğe bağlı optimize)
                ->filterColumn('personel_adsoyad', function($q, $keyword) {
                    $q->where('p.personel_adsoyad', 'like', "%{$keyword}%");
                })
                ->filterColumn('personel_sicilno', function($q, $keyword) {
                    $q->where('p.personel_sicilno', 'like', "%{$keyword}%");
                })

                // *** KRİTİK: DataTables'ın gönderebileceği kolon adlarını agg.*'a map et ***
                // Eğer front-end'de column->name "pdks_cihazlar.cihaz_adi" ise:
                ->orderColumn('pdks_cihazlar.cihaz_adi', 'agg.cihaz_adi $1')
                ->orderColumn('pdks_cihazlar.cihaz_id',  'agg.cihaz_id $1')
                // Eğer front-end'de sadece "cihaz_adi"/"cihaz_id" ise:
                ->orderColumn('cihaz_adi', 'agg.cihaz_adi $1')
                ->orderColumn('cihaz_id',  'agg.cihaz_id $1')

                ->addColumn('action', 'admin.backend.kart.kart-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        // İlk yükleme: mevcut yapıyı koruyorum
        $personel = DB::table('personel as p')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'd.durum_ad')
            ->where('p.personel_kurumid', $kurum_id)
            ->where('p.personel_durum', '1')
            ->orderBy('p.personel_adsoyad', 'asc')
            ->get();

        $cihaz2 = DB::table('pdks_cihazlar')
            ->where('cihaz_kurumid', $kurum_id)
            ->where('cihaz_durum', '1')
            ->orderBy('cihaz_adi', 'asc')
            ->get();

        return view('admin.backend.kart.kart', compact('title', 'pagetitle', 'personel', 'cihaz2'));
    }
    public function Kart2()
    {
        if (!Auth::user()->hasPermissionTo('personel.kartlistesi')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $kurum_id  = auth()->user()->kurum_id;
        $title     = 'Kart Listesi';
        $pagetitle = 'Kart Listesi';

        if (request()->ajax()) {

            // Cihazları kart bazında tek satıra indir (pre-aggregate)
            $cihazAgg = DB::table('pdks_cihaz_yetkiler as y')
                ->join('pdks_cihazlar as c', 'c.cihaz_id', '=', 'y.cihaz_id')
                ->select(
                    'y.kart_id',
                    DB::raw('GROUP_CONCAT(c.cihaz_id SEPARATOR ",")  as cihaz_id'),
                    DB::raw('GROUP_CONCAT(c.cihaz_adi SEPARATOR ",") as cihaz_adi')
                )
                ->groupBy('y.kart_id');

            // Ana sorgu
            $query = DB::table('pdks_kartlar as k')
                ->join('pdks_personel_kartlar as pk', 'pk.kart_id', '=', 'k.kart_id')
                ->join('personel as p', 'p.personel_id', '=', 'pk.personel_id')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->leftJoinSub($cihazAgg, 'agg', function ($join) {
                    $join->on('agg.kart_id', '=', 'k.kart_id');
                })
                ->where('p.personel_kurumid', $kurum_id)
                ->select([
                    'p.personel_adsoyad',
                    'p.personel_sicilno',
                    'p.personel_birim',
                    'd.durum_ad',
                    'b.birim_ad',
                    'u.unvan_ad',
                    'k.kart_id',
                    'k.kart_numarasi',
                    'k.yetkili',
                    DB::raw('COALESCE(agg.cihaz_id, "") as cihaz_id'),
                    DB::raw('COALESCE(agg.cihaz_adi, "") as cihaz_adi'),
                ])
                ->orderByDesc('k.kart_id'); // varsayılan sıralama

            // DataTables server-side
            return datatables()
                ->query($query)

                // GLOBAL SEARCH (arama kutusu)
                ->filter(function($q) {
                    $search = request()->input('search.value');
                    if (!empty($search)) {
                        $search = trim($search);
                        $q->where(function($qq) use ($search) {
                            $qq->where('p.personel_adsoyad', 'like', "%{$search}%")
                            ->orWhere('p.personel_sicilno', 'like', "%{$search}%")
                            ->orWhere('b.birim_ad', 'like', "%{$search}%")
                            ->orWhere('u.unvan_ad', 'like', "%{$search}%")
                            ->orWhere('d.durum_ad', 'like', "%{$search}%")
                            ->orWhere('k.kart_numarasi', 'like', "%{$search}%")
                            ->orWhere('k.kart_id', 'like', "%{$search}%")
                            ->orWhere('k.yetkili', 'like', "%{$search}%")
                            // GROUP_CONCAT alanlarında arama:
                            ->orWhereRaw('agg.cihaz_adi LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('agg.cihaz_id  LIKE ?', ["%{$search}%"]);
                        });
                    }

                    // KOLON BAZLI SEARCH (columns[i][search][value])
                    $columns = request()->input('columns', []);
                    foreach ($columns as $col) {
                        $val  = $col['search']['value'] ?? '';
                        if ($val === '') continue;

                        // DataTables bazen "data", bazen "name" gönderir
                        $name = $col['name'] ?? $col['data'] ?? '';

                        switch ($name) {
                            case 'personel_adsoyad':
                                $q->where('p.personel_adsoyad', 'like', "%{$val}%"); break;
                            case 'personel_sicilno':
                                $q->where('p.personel_sicilno', 'like', "%{$val}%"); break;
                            case 'birim_ad':
                                $q->where('b.birim_ad', 'like', "%{$val}%"); break;
                            case 'unvan_ad':
                                $q->where('u.unvan_ad', 'like', "%{$val}%"); break;
                            case 'durum_ad':
                                $q->where('d.durum_ad', 'like', "%{$val}%"); break;
                            case 'kart_numarasi':
                                $q->where('k.kart_numarasi', 'like', "%{$val}%"); break;
                            case 'yetkili':
                                $q->where('k.yetkili', 'like', "%{$val}%"); break;
                            case 'cihaz_adi':
                            case 'pdks_cihazlar.cihaz_adi': // frontend böyle gönderebilir
                            case 'agg.cihaz_adi':
                                $q->whereRaw('agg.cihaz_adi LIKE ?', ["%{$val}%"]); break;
                            case 'cihaz_id':
                            case 'pdks_cihazlar.cihaz_id':
                            case 'agg.cihaz_id':
                                $q->whereRaw('agg.cihaz_id LIKE ?', ["%{$val}%"]); break;
                            default:
                                // tanınmayan kolon adı gelirse es geç
                                break;
                        }
                    }
                })

                // Sıralama alias fix (önceki hatayı önler)
                ->orderColumn('pdks_cihazlar.cihaz_adi', 'agg.cihaz_adi $1')
                ->orderColumn('pdks_cihazlar.cihaz_id',  'agg.cihaz_id $1')
                ->orderColumn('cihaz_adi', 'agg.cihaz_adi $1')
                ->orderColumn('cihaz_id',  'agg.cihaz_id $1')

                ->addColumn('action', 'admin.backend.kart.kart-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        // İlk yükleme (değişmedi)
        $personel = DB::table('personel as p')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'd.durum_ad')
            ->where('p.personel_kurumid', $kurum_id)
            ->where('p.personel_durum', '1')
            ->orderBy('p.personel_adsoyad', 'asc')
            ->get();

        $cihaz2 = DB::table('pdks_cihazlar')
            ->where('cihaz_kurumid', $kurum_id)
            ->where('cihaz_durum', '1')
            ->orderBy('cihaz_adi', 'asc')
            ->get();

        return view('admin.backend.kart.kart', compact('title', 'pagetitle', 'personel', 'cihaz2'));
    }
    public function Kart()
    {
        if (!Auth::user()->hasPermissionTo('personel.kartlistesi')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        // Kullanıcı bilgilerini tek seferde al
        $user = auth()->user();
        $kurum_id  = $user->kurum_id;
        $birim_id  = $user->birim_id;
        $bolge_id  = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        $title     = 'Kart Listesi';
        $pagetitle = 'Kart Listesi';

        if (request()->ajax()) {

            // Cihazları kart bazında tek satıra indir (pre-aggregate)
            $cihazAgg = DB::table('pdks_cihaz_yetkiler as y')
                ->join('pdks_cihazlar as c', 'c.cihaz_id', '=', 'y.cihaz_id')
                ->select(
                    'y.kart_id',
                    DB::raw('GROUP_CONCAT(c.cihaz_id SEPARATOR ",")  as cihaz_id'),
                    DB::raw('GROUP_CONCAT(c.cihaz_adi SEPARATOR ",") as cihaz_adi')
                )
                ->groupBy('y.kart_id');

            // Ana sorgu
            $query = DB::table('pdks_kartlar as k')
                ->join('pdks_personel_kartlar as pk', 'pk.kart_id', '=', 'k.kart_id')
                ->join('personel as p', 'p.personel_id', '=', 'pk.personel_id')
                ->join('unvan as u', 'u.unvan_id', '=', 'p.personel_unvan')
                ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
                ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
                ->leftJoinSub($cihazAgg, 'agg', function ($join) {
                    $join->on('agg.kart_id', '=', 'k.kart_id');
                })
                ->where('p.personel_kurumid', $kurum_id)
                ->select([
                    'p.personel_adsoyad',
                    'p.personel_sicilno',
                    'p.personel_birim',
                    'd.durum_ad',
                    'b.birim_ad',
                    'u.unvan_ad',
                    'k.kart_id',
                    'k.kart_numarasi',
                    'k.yetkili',
                    DB::raw('COALESCE(agg.cihaz_id, "") as cihaz_id'),
                    DB::raw('COALESCE(agg.cihaz_adi, "") as cihaz_adi'),
                ])
                ->orderByDesc('k.kart_id');

            // 🧠 YÖNETİCİ / NORMAL FİLTRESİ
            if ($isYonetici) {
                $query->where('p.personel_bolge', $bolge_id);
            } else {
                $query->where('p.personel_birim', $birim_id);
            }

            // DataTables server-side işlemi
            return datatables()
                ->query($query)
                ->filter(function($q) {
                    $search = request()->input('search.value');
                    if (!empty($search)) {
                        $search = trim($search);
                        $q->where(function($qq) use ($search) {
                            $qq->where('p.personel_adsoyad', 'like', "%{$search}%")
                            ->orWhere('p.personel_sicilno', 'like', "%{$search}%")
                            ->orWhere('b.birim_ad', 'like', "%{$search}%")
                            ->orWhere('u.unvan_ad', 'like', "%{$search}%")
                            ->orWhere('d.durum_ad', 'like', "%{$search}%")
                            ->orWhere('k.kart_numarasi', 'like', "%{$search}%")
                            ->orWhere('k.kart_id', 'like', "%{$search}%")
                            ->orWhere('k.yetkili', 'like', "%{$search}%")
                            ->orWhereRaw('agg.cihaz_adi LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('agg.cihaz_id  LIKE ?', ["%{$search}%"]);
                        });
                    }

                    // Kolon bazlı filtreler
                    $columns = request()->input('columns', []);
                    foreach ($columns as $col) {
                        $val  = $col['search']['value'] ?? '';
                        if ($val === '') continue;
                        $name = $col['name'] ?? $col['data'] ?? '';
                        switch ($name) {
                            case 'personel_adsoyad': $q->where('p.personel_adsoyad', 'like', "%{$val}%"); break;
                            case 'personel_sicilno': $q->where('p.personel_sicilno', 'like', "%{$val}%"); break;
                            case 'birim_ad': $q->where('b.birim_ad', 'like', "%{$val}%"); break;
                            case 'unvan_ad': $q->where('u.unvan_ad', 'like', "%{$val}%"); break;
                            case 'durum_ad': $q->where('d.durum_ad', 'like', "%{$val}%"); break;
                            case 'kart_numarasi': $q->where('k.kart_numarasi', 'like', "%{$val}%"); break;
                            case 'yetkili': $q->where('k.yetkili', 'like', "%{$val}%"); break;
                            case 'cihaz_adi':
                            case 'pdks_cihazlar.cihaz_adi':
                            case 'agg.cihaz_adi':
                                $q->whereRaw('agg.cihaz_adi LIKE ?', ["%{$val}%"]); break;
                            case 'cihaz_id':
                            case 'pdks_cihazlar.cihaz_id':
                            case 'agg.cihaz_id':
                                $q->whereRaw('agg.cihaz_id LIKE ?', ["%{$val}%"]); break;
                        }
                    }
                })
                ->orderColumn('pdks_cihazlar.cihaz_adi', 'agg.cihaz_adi $1')
                ->orderColumn('pdks_cihazlar.cihaz_id',  'agg.cihaz_id $1')
                ->orderColumn('cihaz_adi', 'agg.cihaz_adi $1')
                ->orderColumn('cihaz_id',  'agg.cihaz_id $1')
                ->addColumn('action', 'admin.backend.kart.kart-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        // 🧱 İlk yükleme (view datası)
        $personel = DB::table('personel as p')
            ->join('durum as d', 'd.durum_id', '=', 'p.personel_durumid')
            ->join('birim as b', 'b.birim_id', '=', 'p.personel_birim')
            ->select('p.personel_id', 'p.personel_adsoyad', 'b.birim_ad', 'd.durum_ad')
            ->where('p.personel_kurumid', $kurum_id)
            ->where('p.personel_durum', '1')
            ->when($isYonetici, fn($q) => $q->where('p.personel_bolge', $bolge_id))
            ->when(!$isYonetici, fn($q) => $q->where('p.personel_birim', $birim_id))
            ->orderBy('p.personel_adsoyad', 'asc')
            ->get();

        $cihaz2 = DB::table('pdks_cihazlar')
            ->where('cihaz_kurumid', $kurum_id)
            ->where('cihaz_durum', '1')
            ->orderBy('cihaz_adi', 'asc')
            ->get();

        return view('admin.backend.kart.kart', compact('title', 'pagetitle', 'personel', 'cihaz2'));
    }
    public function update__(Request $request)
    {
        $validated = $request->validate([
            'kart_id' => 'required|integer|exists:pdks_kartlar,kart_id',
            'kart_numarasi' => 'required|string|max:255',
        ]);
    
        $kart_id = $validated['kart_id'];
        $yeni_kart_numarasi = $validated['kart_numarasi'];
        $islem = 3; // Güncelleme işlemi kodu
        $kart_guncelleyen = auth()->id();
        $kart_guncelleyenip = $request->ip();
    
        try {
            DB::beginTransaction();
    
            // 1️⃣ Kartı getir
            $kart = DB::table('pdks_kartlar')->where('kart_id', $kart_id)->first();
    
            if (!$kart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kart bulunamadı.',
                ], 404);
            }
    
            // 2️⃣ Aynı kart numarası sistemde başka kartta var mı kontrol et
            $varMi = DB::table('pdks_kartlar')
                ->where('kart_numarasi', $yeni_kart_numarasi)
                ->where('kart_id', '!=', $kart_id)
                ->exists();
    
            if ($varMi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bu kart numarası başka bir karta ait.',
                ], 422);
            }
    
            // 3️⃣ Güncelleme işlemi
            DB::table('pdks_kartlar')
                ->where('kart_id', $kart_id)
                ->update([
                    'kart_numarasi' => $yeni_kart_numarasi,
                    'kart_guncelleyenkullanici' => $kart_guncelleyen,
                    'kart_guncelleyenip' => $kart_guncelleyenip,
                    'updated_at' => now(),
                ]);
    
            // 4️⃣ Güncellenen karta ait cihazları bul ve sync log ekle
            $cihazlar = DB::table('pdks_cihaz_yetkiler')
                ->where('kart_id', $kart_id)
                ->pluck('cihaz_id')
                ->toArray();
    
            foreach ($cihazlar as $cihaz_id) {
                DB::table('pdks_cihaz_sync')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                    'islem' => $islem,
                    'created_at' => now(),
                ]);
    
                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_cihaz_id' => $cihaz_id,
                    'log_kart_id' => $kart_id,
                    'log_islem' => $islem,
                    'created_at' => now(),
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Kart numarası başarıyla güncellendi.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kart güncellenemedi. Hata: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'kart_id' => 'required|integer|exists:pdks_kartlar,kart_id',
            'kart_numarasi' => 'required|string|max:255',
        ]);

        $kart_id = $validated['kart_id'];
        $yeni_kart_numarasi = $validated['kart_numarasi'];
        $kart_guncelleyen = auth()->id();
        $kart_guncelleyenip = $request->ip();

        try {
            DB::beginTransaction();

            // 1️⃣ Kart bilgilerini al
            $kart = DB::table('pdks_kartlar')->where('kart_id', $kart_id)->first();
            if (!$kart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kart bulunamadı.',
                ], 404);
            }

            // 2️⃣ Aynı numara başka bir kartta var mı kontrol et
            $varMi = DB::table('pdks_kartlar')
                ->where('kart_numarasi', $yeni_kart_numarasi)
                ->where('kart_id', '!=', $kart_id)
                ->exists();

            if ($varMi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bu kart numarası başka bir karta ait.',
                ], 422);
            }

            // 3️⃣ Kart numarasını güncelle
            DB::table('pdks_kartlar')
                ->where('kart_id', $kart_id)
                ->update([
                    'kart_numarasi' => $yeni_kart_numarasi,
                    'kart_guncelleyenkullanici' => $kart_guncelleyen,
                    'kart_guncelleyenip' => $kart_guncelleyenip,
                    'updated_at' => now(),
                ]);

            // 4️⃣ Kartın bağlı olduğu cihazları bul
            $cihazlar = DB::table('pdks_cihaz_yetkiler')
                ->where('kart_id', $kart_id)
                ->pluck('cihaz_id')
                ->toArray();

            // 5️⃣ Her cihaz için önce sil (2), sonra ekle (1) işlemi ekle
            foreach ($cihazlar as $cihaz_id) {
                // Eski kartı cihazdan sil
                DB::table('pdks_cihaz_sync')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                    'islem' => 2, // Sil
                    'created_at' => now(),
                ]);

                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_cihaz_id' => $cihaz_id,
                    'log_kart_id' => $kart_id,
                    'log_islem' => 2,
                    'created_at' => now(),
                ]);

                // Yeni kartı cihaza ekle
                DB::table('pdks_cihaz_sync')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                    'islem' => 1, // Ekle
                    'created_at' => now(),
                ]);

                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_cihaz_id' => $cihaz_id,
                    'log_kart_id' => $kart_id,
                    'log_islem' => 1,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kart numarası başarıyla güncellendi ve cihazlara yeniden senkronlandı.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kart güncellenemedi. Hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store2(Request $request)
    {
        $request->validate([
            'personel_id' => 'required|integer',
            'cihaz_id' => 'required|array',
            'cihaz_id.*' => 'integer',
            'kart_numarasi' => 'required|string',
            'yetkili' => 'required|string',
            'kart_adi' => 'nullable|string',
        ]);

        $kart_numarasi = $request->kart_numarasi;
        $personel_id = $request->personel_id;
        $cihazlar = $request->cihaz_id;
        $yetkili = $request->yetkili;
        $kart_adi = $request->kart_adi ?? null;
        $kart_durum = 1;
        $islem = 1;

        $kart_kurumid = Auth::user()->kurum_id;
        $kart_birim = Auth::user()->birim_id;
        $kart_bolge = Auth::user()->bolge ?? null;
        $kart_ekleyenkullanici = Auth::id();
        $kart_ekleyenip = request()->ip();

        // Kart daha önce eklenmiş mi kontrol et
        $kartMevcut = DB::table('pdks_kartlar')
            ->where('kart_numarasi', $kart_numarasi)
            ->where('kart_durum', $kart_durum)
            ->exists();

        if ($kartMevcut) {
            return response()->json([
                'status' => 'error',
                'mesaj' => 'Girilen Kart Sistemde Mevcut, Lütfen Kontrol Edin!',
            ]);
        }

        try {
            DB::beginTransaction();

            // 1. Kart ekle
            $kart_id = DB::table('pdks_kartlar')->insertGetId([
                'kart_personelid' => $personel_id,
                'kart_numarasi' => $kart_numarasi,
                'yetkili' => $yetkili,
                'kart_adi' => $kart_adi,
                'kart_ekleyenkullanici' => $kart_ekleyenkullanici,
                'kart_ekleyenip' => $kart_ekleyenip,
                'kart_kurumid' => $kart_kurumid,
                'kart_birim' => $kart_birim,
                'kart_bolge' => $kart_bolge,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. pdks_personel_kartlar tablosuna ilişki ekle
            DB::table('pdks_personel_kartlar')->insert([
                'personel_id' => $personel_id,
                'kart_id' => $kart_id,
            ]);

            foreach ($cihazlar as $cihaz_id) {
                // 3. pdks_cihaz_yetkiler
                DB::table('pdks_cihaz_yetkiler')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                ]);

                // 4. pdks_cihaz_sync
                DB::table('pdks_cihaz_sync')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                    'islem' => $islem,
                ]);

                // 5. pdks_cihaz_sync_log
                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_cihaz_id' => $cihaz_id,
                    'log_kart_id' => $kart_id,
                    'log_islem' => $islem,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'mesaj' => 'Kart Ekleme İşlemi Başarılı!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'mesaj' => 'Kart Eklenemedi. Hata: ' . $e->getMessage(),
            ]);
        }
    }
    public function store_(Request $request)
    {
        // Validasyon
        $validated = $request->validate([
            'personel_id' => 'required|integer|exists:personel,personel_id',
            'cihaz_id' => 'required|array|min:1',
            'cihaz_id.*' => 'integer|exists:pdks_cihazlar,cihaz_id',
            'kart_numarasi' => 'required|string|max:255',
            'yetkili' => 'required|string|max:255',
            'kart_adi' => 'nullable|string|max:255',
        ]);

        // Sabit bilgiler
        $kart_durum = 1;
        $islem = 1;
        $kart_kurumid = auth()->user()->kurum_id;
        $kart_birim = auth()->user()->birim_id;
        $kart_bolge = auth()->user()->bolge_id ?? null;
        $kart_ekleyenkullanici = auth()->id();
        $kart_ekleyenip = $request->ip();

        // Aynı kart numarası sistemde mevcut mu kontrol et
        $kartMevcut = DB::table('pdks_kartlar')
            ->where('kart_numarasi', $validated['kart_numarasi'])
            ->where('kart_durum', $kart_durum)
            ->exists();

        if ($kartMevcut) {
            return response()->json([
                'status' => 'error',
                'message' => 'Girilen kart numarası sistemde mevcut. Lütfen kontrol ediniz.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Kartı ekle
            $kart_id = DB::table('pdks_kartlar')->insertGetId([
                'kart_personelid' => $validated['personel_id'],
                'kart_numarasi' => $validated['kart_numarasi'],
                'yetkili' => $validated['yetkili'],
                'kart_adi' => $validated['kart_adi'],
                'kart_ekleyenkullanici' => $kart_ekleyenkullanici,
                'kart_ekleyenip' => $kart_ekleyenip,
                'kart_kurumid' => $kart_kurumid,
                'kart_birim' => $kart_birim,
                'kart_bolge' => $kart_bolge,
                'kart_durum' => $kart_durum,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Kart ile personel ilişkisini kur
            DB::table('pdks_personel_kartlar')->insert([
                'personel_id' => $validated['personel_id'],
                'kart_id' => $kart_id,
            ]);

            foreach ($validated['cihaz_id'] as $cihaz_id) {
                // Cihaz yetkilerini ekle
                DB::table('pdks_cihaz_yetkiler')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                ]);

                // Sync ve log kayıtları
                DB::table('pdks_cihaz_sync')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                    'islem' => $islem,
                ]);

                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_cihaz_id' => $cihaz_id,
                    'log_kart_id' => $kart_id,
                    'log_islem' => $islem,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kart başarıyla eklendi.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Kart eklenemedi. Hata: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        // Validasyon
        $validated = $request->validate([
            'personel_id' => 'required|integer|exists:personel,personel_id',
            'cihaz_id' => 'required|array|min:1',
            'cihaz_id.*' => 'integer|exists:pdks_cihazlar,cihaz_id',
            'kart_numarasi' => 'required|string|max:255',
            'yetkili' => 'required|string|max:255',
            'kart_adi' => 'nullable|string|max:255',
        ]);

        // Sabit bilgiler
        $kart_durum = '1';
        $islem = '1';
        $kart_kurumid = auth()->user()->kurum_id;
        $kart_birim = auth()->user()->birim_id;
        $kart_bolge = auth()->user()->bolge_id ?? null;
        $kart_ekleyenkullanici = auth()->id();
        $kart_ekleyenip = $request->ip();

        try {
            DB::beginTransaction();

            // Aynı kart numarası sistemde var mı kontrol et
            $kart = DB::table('pdks_kartlar')
                ->where('kart_numarasi', $validated['kart_numarasi'])
                ->where('kart_durum', $kart_durum)
                ->first();

            if (!$kart) {
                // Kart sistemde yoksa yeni kart olarak ekle
                $kart_id = DB::table('pdks_kartlar')->insertGetId([
                    'kart_personelid' => $validated['personel_id'],
                    'kart_numarasi' => $validated['kart_numarasi'],
                    'yetkili' => $validated['yetkili'],
                    'kart_adi' => $validated['kart_adi'],
                    'kart_ekleyenkullanici' => $kart_ekleyenkullanici,
                    'kart_ekleyenip' => $kart_ekleyenip,
                    'kart_kurumid' => $kart_kurumid,
                    'kart_birim' => $kart_birim,
                    'kart_bolge' => $kart_bolge,
                    'kart_durum' => $kart_durum,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Personel-kart eşlemesi
                DB::table('pdks_personel_kartlar')->insert([
                    'personel_id' => $validated['personel_id'],
                    'kart_id' => $kart_id,
                ]);
            } else {
                $kart_id = $kart->kart_id;

                // Kart sistemde mevcut ancak personel ilişkisi yoksa ekle
                $personelKartVarMi = DB::table('pdks_personel_kartlar')
                    ->where('personel_id', $validated['personel_id'])
                    ->where('kart_id', $kart_id)
                    ->exists();

                if (!$personelKartVarMi) {
                    DB::table('pdks_personel_kartlar')->insert([
                        'personel_id' => $validated['personel_id'],
                        'kart_id' => $kart_id,
                    ]);
                }
            }

            // Cihazlara yetki ekleme
            $eklenenCihazSayisi = 0;
            foreach ($validated['cihaz_id'] as $cihaz_id) {
                // Aynı kart-cihaz çifti daha önce eklenmiş mi kontrol et
                $yetkiVarMi = DB::table('pdks_cihaz_yetkiler')
                    ->where('cihaz_id', $cihaz_id)
                    ->where('kart_id', $kart_id)
                    ->exists();

                if ($yetkiVarMi) {
                    continue; // Bu cihaz için zaten yetki verilmiş, atla
                }

                // Yeni cihaz yetkisi ve senkronizasyon kayıtları
                DB::table('pdks_cihaz_yetkiler')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                ]);

                DB::table('pdks_cihaz_sync')->insert([
                    'cihaz_id' => $cihaz_id,
                    'kart_id' => $kart_id,
                    'islem' => $islem,
                ]);

                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_cihaz_id' => $cihaz_id,
                    'log_kart_id' => $kart_id,
                    'log_islem' => $islem,
                ]);

                $eklenenCihazSayisi++;
            }

            if ($eklenenCihazSayisi === 0) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bu kartın seçilen cihazlara ait yetkileri zaten tanımlı.',
                ], 422);
            }

            DB::commit();
            // Personelin kart kullanım durumunu aktif et
            DB::table('personel')
            ->where('personel_id', $validated['personel_id'])
            ->update([
                'personel_kartkullanim' => '1',
                'updated_at' => now(),
            ]);

            //karteklerken de personel kartının başlangıç tarihini ayın başı olarak ayarlanow()->startOfMonth(),
            DB::table('personel_kart_gecmisi')->insert([
                'personel_id' => $validated['personel_id'],
                'kart_id' => $kart_id,
                'baslangic_tarihi' => now()->startOfMonth(),
                'bitis_tarihi' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kart ve cihaz yetkileri başarıyla eklendi.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kart eklenemedi. Hata: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'kart_id' => 'required|integer|exists:pdks_kartlar,kart_id',
        ]);
    
        $kart_id = $validated['kart_id'];
        $kart_silenkullanici = auth()->id();
        $kart_silenip = $request->ip();
        $kart_durum = '0';
        $islem = '2';
    
        try {
            DB::beginTransaction();
    
            // 1️⃣ Kart bilgilerini çek
            $kart = DB::table('pdks_kartlar')->where('kart_id', $kart_id)->first();
    
            if (!$kart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kart bulunamadı.',
                ], 404);
            }
    
            // 2️⃣ Kartın bağlı olduğu cihazları otomatik bul
            $cihaz_id_list = DB::table('pdks_cihaz_yetkiler')
                ->where('kart_id', $kart_id)
                ->pluck('cihaz_id')
                ->toArray();
    
            // 3️⃣ Kart durumunu pasif yap
            DB::table('pdks_kartlar')
                ->where('kart_id', $kart_id)
                ->update([
                    'kart_durum' => $kart_durum,
                    'kart_silenkullanici' => $kart_silenkullanici,
                    'kart_silenip' => $kart_silenip,
                    'updated_at' => now(),
                ]);
    
            // 4️⃣ İlişkili kayıtları sil
            DB::table('pdks_personel_kartlar')
                ->where('kart_id', $kart_id)
                ->delete();
    
            DB::table('pdks_cihaz_yetkiler')
                ->where('kart_id', $kart_id)
                ->delete();
    
            // 5️⃣ Cihaz senkronizasyonu ve log ekle
            foreach ($cihaz_id_list as $cihaz_id) {
                DB::table('pdks_cihaz_sync')->insert([
                    'kart_id' => $kart_id,
                    'cihaz_id' => $cihaz_id,
                    'islem' => $islem,
                    'created_at' => now(),
                ]);
    
                DB::table('pdks_cihaz_sync_log')->insert([
                    'log_kart_id' => $kart_id,
                    'log_cihaz_id' => $cihaz_id,
                    'log_islem' => $islem,
                    'created_at' => now(),
                ]);
            }
    
            // 6️⃣ Personel bilgisi varsa, kart kullanımını pasif et
            if ($kart->kart_personelid) {
                DB::table('personel')
                    ->where('personel_id', $kart->kart_personelid)
                    ->update([
                        'personel_kartkullanim' => '0',
                        'updated_at' => now(),
                    ]);
    
                DB::table('personel_kart_gecmisi')
                    ->where('personel_id', $kart->kart_personelid)
                    ->where('kart_id', $kart_id)
                    ->whereNull('bitis_tarihi')
                    ->update([
                        'bitis_tarihi' => now(),
                        'updated_at' => now(),
                    ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Kart başarıyla silindi.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Kart silinemedi. Hata: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
}
