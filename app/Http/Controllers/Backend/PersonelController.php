<?php

namespace App\Http\Controllers\Backend;
use App\Models\Birim;
use App\Models\Durum;
use App\Models\Gorev;
use App\Models\PersonelKartGecmisi;
use App\Models\Unvan;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Personel;
use Datatables;
use Yajra\DataTables\EloquentDataTable;


class PersonelController extends Controller
{
    public function Personel_karstsiz()
    {
        $kurum_id = auth()->user()->kurum_id;
        $ogrenim = DB::table('ogrenim')->where('ogrenim_durum', '1')->get();
        $mesai = DB::table('mesai_saati')->where('mesai_durum', '1')->get();
        $il = DB::table('il')->get();
        $ilce = DB::table('ilce')->get();
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $ayrilis = DB::table('ayrilis')->where('ayrilis_durum', '1')->get();
        $title = 'Personel';
        $pagetitle = 'Personel Listesi';
        if (request()->ajax()) {
            return DataTables()->of(Personel::select(
                'personel.personel_id',
                'personel.personel_adsoyad',
                'personel.personel_sicilno',
                'durum.durum_ad',
                'unvan.unvan_ad',
                'birim.birim_ad',
                'gorev.gorev_ad',
                'ogrenim.ogrenim_ad',
            )
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                ->join('mesai_saati', 'personel.personel_mesai', '=', 'mesai_saati.mesai_id')
                ->join('ogrenim', 'personel.personel_ogrenim', '=', 'ogrenim.ogrenim_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('ayrilis', 'personel.personel_durum', '=', 'ayrilis.ayrilis_id')
                ->where('personel_durum', '1')
                ->where('personel_kurumid', $kurum_id))
                ->addColumn('action', 'admin.backend.personel.personel-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.backend.personel.personel', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'il',
            'ilce',
            'mesai',
            'ogrenim',
            'ayrilis'
        ));
    }
    public function Personel2()
    {
        if (!Auth::user()->hasPermissionTo('personel.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        // Tanımlar
        $ogrenim = DB::table('ogrenim')->where('ogrenim_durum', operator: '1')->get();
        $mesai = DB::table('mesai_saati')->where('mesai_durum', '1')->get();
        $il = DB::table('il')->get();
        $ilce = DB::table('ilce')->get();
        $birim = DB::table('birim')->where('birim_durum', '1')->where('birim_id',$birim_id)->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $ayrilis = DB::table('ayrilis')->where('ayrilis_durum', '1')->get();

        // Kartı olan personellerin ID listesi (tek sorgu, benzersiz ID)
        $kartliPersoneller = DB::table('pdks_kartlar')
            ->where('kart_durum', '1')
            ->distinct()
            ->pluck('kart_personelid')
            ->toArray();

        $title = 'Personel';
        $pagetitle = 'Personel Listesi';

        if (request()->ajax()) {
            return DataTables()->of(
                Personel::select(
                    'personel.personel_id',
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'durum.durum_ad',
                    'unvan.unvan_ad',
                    'birim.birim_ad',
                    'gorev.gorev_ad',
                    'personel.personel_kan',
                    'ogrenim.ogrenim_ad',
                )
                    ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                    ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                    ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                    ->join('mesai_saati', 'personel.personel_mesai', '=', 'mesai_saati.mesai_id')
                    ->join('ogrenim', 'personel.personel_ogrenim', '=', 'ogrenim.ogrenim_id')
                    ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                    ->join('ayrilis', 'personel.personel_durum', '=', 'ayrilis.ayrilis_id')
                    ->where('personel_durum', '1')
                    ->where('personel_birim', $birim_id)
            )
                ->addColumn('kart_durum', function ($row) use ($kartliPersoneller) {
                    return in_array($row->personel_id, $kartliPersoneller) ? 1 : 0;
                })
                ->addColumn('action', 'admin.backend.personel.personel-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.personel.personel', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'il',
            'ilce',
            'mesai',
            'ogrenim',
            'ayrilis'
        ));
    }
    public function Personel()
    {
        if (!Auth::user()->hasPermissionTo('personel.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        // 🔹 Tanımlar
        $ogrenim = DB::table('ogrenim')->where('ogrenim_durum', '1')->get();
        $mesai = DB::table('mesai_saati')->where('mesai_durum', '1')->get();
        $il = DB::table('il')->get();
        $ilce = DB::table('ilce')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $ayrilis = DB::table('ayrilis')->where('ayrilis_durum', '1')->get();

        // 🔹 Birim listesi (yöneticiye göre)
        $birim = DB::table('birim')
            ->where('birim_durum', '1')
            ->when($isYonetici, function ($query) use ($bolge_id) {
                $query->where('birim_durum', '1');
            }, function ($query) use ($birim_id) {
                $query->where('birim_id', $birim_id);
            })
            ->get();

        // 🔹 Kartlı personeller
        $kartliPersoneller = DB::table('pdks_kartlar')
            ->where('kart_durum', '1')
            ->distinct()
            ->pluck('kart_personelid')
            ->toArray();

        $title = 'Personel';
        $pagetitle = 'Personel Listesi';

        // 🔹 DataTable
        if (request()->ajax()) {
            $personeller = Personel::select(
                    'personel.personel_id',
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'durum.durum_ad',
                    'unvan.unvan_ad',
                    'birim.birim_ad',
                    'gorev.gorev_ad',
                    'personel.personel_kan',
                    //'ogrenim.ogrenim_ad',
                )
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                ->join('mesai_saati', 'personel.personel_mesai', '=', 'mesai_saati.mesai_id')
                //->join('ogrenim', 'personel.personel_ogrenim', '=', 'ogrenim.ogrenim_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('ayrilis', 'personel.personel_durum', '=', 'ayrilis.ayrilis_id')
                ->where('personel_durum', '1')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('personel.personel_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('personel.personel_birim', $birim_id);
                });

            return DataTables()->of($personeller)
                ->addColumn('kart_durum', function ($row) use ($kartliPersoneller) {
                    return in_array($row->personel_id, $kartliPersoneller) ? 1 : 0;
                })
                ->addColumn('action', 'admin.backend.personel.personel-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.personel.personel', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'il',
            'ilce',
            'mesai',
            'ogrenim',
            'ayrilis'
        ));
    }
    public function PersonelAyrilan2()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $ogrenim = DB::table('ogrenim')->where('ogrenim_durum', '1')->get();
        $mesai = DB::table('mesai_saati')->where('mesai_durum', '1')->get();
        $il = DB::table('il')->get();
        $ilce = DB::table('ilce')->get();
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $ayrilis = DB::table('ayrilis')->where('ayrilis_durum', '1')->get();
        $title = 'Ayrılış Yapan Personel';
        $pagetitle = 'Ayrılış Yapan Personel Listesi';
        if (request()->ajax()) {
            return DataTables()->of(Personel::select(
                'personel.personel_id',
                'personel.personel_adsoyad',
                'personel.personel_sicilno',
                'durum.durum_ad',
                'unvan.unvan_ad',
                'birim.birim_ad',
                'gorev.gorev_ad',
                'ogrenim.ogrenim_ad',
                'ayrilis.ayrilis_tur',
            )
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                ->join('mesai_saati', 'personel.personel_mesai', '=', 'mesai_saati.mesai_id')
                ->join('ogrenim', 'personel.personel_ogrenim', '=', 'ogrenim.ogrenim_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('ayrilis', 'personel.personel_durum', '=', 'ayrilis.ayrilis_id')
                ->where('personel_durum', '<>', '1')
                ->where('personel_birim', $birim_id))
                ->addColumn('action', 'admin.backend.personel.personel-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.backend.personel.personelayrilan', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'il',
            'ilce',
            'mesai',
            'ogrenim',
            'ayrilis'
        ));
    }
    public function PersonelAyrilawn()
    {
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $isYonetici = $user->yonetici == 1;

        $ogrenim = DB::table('ogrenim')->where('ogrenim_durum', '1')->get();
        $mesai = DB::table('mesai_saati')->where('mesai_durum', '1')->get();
        $il = DB::table('il')->get();
        $ilce = DB::table('ilce')->get();
        $birim = DB::table('birim')->where('birim_durum', '1')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $ayrilis = DB::table('ayrilis')->where('ayrilis_durum', '1')->get();

        $title = 'Ayrılış Yapan Personel';
        $pagetitle = 'Ayrılış Yapan Personel Listesi';

        if (request()->ajax()) {

            $personeller = Personel::select(
                'personel.personel_id',
                'personel.personel_adsoyad',
                'personel.personel_sicilno',
                'durum.durum_ad',
                'unvan.unvan_ad',
                'birim.birim_ad',
                'gorev.gorev_ad',
                'ogrenim.ogrenim_ad',
                'ayrilis.ayrilis_tur',
            )
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                ->join('mesai_saati', 'personel.personel_mesai', '=', 'mesai_saati.mesai_id')
                ->join('ogrenim', 'personel.personel_ogrenim', '=', 'ogrenim.ogrenim_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('ayrilis', 'personel.personel_durum', '=', 'ayrilis.ayrilis_id')
                ->where('personel_durum', '<>', '1')
                ->when($isYonetici, function ($query) use ($kurum_id) {
                    return $query->where('personel.personel_kurumid', $kurum_id);
                }, function ($query) use ($birim_id) {
                    return $query->where('personel.personel_birim', $birim_id);
                })
                ->get();

            return DataTables()->of($personeller)
                ->addColumn('action', 'admin.backend.personel.personel-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.personel.personelayrilan', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'il',
            'ilce',
            'mesai',
            'ogrenim',
            'ayrilis'
        ));
    }
    public function PersonelAyrilan()
    {
        if (!Auth::user()->hasPermissionTo('personel.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        // 🔹 Tanımlar
        $ogrenim = DB::table('ogrenim')->where('ogrenim_durum', '1')->get();
        $mesai = DB::table('mesai_saati')->where('mesai_durum', '1')->get();
        $il = DB::table('il')->get();
        $ilce = DB::table('ilce')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $ayrilis = DB::table('ayrilis')->where('ayrilis_durum', '1')->get();

        // 🔹 Birim listesi (yöneticiye göre)
        $birim = DB::table('birim')
            ->where('birim_durum', '1')
            ->when($isYonetici, function ($query) use ($bolge_id) {
                $query->where('birim_durum', '1');
            }, function ($query) use ($birim_id) {
                $query->where('birim_id', $birim_id);
            })
            ->get();

        // 🔹 Kartlı personeller
        $kartliPersoneller = DB::table('pdks_kartlar')
            ->where('kart_durum', '1')
            ->distinct()
            ->pluck('kart_personelid')
            ->toArray();

        $title = 'Personel';
        $pagetitle = 'Personel Listesi';

        // 🔹 DataTable
        if (request()->ajax()) {
            $personeller = Personel::select(
                    'personel.personel_id',
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'durum.durum_ad',
                    'unvan.unvan_ad',
                    'birim.birim_ad',
                    'gorev.gorev_ad',
                    'personel.personel_kan',
                    'ogrenim.ogrenim_ad',
                    'ayrilis.ayrilis_tur',
                )
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('gorev', 'personel.personel_gorev', '=', 'gorev.gorev_id')
                ->join('mesai_saati', 'personel.personel_mesai', '=', 'mesai_saati.mesai_id')
                ->join('ogrenim', 'personel.personel_ogrenim', '=', 'ogrenim.ogrenim_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('ayrilis', 'personel.personel_durum', '=', 'ayrilis.ayrilis_id')
                ->where('personel_durum', '<>', '1')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('personel.personel_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('personel.personel_birim', $birim_id);
                });

            return DataTables()->of($personeller)
                ->addColumn('kart_durum', function ($row) use ($kartliPersoneller) {
                    return in_array($row->personel_id, $kartliPersoneller) ? 1 : 0;
                })
                ->addColumn('action', 'admin.backend.personel.personel-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.personel.personelayrilan', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'unvan',
            'birim',
            'il',
            'ilce',
            'mesai',
            'ogrenim',
            'ayrilis'
        ));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'personel_adsoyad' => 'nullable|string|max:255',
            'personel_tc' => 'nullable|numeric|digits:11',
            'personel_telefon' => 'nullable|string|max:15',
            'personel_durumid' => 'nullable|exists:durum,durum_id',
            'personel_gorev' => 'nullable|exists:gorev,gorev_id',
            'personel_unvan' => 'nullable|exists:unvan,unvan_id',
            'personel_birim' => 'nullable|exists:birim,birim_id',
            'personel_dogumtarihi' => 'nullable',
            'personel_isegiristarih' => 'nullable',
            'personel_eposta' => 'nullable|email',
            'personel_kartkullanim' => 'nullable',
            'personel_derece' => 'nullable',
            'personel_kademe' => 'nullable',
            'personel_il' => 'nullable|string',
            'personel_ilce' => 'nullable|string',
            'personel_sozlesmelimi' => 'nullable|string',
            'personel_engellimi' => 'nullable|string',
            'personel_mesai' => 'nullable',
            'personel_kan' => 'nullable',
            'personel_ogrenim' => 'nullable',
            'personel_okul' => 'nullable|string',
            'personel_durum' => 'string',
            'personel_adres' => 'nullable|string',
            'personel_aciklama' => 'nullable|string',
            'personel_resim' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Kullanıcı bilgileri
        $validated['personel_kurumid'] = auth()->user()->kurum_id;
        $validated['personel_ekleyen'] = auth()->user()->id;
        $validated['personel_bolge'] = auth()->user()->bolge_id;

        $isNew = !$request->has('personel_id');

        // Eğer resim yüklendiyse
        if ($request->hasFile('personel_resim')) {
            $kurumId = $validated['personel_kurumid'];
            $sicilNo = $validated['personel_sicilno'];
            $extension = $request->personel_resim->extension();

            // Hedef klasör: upload/personel/{kurumId}
            $targetDir = public_path("upload/personel/{$kurumId}");
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0777, true);
            }

            // Düzenleme ise eski resmi sil
            if ($request->personel_id) {
                $existingPersonel = Personel::find($request->personel_id);
                if ($existingPersonel && $existingPersonel->personel_resim) {
                    $oldImagePath = public_path($existingPersonel->personel_resim);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
            }

            // Yeni dosya ismi: {sicilNo}.jpg (orijinal uzantı ile)
            $fileName = "{$sicilNo}.{$extension}";
            $request->personel_resim->move($targetDir, $fileName);

            $validated['personel_resim'] = "upload/personel/{$kurumId}/{$fileName}";
        } else {
            // Resim seçilmezse eski resmi koru
            if ($request->personel_id) {
                unset($validated['personel_resim']);
            }
        }
        // Kart kullanım geçmişi ekleme
        // Kart kullanımı loglama
        if ($request->has('personel_kartkullanim')) {
            if ($request->personel_kartkullanim == 1) {
                // Kart kullanmaya başladıysa yeni kayıt aç
                DB::table('personel_kart_gecmisi')->insert([
                    'personel_id' => $request->personel_id,
                    'baslangic_tarihi' => now()->startOfMonth(),
                    'bitis_tarihi' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Kart kullanımı kapandıysa en son açıkta kalan kaydı kapat
                DB::table('personel_kart_gecmisi')
                    ->where('personel_id', $request->personel_id)
                    ->whereNull('bitis_tarihi')
                    ->update([
                        'bitis_tarihi' => now()->endOfMonth(),
                        'updated_at' => now(),
                    ]);
            }
        }
        $personel = Personel::updateOrCreate(
            ['personel_id' => $request->personel_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Personel Başarıyla Eklendi!' : 'Personel Başarıyla Güncellendi!',
            'data' => $personel
        ]);
    }
    public function store2(Request $request)
    {
        $validated = $request->validate([
            'personel_adsoyad' => 'required|string|max:255',
            'personel_tc' => 'required|numeric|digits:11',
            'personel_sicilno' => 'required|string|max:20',
            'personel_telefon' => 'required|string|max:15',
            'personel_durumid' => 'required|exists:durum,durum_id', // Durum ID kontrolü
            'personel_gorev' => 'required|exists:gorev,gorev_id',
            'personel_unvan' => 'required|exists:unvan,unvan_id',
            'personel_birim' => 'required|exists:birim,birim_id',
            'personel_dogumtarihi' => 'date',
            'personel_isegiristarih' => 'date',
            'personel_eposta' => 'email',
            'personel_kartkullanim' => 'required', // PDKS kart kullanımı için 0 veya 1
            'personel_derece' => 'numeric',
            'personel_kademe' => 'numeric',
            'personel_il' => 'string',
            'personel_ilce' => 'string',
            'personel_sozlesmelimi' => 'required|string',
            'personel_engellimi' => 'required|string',
            'personel_mesai' => 'string',
            'personel_kan' => 'string',
            'personel_ogrenim' => 'string',
            'personel_okul' => 'string',
            'personel_durum' => 'string',
            'personel_adres' => 'string',
            'personel_aciklama' => 'string',
            'personel_resim' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        // Kullanıcının  bilgisini al
        $validated['personel_kurumid'] = auth()->user()->kurum_id;
        $validated['personel_ekleyen'] = auth()->user()->id;
        //$validated['personel_ip'] = $request->ip(); ------ Kullanıcının IP adresini al

        if ($request->hasFile('personel_resim')) {
            // Eğer düzenleme ise mevcut resmi bulup sil
            if ($request->personel_id) {
                $existingPersonel = Personel::find($request->personel_id);
                if ($existingPersonel && $existingPersonel->personel_resim && File::exists(public_path($existingPersonel->personel_resim))) {
                    File::delete(public_path($existingPersonel->personel_resim));
                }
            }

            // Yeni resmi yükle
            $imageName = time() . '_' . $request->personel_resim->getClientOriginalName();
            $request->personel_resim->move(public_path('upload/personel'), $imageName);
            $validated['personel_resim'] = 'upload/personel/' . $imageName;
        } else {
            // Resim seçilmezse eski resmi koru
            if ($request->personel_id) {
                unset($validated['personel_resim']);
            }
        }

        $isNew = !$request->has('personel_id');

        $personel = Personel::updateOrCreate(
            ['personel_id' => $request->personel_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Personel Başarıyla Eklendi!' : 'Personel Başarıyla Güncellendi!',
            'data' => $personel
        ]);
    }
    public function edit(Request $request)
    {
        $personel = Personel::where('personel_id', $request->personel_id)->first();

        if (!$personel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personel bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Personel Başarılı Bir Şekilde Güncelendi!',
            'data' => $personel
        ]);
    }
    public function delete(Request $request)
    {
        $personel = Personel::find($request->personel_id);

        if (!$personel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personel bulunamadı!'
            ], 404);
        }
        $userId = Auth::id();
        $personel->personel_durum = '0';
        $personel->personel_silen = $userId;
        $personel->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Personel Başarılı Bir Şekilde Silindi!',
            'data' => $personel
        ]);
    }
    public function PersonelDetay2(Request $request)
    {
        $title = 'Personel Detay';
        $pagetitle = 'Personel Detay Listesi';
        $birim_id = auth()->user()->birim_id;

        $query = Personel::select(
            'personel.*',
            'unvan.unvan_ad',
            'durum.durum_ad',
            'gorev.gorev_ad as gorev_ad',
            'birim.birim_ad as birim_ad',
            'personel.personel_isegiristarih as personel_isegiristarih'
        )
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')  // Unvan join
            ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')  // Durum join
            ->join('gorev', 'gorev.gorev_id', '=', 'personel.personel_gorev') // Gorev join
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim') // Birim join
            ->where('personel.personel_durum', '1')
            ->where('personel.personel_birim', $birim_id);

        // Unvan id varsa filtre uygula
        if ($request->filled('unvan_id')) {
            $query->where('personel.personel_gorev', $request->unvan_id);
        }

        $personeller = $query->get();

        return view('admin.backend.personel.personel-detay', compact('personeller', 'title', 'pagetitle'));
    }
    public function PersonelDetay(Request $request)
    {
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        $title = 'Personel Detay';
        $pagetitle = 'Personel Detay Listesi';

        $query = Personel::select(
            'personel.*',
            'unvan.unvan_ad',
            'durum.durum_ad',
            'gorev.gorev_ad as gorev_ad',
            'birim.birim_ad as birim_ad',
            'personel.personel_isegiristarih as personel_isegiristarih'
        )
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')
            ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')
            ->join('gorev', 'gorev.gorev_id', '=', 'personel.personel_gorev')
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim')
            ->where('personel.personel_durum', '1');

        // 👇 Yetkiye göre filtreleme
        if ($isYonetici) {
            $query->where('personel.personel_bolge', $bolge_id);
        } else {
            $query->where('personel.personel_birim', $birim_id);
        }

        // 👇 Unvan filtresi (isteğe bağlı)
        if ($request->filled('unvan_id')) {
            $query->where('personel.personel_gorev', $request->unvan_id);
        }

        $personeller = $query->get();

        return view('admin.backend.personel.personel-detay', compact('personeller', 'title', 'pagetitle'));
    }
    public function PersonelBilgiDetays_onceki($id)
    {
        $title = 'Personel Bilgi Kart Detayı';
        $pagetitle = 'Personel Bilgi Kart Detayı';
    
        // Personel bilgisi (ilişkiler dahil)
        $personel = Personel::with(['unvan', 'durum', 'gorev', 'birim', 'il', 'ilce'])
            ->where('personel_durum', 1)
            ->findOrFail($id);
    
        // Bu personele ait giriş-çıkış kayıtları
        $gecisler = DB::table('pdks_cihaz_gecisler')
            ->join('pdks_kartlar', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->where('pdks_kartlar.kart_personelid', $id)
            ->orderBy('pdks_cihaz_gecisler.gecis_tarihi', 'desc')
            ->select(
                'pdks_cihaz_gecisler.gecis_id',
                'pdks_cihaz_gecisler.gecis_tarihi'
            )
            ->get();
    
        // İzin kayıtları
        $izinler = DB::table('izin')
            ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
            ->where('izin.izin_personel', $id)
            ->where('izin.izin_durum', '1') // aktif izinler
            ->orderBy('izin.izin_id', 'desc')
            ->select(
                'izin.*',
                'izin_turleri.izin_ad'
            )
            ->get();
    
        return view('admin.backend.personel.personel-bilgidetay', compact(
            'personel',
            'izinler',
            'gecisler',
            'title',
            'pagetitle'
        ));
    }
    public function PersonelBilgiDetay($id)
    {
        $title = 'Personel Bilgi Kart Detayı';
        $pagetitle = 'Personel Bilgi Kart Detayı';

        // Personel bilgisi (ilişkiler dahil)
        $personel = Personel::with(['unvan', 'durum', 'gorev', 'birim', 'il', 'ilce', 'dosyalar', 'ogrenim'])
            ->where('personel_durum', '1')
            ->findOrFail($id);

        // Bu personele ait günlük giriş-çıkış (ilk/son okutma)
        $gecisler = DB::table('pdks_cihaz_gecisler')
            ->join('pdks_kartlar', 'pdks_cihaz_gecisler.kart_id', '=', 'pdks_kartlar.kart_id')
            ->where('pdks_kartlar.kart_personelid', $id)
            ->select(
                DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi) as tarih'),
                DB::raw('MIN(pdks_cihaz_gecisler.gecis_tarihi) as giris'),
                DB::raw('MAX(pdks_cihaz_gecisler.gecis_tarihi) as cikis'),
                DB::raw('COUNT(*) as kayit_sayisi')
            )
            ->groupBy(DB::raw('DATE(pdks_cihaz_gecisler.gecis_tarihi)'))
            ->orderBy('tarih', 'desc')
            ->take(100) // Son 100 kaydı al
            ->get();

        // İzin kayıtları (mevcut)
        $izinler = DB::table('izin')
            ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
            ->where('izin.izin_personel', $id)
            ->where('izin.izin_durum', '1')
            ->orderBy('izin.izin_id', 'desc')
            ->select('izin.*', 'izin_turleri.izin_ad')
            ->take(100) // Son 100 kaydı al
            ->get();

        return view('admin.backend.personel.personel-bilgidetay', compact(
            'personel',
            'izinler',
            'gecisler',
            'title',
            'pagetitle'
        ));
    }    
    public function PersonelKartGdecmisi(Request $request)
    {
        $title = 'Personel Kart Kullanım Geçmişi';
        $pagetitle = 'Personel Kart Kullanım Geçmiş Listesi';
        $kurum_id = auth()->user()->kurum_id;
        $personel = DB::table('personel')->where('personel_kurumid', $kurum_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();

        if (request()->ajax()) {
            $dateRange = request('date_range');

            $query = DB::table('personel as p')
                ->join('personel_kart_gecmisi as pkg', 'pkg.personel_id', '=', second: 'p.personel_id')
                ->where('p.personel_kurumid', $kurum_id)
                ->where('pkg.durum', '1')
                ->select(
                    'p.personel_adsoyad as personel_adsoyad',
                    'pkg.baslangic_tarihi as baslangic_tarihi',
                    'pkg.bitis_tarihi as bitis_tarihi',
                    'p.personel_id as personel_id',
                    'pkg.id as id'
                );

            // Tarih aralığı filtresi
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $start = $dates[0];
                    $end = $dates[1];
                    $query->whereBetween(DB::raw('DATE(pkg.baslangic_tarihi)'), [$start, $end]);
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.personel.personel-kart-gecmisi-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.personel.personel-kart-gecmisi', compact( 'title', 'pagetitle','personel'));
    }
    public function PersonelKartGecmisi(Request $request)
    {
        $title     = 'Personel Kart Kullanım Geçmişi';
        $pagetitle = 'Personel Kart Kullanım Geçmiş Listesi';
        $kurum_id  = auth()->user()->kurum_id;
        $birim_id  = auth()->user()->birim_id;

        $personel = DB::table('personel')
            ->where('personel_birim', $birim_id)
            ->where('personel_durum', '1')
            ->orderBy('personel_adsoyad', 'asc')
            ->get();

        if ($request->ajax()) {

            $query = DB::table('personel as p')
                // HATALI: second: 'p.personel_id' → DOĞRU:
                ->join('personel_kart_gecmisi as pkg', 'pkg.personel_id', '=', 'p.personel_id')
                ->where('p.personel_birim', $birim_id)
                ->where('pkg.durum', '1')
                ->select([
                    'p.personel_adsoyad as personel_adsoyad',
                    'pkg.baslangic_tarihi as baslangic_tarihi',
                    'pkg.bitis_tarihi as bitis_tarihi',
                    'p.personel_id as personel_id',
                    'pkg.id as id',
                ]);

            // === Tarih aralığı filtresi (YYYY-MM-DD - YYYY-MM-DD) ===
            if ($request->filled('date_range')) {
                [$start, $end] = array_map('trim', explode(' - ', $request->input('date_range')));

                try {
                    // DATETIME kolonlar için gün başı/sonu ile kapsayıcı filtre
                    $startDate = Carbon::parse($start)->startOfDay();
                    $endDate   = Carbon::parse($end)->endOfDay();

                    // İNDEKS DOSTU: DATE() kullanmadan filtrele
                    //$query->whereBetween('pkg.baslangic_tarihi', [$startDate, $endDate]);

                    // Eğer "seçilen aralıkla kesişen tüm kayıtlar (başlangıç–bitiş aralığı)" istenirse,
                    // yukarıdaki satır yerine şu bloğu kullan:
                    
                    $query->where(function($q) use ($startDate, $endDate) {
                        $q->whereBetween('pkg.baslangic_tarihi', [$startDate, $endDate])
                        ->orWhereBetween('pkg.bitis_tarihi',     [$startDate, $endDate])
                        ->orWhere(function($qq) use ($startDate, $endDate) {
                            $qq->where('pkg.baslangic_tarihi', '<=', $startDate)
                                ->where('pkg.bitis_tarihi',   '>=', $endDate);
                        });
                    });
                    
                } catch (\Throwable $e) {
                    // tarih parse edilemezse filtreyi uygulama
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.personel.personel-kart-gecmisi-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.personel.personel-kart-gecmisi', compact('title','pagetitle','personel'));
    }
    public function KartGecmisStore(Request $request)
    {
        $validated = $request->validate([
            'personel_id'      => 'required|integer|exists:personel,personel_id',
            'baslangic_tarihi' => 'required|date',
            'bitis_tarihi'     => 'nullable|date',
        ]);

        // 1. Bitiş tarihi kontrolü
        if (!empty($validated['bitis_tarihi']) && $validated['bitis_tarihi'] < $validated['baslangic_tarihi']) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bitiş tarihi başlangıç tarihinden önce olamaz!',
            ], 422);
        }

        // 2. Mükerrer kayıt kontrolü (aynı personel + tarih aralığı çakışması)
        $query = PersonelKartGecmisi::where('personel_id', $validated['personel_id'])
        ->where('durum', '1') // Aktif kayıtlar
            ->when($request->id, function ($q) use ($request) {
                // Güncellemede kendi kaydını hariç tut
                $q->where('id', '!=', $request->id);
            })
            ->where(function ($q) use ($validated) {
                $start = $validated['baslangic_tarihi'];
                $end   = $validated['bitis_tarihi'] ?? $validated['baslangic_tarihi']; // bitiş boşsa tek gün kabul

                // Çakışma kontrolü: (başlangıç veya bitiş kesişmesi veya tamamen kapsama)
                $q->whereBetween('baslangic_tarihi', [$start, $end])
                ->orWhereBetween('bitis_tarihi', [$start, $end])
                ->orWhere(function ($qq) use ($start, $end) {
                    $qq->where('baslangic_tarihi', '<=', $start)
                        ->where('bitis_tarihi', '>=', $end);
                });
            });

        if ($query->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bu personel için seçilen tarih aralığında zaten bir kayıt var!',
            ], 422);
        }

        // 3. Kayıt ekle/güncelle
        $isNew = !$request->has('id');
        $personelkartgecmis = PersonelKartGecmisi::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        return response()->json([
            'status'  => 'success',
            'message' => $isNew 
                ? 'Kayıt başarıyla eklendi!' 
                : 'Kayıt başarıyla güncellendi!',
            'data'    => $personelkartgecmis,
        ]);
    }
    public function KartGecmisEdit(Request $request)
    {
        $personelkartgecmis = PersonelKartGecmisi::where('id', $request->id)->first();

        if (!$personelkartgecmis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personel bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Personel Kart Kullanımı Başarılı Bir Şekilde Güncelendi!',
            'data' => $personelkartgecmis
        ]);
    }
    public function KartGecmisDelete(Request $request)
    {
        $personel = PersonelKartGecmisi::find($request->id);

        if (!$personel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personel bulunamadı!'
            ], 404);
        }
        //$userId = Auth::id();
        $personel->durum = '0';
        $personel->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Personel Başarılı Bir Şekilde Silindi!',
            'data' => $personel
        ]);
    }
    public function PersonelKapakMemur(Request $request)
    {
        $kurum_id  = auth()->user()->kurum_id;
        $birim_id  = auth()->user()->birim_id;
        $ayar = DB::table('ayar')->first();
        $title = 'Kapak Memuru Personel Listesi';
    
        $personel = DB::table('personel as p')
            ->selectRaw("
                p.personel_tc,
                p.personel_resim,
                p.personel_adsoyad,
                p.personel_sicilno as sira,
                CONCAT(SUBSTRING(p.personel_sicilno, 1, 2), '-', SUBSTRING(p.personel_sicilno, 3)) as personel_sicilno
            ")
            ->where('p.personel_durumid', '1')
            ->where('p.personel_durum', '1')
            ->where('p.personel_kurumid', $kurum_id)
            ->where('p.personel_birim', $birim_id)
            ->orderByRaw("CASE WHEN p.personel_sicilno BETWEEN '00%' AND '55%' THEN 1 ELSE 0 END")
            ->orderBy('p.personel_sicilno')
            ->get();
    


        return view('admin.backend.personel.kapak.kapakmemur', compact('title','personel','ayar') );
    }
    public function PersonelKapakIsci(Request $request)
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id  = auth()->user()->birim_id;
        $ayar = DB::table('ayar')->first();
        $title = 'Kapak İşçi Personel Listesi';
    
        $personel = DB::table('personel as p')
            ->selectRaw("
                p.personel_tc,
                p.personel_resim,
                p.personel_adsoyad,
                CONCAT(SUBSTRING(p.personel_sicilno, 1, 2), '-', SUBSTRING(p.personel_sicilno, 3)) as personel_sicilno,
                SUBSTRING(p.personel_sicilno, 3) as sira
            ")
            ->where('p.personel_durumid', '2')
            ->where('p.personel_durum', '1')
            ->where('p.personel_kurumid', $kurum_id)
            ->where('p.personel_birim', $birim_id)
            ->orderByRaw("CASE WHEN SUBSTRING(p.personel_sicilno, 3) BETWEEN '00' AND '55' THEN 1 ELSE 0 END")
            ->orderBy('sira')
            ->get();
    
        return view('admin.backend.personel.kapak.kapakisci', compact('title', 'personel', 'ayar'));
    }
    public function PersonelKapakFirma(Request $request)
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id  = auth()->user()->birim_id;
        $ayar = DB::table('ayar')->first();
        $title = 'Kapak İşçi Personel Listesi';
    
        $personel = DB::table('personel as p')
            ->selectRaw("
                p.personel_tc,
                p.personel_resim,
                p.personel_adsoyad,
                CONCAT(SUBSTRING(p.personel_sicilno, 1, 2), '-', SUBSTRING(p.personel_sicilno, 3)) as personel_sicilno,
                SUBSTRING(p.personel_sicilno, 3) as sira
            ")
            ->where('p.personel_durumid', '5')
            ->where('p.personel_durum', '1')
            ->where('p.personel_kurumid', $kurum_id)
            ->where('p.personel_birim', $birim_id)
            ->orderByRaw("CASE WHEN SUBSTRING(p.personel_sicilno, 3) BETWEEN '00' AND '55' THEN 1 ELSE 0 END")
            ->orderBy('sira')
            ->get();
    
        return view('admin.backend.personel.kapak.kapakisci', compact('title', 'personel', 'ayar'));
    }

 
}
