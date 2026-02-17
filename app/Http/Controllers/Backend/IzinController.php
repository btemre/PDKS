<?php

namespace App\Http\Controllers\Backend;
use App\Models\IzinMazeret;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Personel;
use App\Models\Izin;
use App\Http\Requests\IzinMazeretEkleRequest;

class IzinController extends Controller
{
    public function Izin2()
    {
        if (!Auth::user()->hasPermissionTo('personel.izin')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->orderBy('izin_ad', 'asc')->get();
        $izinturmazeret = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        // Doğrudan DB sorgusuyla resmi tatiller alındı
        $resmiTatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
        $title = 'İzin/Mazeret İşlemleri';
        $pagetitle = 'İzinli Listesi';
        if (request()->ajax()) {
            $query = Personel::select(
                'personel.personel_id',
                'izin.izin_id',
                'personel.personel_adsoyad',
                'personel.personel_sicilno',
                'durum.durum_ad',
                'unvan.unvan_ad',
                'birim.birim_ad',
                'izin.izin_baslayis',
                'izin.izin_bitis',
                'izin.izin_isebaslayis',
                'izin.izin_suresi',
                'izin.izin_yil',
                'izin_turleri.izin_ad'
            )
                ->join('izin', 'personel.personel_id', '=', 'izin.izin_personel')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where('izin.izin_durum', '1')
                ->where('izin.izin_birim', auth()->user()->birim_id)
                ->orderBy('izin.izin_id', 'desc');

            // Eğer tarih aralığı seçilmişse filtre uygula
            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('izin.izin_baslayis', [$startDate, $endDate]);
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.izin.izin-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.izin.izin', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'izintur',
            'unvan',
            'personel',
            'izinturmazeret',
            'resmiTatiller' // Resmi tatilleri view'e gönder
        ));
    }
    public function Izin()
    {
        if (!Auth::user()->hasPermissionTo('personel.izin')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }

        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        // 🔹 Tanımlar
        $izintur = DB::table('izin_turleri')
            ->where('izin_durum', '1')
            ->orderBy('izin_ad', 'asc')
            ->get();

        $izinturmazeret = DB::table('izin_turleri')
            ->where('izin_durum', '1')
            ->where('izin_statu', 2)
            ->orderBy('izin_ad', 'asc')
            ->get();

        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();

        // 🔹 Personeller (yöneticiyse bölgedeki tüm birimler, değilse kendi birimi)
        $personel = DB::table('personel')
            ->where('personel_durum', '1')
            ->when($isYonetici, function ($query) use ($bolge_id) {
                $query->where('personel_bolge', $bolge_id);
            }, function ($query) use ($birim_id) {
                $query->where('personel_birim', $birim_id);
            })
            ->orderBy('personel_adsoyad', 'asc')
            ->get();

        // 🔹 Resmî tatiller
        $resmiTatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();

        $title = 'İzin/Mazeret İşlemleri';
        $pagetitle = 'İzinli Listesi';

        // 🔹 AJAX isteği (DataTables)
        if (request()->ajax()) {
            $query = Personel::select(
                    'personel.personel_id',
                    'izin.izin_id',
                    'personel.personel_adsoyad',
                    'personel.personel_sicilno',
                    'durum.durum_ad',
                    'unvan.unvan_ad',
                    'birim.birim_ad',
                    'izin.izin_baslayis',
                    'izin.izin_bitis',
                    'izin.izin_isebaslayis',
                    'izin.izin_suresi',
                    'izin.izin_yil',
                    'izin_turleri.izin_ad'
                )
                ->join('izin', 'personel.personel_id', '=', 'izin.izin_personel')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where('izin.izin_durum', '1')
                ->when($isYonetici, function ($query) use ($bolge_id) {
                    $query->where('personel.personel_bolge', $bolge_id);
                }, function ($query) use ($birim_id) {
                    $query->where('izin.izin_birim', $birim_id);
                })
                ->orderBy('izin.izin_id', 'desc');

            // 🔹 Tarih aralığı filtresi
            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                if (count($dates) === 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('izin.izin_baslayis', [$startDate, $endDate]);
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.izin.izin-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.izin.izin', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'izintur',
            'unvan',
            'personel',
            'izinturmazeret',
            'resmiTatiller'
        ));
    }

    public function IzinOnay()
    {
        if (!Auth::user()->hasPermissionTo('izin.onay')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $izintur = DB::table('izin_turleri')->where('izin_durum', '1')->orderBy('izin_ad', 'asc')->get();
        $izinturmazeret = DB::table('izin_turleri')->where('izin_durum', '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        // Doğrudan DB sorgusuyla resmi tatiller alındı
        $resmiTatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
        $title = 'İzin/Mazeret Onay İşlemleri';
        $pagetitle = 'İzin Onayı';
        if (request()->ajax()) {
            $query = Personel::select(
                'personel.personel_id',
                'izin.izin_id',
                'personel.personel_adsoyad',
                'personel.personel_sicilno',
                'durum.durum_ad',
                'unvan.unvan_ad',
                'birim.birim_ad',
                'izin.izin_baslayis',
                'izin.izin_bitis',
                'izin.izin_isebaslayis',
                'izin.izin_suresi',
                'izin.izin_yil',
                'izin_turleri.izin_ad'
            )
                ->join('izin', 'personel.personel_id', '=', 'izin.izin_personel')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('izin_turleri', 'izin.izin_turid', '=', 'izin_turleri.izin_turid')
                ->where('izin.izin_durum', '1')
                ->where('izin.izin_onay', '0')
                ->where('izin.izin_birim', auth()->user()->birim_id);

            // Eğer tarih aralığı seçilmişse filtre uygula
            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('izin.izin_baslayis', [$startDate, $endDate]);
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.izin.izinonay-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.izin.izinonay', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'izintur',
            'unvan',
            'personel',
            'izinturmazeret',
            'resmiTatiller' // Resmi tatilleri view'e gönder
        ));
    }
    public function izinOnayla(Request $request)
    {
        $izin = Izin::findOrFail($request->izin_id);
        $izin->izin_onay = 1;
        $izin->izin_onaylayan = auth()->user()->id;
        $izin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'İzin başarıyla onaylandı.'
        ]);
    }
    public function izinTopluOnay(Request $request)
    {
        $ids = $request->input('izin_ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'Seçili izin yok!'], 400);
        }

        Izin::whereIn('izin_id', $ids)
            ->where('izin_onay', '!=', 1) // zaten onaylı olanı tekrar güncelleme
            ->update(['izin_onay' => 1], ['izin_onaylayan' => auth()->user()->id]);

        return response()->json(['message' => 'Seçilen izinler onaylandı.']);
    }
    public function IzinMazeret()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $izintur = DB::table('izin_turleri')->where('izin_durum', operator: '1')->where('izin_statu', 2)->orderBy('izin_ad', 'asc')->get();
        $unvan = DB::table('unvan')->where('unvan_durum', '1')->get();
        $gorev = DB::table('gorev')->where('gorev_durum', '1')->get();
        $durum = DB::table('durum')->where('durum_aktif', '1')->get();
        $personel = DB::table('personel')->where('personel_birim', $birim_id)->where('personel_durum', '1')->orderBy('personel_adsoyad', 'asc')->get();
        $resmiTatiller = DB::table('tatil')->pluck('tatil_tarih')->toArray();
        $title = 'Saatlik İzin  İşlemleri';
        $pagetitle = 'Saatlik İzinli Listesi';
        if (request()->ajax()) {
            $query = Personel::select(
                'personel.personel_id',
                'izin_mazeret.izinmazeret_id',
                'personel.personel_adsoyad',
                'personel.personel_sicilno',
                'durum.durum_ad',
                'unvan.unvan_ad',
                'birim.birim_ad',
                'izin_mazeret.izinmazeret_suresi',
                'izin_mazeret.izinmazeret_baslayis',
                'izin_mazeret.izinmazeret_baslayissaat',
                'izin_mazeret.izinmazeret_bitissaat',
                'izin_mazeret.izinmazeret_aciklama',
                'izin_mazeret.izinmazeret_yil',
                'izin_turleri.izin_ad'
            )
                ->join('izin_mazeret', 'personel.personel_id', '=', 'izin_mazeret.izinmazeret_personel')
                ->join('unvan', 'personel.personel_unvan', '=', 'unvan.unvan_id')
                ->join('durum', 'personel.personel_durumid', '=', 'durum.durum_id')
                ->join('birim', 'personel.personel_birim', '=', 'birim.birim_id')
                ->join('izin_turleri', 'izin_mazeret.izinmazeret_turid', '=', 'izin_turleri.izin_turid')
                ->where('izin_mazeret.izinmazeret_durum', '1')
                ->where('izin_mazeret.izinmazeret_birim', auth()->user()->birim_id);

            // Eğer tarih aralığı seçilmişse filtre uygula
            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('izin_mazeret.izinmazeret_baslayis', [$startDate, $endDate]);
                }
            }

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.izin.izinmazeret-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.backend.izin.izinmazeret', compact(
            'title',
            'pagetitle',
            'durum',
            'gorev',
            'izintur',
            'unvan',
            'personel',
            'resmiTatiller' // Resmi tatilleri view'e gönder
        ));
    }
    public function IzinKullani2m()
    {
        $kurum_id = auth()->user()->kurum_id;  // Kullanıcının kurum_id'sini alıyoruz
        $title = 'İzin Kullanım Listesi';
        $pagetitle = 'İzinli Listesi';

        if (request()->ajax()) {
            //DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

            $sorgu = "
        SELECT 
            MAX(i.izin_id) AS izin_id, 
            d.durum_ad, 
            u.unvan_ad, 
            it.izin_ad, 
            p.personel_isegiristarih, 
            p.personel_adsoyad, 
            SUM(i.izin_suresi) AS izin_suresi,
            i.izin_yil, 
            ich.izin_hakki, 
            p.personel_sozlesmelimi, 
            p.personel_engellimi,
            (ich.izin_hakki - SUM(i.izin_suresi)) AS Kalanizin, 
            LPAD((i.izin_yil - YEAR(p.personel_isegiristarih)), 2, '0') AS tecrube
        FROM 
            izin_calisan_haklari ich
        LEFT OUTER JOIN 
            durum d ON d.durum_id = ich.calisan_statu_id
        LEFT OUTER JOIN 
            personel p ON p.personel_durumid = d.durum_id
        LEFT OUTER JOIN 
            izin i ON i.izin_personel = p.personel_id AND ich.izin_tur_id = i.izin_turid
        LEFT OUTER JOIN 
            unvan u ON u.unvan_id = p.personel_unvan
        LEFT OUTER JOIN 
            izin_turleri it ON it.izin_turid = ich.izin_tur_id
        WHERE 
            ich.alt_tecrube <= i.izin_yil - YEAR(p.personel_isegiristarih)
            AND ich.ust_tecrube >= i.izin_yil - YEAR(p.personel_isegiristarih)
            AND i.izin_durum = '1'
            AND p.personel_durum = '1'
            AND i.izin_yil >= YEAR(NOW() - INTERVAL 2 YEAR)
            AND p.personel_kurumid = :kurum_id
        GROUP BY 
            i.izin_personel, i.izin_yil, i.izin_turid, d.durum_ad, u.unvan_ad, it.izin_ad, 
            p.personel_isegiristarih, p.personel_adsoyad, ich.izin_hakki, p.personel_sozlesmelimi, 
            p.personel_engellimi
        ORDER BY 
            personel_adsoyad ASC;
        ";

            // Parametreyi bağlıyoruz ve sorguyu çalıştırıyoruz
            $izinler = DB::select($sorgu, ['kurum_id' => $kurum_id]);

            // DataTable için dönen veri yapısını oluşturuyoruz
            $data = [
                'draw' => intval(request()->input('draw')),
                'recordsTotal' => count($izinler),
                'recordsFiltered' => count($izinler),
                'data' => $izinler,
            ];

            return response()->json($data);
        }

        return view('admin.backend.izin.izinkullanim', compact(
            'title',
            'pagetitle'
        ));
    }
    public function IzinKullanim2()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $title = 'İzin Kullanım Listesi';
        $pagetitle = 'İzin Listesi';

        if (request()->ajax()) {
            $izinKullanim = DB::select("
                SELECT
                    MAX(i.izin_id) AS izin_id,
                    d.durum_ad,
                    u.unvan_ad,
                    it.izin_ad,
                    p.personel_isegiristarih,
                    DATE_FORMAT(p.personel_isegiristarih, '%d-%m-%Y') AS personel_isegiristarih_formatted,
                    p.personel_adsoyad,
                    SUM(i.izin_suresi) AS izin_suresi,
                    i.izin_yil,
                    ich.izin_hakki,
                    p.personel_sozlesmelimi,
                    p.personel_engellimi,
                    (ich.izin_hakki - SUM(i.izin_suresi)) AS Kalanizin,
                    LPAD((i.izin_yil - YEAR(p.personel_isegiristarih)), 2, '0') AS tecrube
                FROM
                    izin_calisan_haklari ich
                LEFT OUTER JOIN
                    durum d ON d.durum_id = ich.calisan_statu_id
                LEFT OUTER JOIN
                    personel p ON p.personel_durumid = d.durum_id
                LEFT OUTER JOIN
                    izin i ON i.izin_personel = p.personel_id AND ich.izin_tur_id = i.izin_turid
                LEFT OUTER JOIN
                    unvan u ON u.unvan_id = p.personel_unvan
                LEFT OUTER JOIN
                    izin_turleri it ON it.izin_turid = ich.izin_tur_id
                WHERE
                    ich.alt_tecrube <= i.izin_yil - YEAR(p.personel_isegiristarih)
                    AND ich.ust_tecrube >= i.izin_yil - YEAR(p.personel_isegiristarih)
                    AND i.izin_durum = '1'
                    AND p.personel_durum = '1'
                    AND i.izin_yil >= YEAR(NOW() - INTERVAL 2 YEAR)
                    AND p.personel_birim = ?
                GROUP BY
                    i.izin_personel, i.izin_yil, i.izin_turid, d.durum_ad, u.unvan_ad, it.izin_ad,
                    p.personel_isegiristarih, p.personel_adsoyad, ich.izin_hakki, p.personel_sozlesmelimi,
                    p.personel_engellimi
                ORDER BY
                    personel_adsoyad ASC
            ", [$birim_id]);

            return DataTables()->of($izinKullanim)
                ->addIndexColumn()
                ->addColumn('tecrube_numeric', function ($row) {
                    return (int) $row->tecrube; // Sıralama için temiz sayısal değer
                })
                ->addColumn('izin_yil_numeric', function ($row) {
                    return (int) $row->izin_yil; // Sıralama için temiz sayısal değer
                })
                ->addColumn('izin_suresi_numeric', function ($row) {
                    return (float) $row->izin_suresi; // Sıralama için temiz sayısal değer
                })
                ->addColumn('izin_hakki_numeric', function ($row) {
                    return (float) $row->izin_hakki; // Sıralama için temiz sayısal değer
                })
                ->addColumn('Kalanizin_numeric', function ($row) {
                    return (float) $row->Kalanizin; // Sıralama için temiz sayısal değer
                })
                ->make(true);
        }

        return view('admin.backend.izin.izinkullanim', compact(
            'title',
            'pagetitle'
        ));
    }
    public function IzinKullanim()
    {
        // Kullanıcı bilgilerini tek seferde al
        $user = auth()->user();
        $kurum_id = $user->kurum_id;
        $birim_id = $user->birim_id;
        $bolge_id = $user->bolge_id;
        $isYonetici = $user->yonetici == 1;

        $title = 'İzin Kullanım Listesi';
        $pagetitle = 'İzin Listesi';

        if (request()->ajax()) {
            // Filtre koşulunu dinamik belirle
            $whereClause = $isYonetici
                ? 'p.personel_bolge = ?'
                : 'p.personel_birim = ?';
            $filterValue = $isYonetici ? $bolge_id : $birim_id;

            $izinKullanim = DB::select("
                SELECT
                    MAX(i.izin_id) AS izin_id,
                    d.durum_ad,
                    u.unvan_ad,
                    it.izin_ad,
                    p.personel_isegiristarih,
                    DATE_FORMAT(p.personel_isegiristarih, '%d-%m-%Y') AS personel_isegiristarih_formatted,
                    p.personel_adsoyad,
                    SUM(i.izin_suresi) AS izin_suresi,
                    i.izin_yil,
                    ich.izin_hakki,
                    p.personel_sozlesmelimi,
                    p.personel_engellimi,
                    (ich.izin_hakki - SUM(i.izin_suresi)) AS Kalanizin,
                    LPAD((i.izin_yil - YEAR(p.personel_isegiristarih)), 2, '0') AS tecrube
                FROM
                    izin_calisan_haklari ich
                LEFT OUTER JOIN
                    durum d ON d.durum_id = ich.calisan_statu_id
                LEFT OUTER JOIN
                    personel p ON p.personel_durumid = d.durum_id
                LEFT OUTER JOIN
                    izin i ON i.izin_personel = p.personel_id AND ich.izin_tur_id = i.izin_turid
                LEFT OUTER JOIN
                    unvan u ON u.unvan_id = p.personel_unvan
                LEFT OUTER JOIN
                    izin_turleri it ON it.izin_turid = ich.izin_tur_id
                WHERE
                    ich.alt_tecrube <= i.izin_yil - YEAR(p.personel_isegiristarih)
                    AND ich.ust_tecrube >= i.izin_yil - YEAR(p.personel_isegiristarih)
                    AND i.izin_durum = '1'
                    AND p.personel_durum = '1'
                    AND i.izin_yil >= YEAR(NOW() - INTERVAL 2 YEAR)
                    AND {$whereClause}
                GROUP BY
                    i.izin_personel, i.izin_yil, i.izin_turid, d.durum_ad, u.unvan_ad, it.izin_ad,
                    p.personel_isegiristarih, p.personel_adsoyad, ich.izin_hakki, p.personel_sozlesmelimi,
                    p.personel_engellimi
                ORDER BY
                    personel_adsoyad ASC
            ", [$filterValue]);

            return DataTables()->of($izinKullanim)
                ->addIndexColumn()
                ->addColumn('tecrube_numeric', fn($row) => (int) $row->tecrube)
                ->addColumn('izin_yil_numeric', fn($row) => (int) $row->izin_yil)
                ->addColumn('izin_suresi_numeric', fn($row) => (float) $row->izin_suresi)
                ->addColumn('izin_hakki_numeric', fn($row) => (float) $row->izin_hakki)
                ->addColumn('Kalanizin_numeric', fn($row) => (float) $row->Kalanizin)
                ->make(true);
        }

        return view('admin.backend.izin.izinkullanim', compact(
            'title',
            'pagetitle'
        ));
    }

    public function getKalanIzins(Request $request)
    {
        $personelId = $request->personel_id;
        $izinTurId  = $request->izin_turid;
        $yil        = $request->izin_yil ?? date('Y');

        $birim_id = auth()->user()->birim_id;

        $kalan = DB::selectOne("
            SELECT 
                ich.izin_hakki - IFNULL(SUM(i.izin_suresi),0) AS kalan_izin
            FROM izin_calisan_haklari ich
            LEFT JOIN personel p ON p.personel_durumid = ich.calisan_statu_id
            LEFT JOIN izin i ON i.izin_personel = p.personel_id 
                            AND i.izin_turid = ich.izin_tur_id
                            AND i.izin_yil = ?
                            AND i.izin_durum = '1'
            WHERE p.personel_id = ?
            AND ich.izin_tur_id = ?
            AND p.personel_birim = ?
            GROUP BY ich.izin_hakki
        ", [$yil, $personelId, $izinTurId, $birim_id]);

        return response()->json([
            'kalan_izin' => $kalan->kalan_izin ?? 0
        ]);
    }
    public function getKalanIzin(Request $request)
    {
        $personelId = $request->personel_id;
        $izinTurId  = $request->izin_turid;
        $yil        = $request->izin_yil ?? date('Y');
        $birim_id   = auth()->user()->birim_id;
    
        $kalan = DB::selectOne("
            SELECT 
                ich.izin_hakki,
                IFNULL(kullanim.toplam_izin, 0) AS kullanilan,
                (ich.izin_hakki - IFNULL(kullanim.toplam_izin, 0)) AS kalan_izin
            FROM izin_calisan_haklari ich
            INNER JOIN personel p ON p.personel_durumid = ich.calisan_statu_id
            LEFT JOIN (
                SELECT izin_personel, izin_turid, izin_yil, SUM(izin_suresi) AS toplam_izin
                FROM izin
                WHERE izin_yil = ? 
                  AND izin_turid = ?
                  AND izin_durum = '1'
                GROUP BY izin_personel, izin_turid, izin_yil
            ) AS kullanim 
                ON kullanim.izin_personel = p.personel_id 
               AND kullanim.izin_turid = ich.izin_tur_id 
               AND kullanim.izin_yil = ?
            WHERE p.personel_id = ?
              AND ich.izin_tur_id = ?
              AND p.personel_birim = ?
              -- Tecrübe yılı kontrolü
              AND ich.alt_tecrube <= (? - YEAR(p.personel_isegiristarih))
              AND ich.ust_tecrube >= (? - YEAR(p.personel_isegiristarih))
            LIMIT 1
        ", [$yil, $izinTurId, $yil, $personelId, $izinTurId, $birim_id, $yil, $yil]);
    
        return response()->json([
            'izin_hakki'  => $kalan->izin_hakki ?? 0,
            'kullanilan'  => $kalan->kullanilan ?? 0,
            'kalan_izin'  => $kalan->kalan_izin ?? 0
        ]);
    }
    public function store2(Request $request)
    {
        $validated = $request->validate([
            'izin_personel' => 'required|string',
            'izin_turid' => 'required',
            'izin_yil' => 'required|numeric|digits:4',
            'izin_baslayis' => 'required|date',
            'izin_suresi' => 'required|numeric',
            'izin_bitis' => 'required|date',
            'izin_isebaslayis' => 'required|date',
        ]);
        // Aynı personele ait aynı tarih aralığı için mükerrer kontrol
        $exists = Izin::where('izin_personel', $validated['izin_personel'])
        ->where('izin_durum', '1')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('izin_baslayis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhereBetween('izin_bitis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('izin_baslayis', '<=', $validated['izin_baslayis'])
                            ->where('izin_bitis', '>=', $validated['izin_bitis']);
                    });
            })
            ->when($request->izin_id, function ($query, $izin_id) {
                return $query->where('izin_id', '!=', $izin_id);
            })
            ->exists();
        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personele ait belirtilen tarihler arasında zaten izin bulunmaktadır!'
            ], 422);
        }
        $validated['izin_kurumid'] = auth()->user()->kurum_id;
        $validated['izin_birim'] = auth()->user()->birim_id;
        $validated['izin_ekleyen_personel'] = auth()->user()->id;
        $validated['izin_ekleyen_ip'] = $request->ip();
        $isNew = !$request->has('izin_id');
        $izin = Izin::updateOrCreate(
            ['izin_id' => $request->izin_id],
            $validated
        );
        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'İzin Başarıyla Eklendi!' : 'İzin Başarıyla Güncellendi!',
            'data' => $izin
        ]);
    }
    public function store_TumIzinKisitli(Request $request)
    {
        $validated = $request->validate([
            'izin_personel' => 'required|string',
            'izin_turid' => 'required',
            'izin_yil' => 'required|numeric|digits:4',
            'izin_baslayis' => 'required|date',
            'izin_suresi' => 'required|numeric',
            'izin_bitis' => 'required|date',
            'izin_isebaslayis' => 'required|date',
        ]);

        // 1️⃣ Aynı personele ait aynı tarih aralığı için mükerrer kontrol
        $exists = Izin::where('izin_personel', $validated['izin_personel'])
            ->where('izin_durum', '1')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('izin_baslayis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhereBetween('izin_bitis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('izin_baslayis', '<=', $validated['izin_baslayis'])
                            ->where('izin_bitis', '>=', $validated['izin_bitis']);
                    });
            })
            ->when($request->izin_id, function ($query, $izin_id) {
                return $query->where('izin_id', '!=', $izin_id);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personele ait belirtilen tarihler arasında zaten izin bulunmaktadır!'
            ], 422);
        }

        // 2️⃣ Kalan izin kontrolü
        $kalan = DB::table('izin_calisan_haklari as ich')
            ->leftJoin('personel as p', 'p.personel_durumid', '=', 'ich.calisan_statu_id')
            ->leftJoin('izin as i', function ($join) use ($validated) {
                $join->on('i.izin_personel', '=', 'p.personel_id')
                    ->where('i.izin_turid', '=', $validated['izin_turid'])
                    ->where('i.izin_yil', '=', $validated['izin_yil'])
                    ->where('i.izin_durum', '=', '1');
                    
            })
            ->where('p.personel_id', $validated['izin_personel'])
            ->where('ich.izin_tur_id', $validated['izin_turid'])
            ->where('p.personel_birim', auth()->user()->birim_id)
            ->selectRaw('ich.izin_hakki - IFNULL(SUM(i.izin_suresi),0) as kalan_izin')
            ->groupBy('ich.izin_hakki')
            ->first();

        $kalanIzin = $kalan->kalan_izin ?? 0;

        if ($validated['izin_suresi'] > $kalanIzin) {
            return response()->json([
                'status' => 'error',
                'message' => "Kalan izin yetersiz! Personele ait yalnızca {$kalanIzin} gün izin bulunmaktadır."
            ], 422);
        }

        // 3️⃣ Kaydetme işlemi
        $validated['izin_kurumid'] = auth()->user()->kurum_id;
        $validated['izin_birim'] = auth()->user()->birim_id;
        $validated['izin_ekleyen_personel'] = auth()->user()->id;
        $validated['izin_ekleyen_ip'] = $request->ip();

        $isNew = !$request->has('izin_id');

        $izin = Izin::updateOrCreate(
            ['izin_id' => $request->izin_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'İzin Başarıyla Eklendi!' : 'İzin Başarıyla Güncellendi!',
            'data' => $izin
        ]);
    }
    public function stores(Request $request)
    {
        $validated = $request->validate([
            'izin_personel' => 'required|string',
            'izin_turid' => 'required',
            'izin_yil' => 'required|numeric|digits:4',
            'izin_baslayis' => 'required|date',
            'izin_suresi' => 'required|numeric',
            'izin_bitis' => 'required|date',
            'izin_isebaslayis' => 'required|date',
        ]);

        // 1️⃣ Aynı personele ait aynı tarih aralığı için mükerrer kontrol
        $exists = Izin::where('izin_personel', $validated['izin_personel'])
            ->where('izin_durum', '1')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('izin_baslayis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhereBetween('izin_bitis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('izin_baslayis', '<=', $validated['izin_baslayis'])
                            ->where('izin_bitis', '>=', $validated['izin_bitis']);
                    });
            })
            ->when($request->izin_id, function ($query, $izin_id) {
                return $query->where('izin_id', '!=', $izin_id);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personele ait belirtilen tarihler arasında zaten izin bulunmaktadır!'
            ], 422);
        }

        // 2️⃣ Kalan izin kontrolü sadece izin_turid = 1 için
        if ($validated['izin_turid'] == '1') {

            $kalan = DB::table('izin_calisan_haklari as ich')
                ->leftJoin('personel as p', 'p.personel_durumid', '=', 'ich.calisan_statu_id')
                ->leftJoin('izin as i', function ($join) use ($validated) {
                    $join->on('i.izin_personel', '=', 'p.personel_id')
                        ->where('i.izin_turid', '=', $validated['izin_turid'])
                        ->where('i.izin_yil', '=', $validated['izin_yil'])
                        ->where('i.izin_durum', '=', '1');
                })
                ->where('p.personel_id', $validated['izin_personel'])
                ->where('ich.izin_tur_id', $validated['izin_turid'])
                ->where('p.personel_birim', auth()->user()->birim_id)
                ->selectRaw('ich.izin_hakki - IFNULL(SUM(i.izin_suresi),0) as kalan_izin')
                ->groupBy('ich.izin_hakki')
                ->first();

            $kalanIzin = $kalan->kalan_izin ?? 0;

            if ($validated['izin_suresi'] > $kalanIzin) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Kalan izin yetersiz! Personele ait yalnızca {$kalanIzin} gün izin bulunmaktadır."
                ], 422);
            }
        }

        // 3️⃣ Kaydetme işlemi
        $validated['izin_kurumid'] = auth()->user()->kurum_id;
        $validated['izin_birim'] = auth()->user()->birim_id;
        $validated['izin_ekleyen_personel'] = auth()->user()->id;
        $validated['izin_ekleyen_ip'] = $request->ip();

        $isNew = !$request->has('izin_id');

        $izin = Izin::updateOrCreate(
            ['izin_id' => $request->izin_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'İzin Başarıyla Eklendi!' : 'İzin Başarıyla Güncellendi!',
            'data' => $izin
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'izin_personel' => 'required|string',
            'izin_turid' => 'required',
            'izin_yil' => 'required|numeric|digits:4',
            'izin_baslayis' => 'required|date',
            'izin_suresi' => 'required|numeric',
            'izin_bitis' => 'required|date',
            'izin_isebaslayis' => 'required|date',
            'izin_adresi' => 'nullable|string',
            'izin_saglikkurumu' => 'nullable|string',
            'izin_aciklama' => 'nullable|string',
            'izin_vefat' => 'nullable|string',
        ]);

        // 1️⃣ Aynı personele ait aynı tarih aralığı için mükerrer kontrol
        $exists = Izin::where('izin_personel', $validated['izin_personel'])
            ->where('izin_durum', '1')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('izin_baslayis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhereBetween('izin_bitis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('izin_baslayis', '<=', $validated['izin_baslayis'])
                            ->where('izin_bitis', '>=', $validated['izin_bitis']);
                    });
            })
            ->when($request->izin_id, function ($query, $izin_id) {
                return $query->where('izin_id', '!=', $izin_id);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personele ait belirtilen tarihler arasında zaten izin bulunmaktadır!'
            ], 422);
        }

        // 2️⃣ Kalan izin kontrolü sadece izin_turid = 1 için
        if ($validated['izin_turid'] == '1') {

            // getKalanIzin mantığını kullanıyoruz
            $kalan = DB::selectOne("
                SELECT 
                    ich.izin_hakki,
                    IFNULL(kullanim.toplam_izin, 0) AS kullanilan,
                    (ich.izin_hakki - IFNULL(kullanim.toplam_izin, 0)) AS kalan_izin
                FROM izin_calisan_haklari ich
                INNER JOIN personel p ON p.personel_durumid = ich.calisan_statu_id
                LEFT JOIN (
                    SELECT izin_personel, izin_turid, izin_yil, SUM(izin_suresi) AS toplam_izin
                    FROM izin
                    WHERE izin_yil = ? 
                    AND izin_turid = ?
                    AND izin_durum = '1'
                    ".($request->izin_id ? "AND izin_id != ?" : "")."
                    GROUP BY izin_personel, izin_turid, izin_yil
                ) AS kullanim 
                    ON kullanim.izin_personel = p.personel_id 
                AND kullanim.izin_turid = ich.izin_tur_id 
                AND kullanim.izin_yil = ?
                WHERE p.personel_id = ?
                AND ich.izin_tur_id = ?
                AND p.personel_birim = ?
                AND ich.alt_tecrube <= (? - YEAR(p.personel_isegiristarih))
                AND ich.ust_tecrube >= (? - YEAR(p.personel_isegiristarih))
                LIMIT 1
            ", $request->izin_id 
                    ? [$validated['izin_yil'], $validated['izin_turid'], $request->izin_id, $validated['izin_yil'], $validated['izin_personel'], $validated['izin_turid'], auth()->user()->birim_id, $validated['izin_yil'], $validated['izin_yil']]
                    : [$validated['izin_yil'], $validated['izin_turid'], $validated['izin_yil'], $validated['izin_personel'], $validated['izin_turid'], auth()->user()->birim_id, $validated['izin_yil'], $validated['izin_yil']]
            );

            $kalanIzin = $kalan->kalan_izin ?? 0;

            if ($validated['izin_suresi'] > $kalanIzin) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Kalan izin yetersiz! Personele ait yalnızca {$kalanIzin} gün izin bulunmaktadır."
                ], 422);
            }
        }

        // 3️⃣ Kaydetme işlemi
        $validated['izin_kurumid'] = auth()->user()->kurum_id;
        $validated['izin_birim'] = auth()->user()->birim_id;
        $validated['izin_ekleyen_personel'] = auth()->user()->id;
        $validated['izin_ekleyen_ip'] = $request->ip();

        $isNew = !$request->has('izin_id');

        $izin = Izin::updateOrCreate(
            ['izin_id' => $request->izin_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'İzin Başarıyla Eklendi!' : 'İzin Başarıyla Güncellendi!',
            'data' => $izin
        ]);
    }
    public function storesorunlu(Request $request)
    {
        $validated = $request->validate([
            'izin_personel' => 'required|string',
            'izin_turid' => 'required',
            'izin_yil' => 'required|numeric|digits:4',
            'izin_baslayis' => 'required|date',
            'izin_suresi' => 'required|numeric',
            'izin_bitis' => 'required|date',
            'izin_isebaslayis' => 'required|date',
        ]);

        // 1️⃣ Aynı personele ait aynı tarih aralığı için mükerrer kontrol
        $exists = Izin::where('izin_personel', $validated['izin_personel'])
            ->where('izin_durum', '1')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('izin_baslayis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhereBetween('izin_bitis', [$validated['izin_baslayis'], $validated['izin_bitis']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('izin_baslayis', '<=', $validated['izin_baslayis'])
                            ->where('izin_bitis', '>=', $validated['izin_bitis']);
                    });
            })
            ->when($request->izin_id, function ($query, $izin_id) {
                return $query->where('izin_id', '!=', $izin_id);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personele ait belirtilen tarihler arasında zaten izin bulunmaktadır!'
            ], 422);
        }

        // 2️⃣ Kalan izin kontrolü sadece izin_turid = 1 için
        if ($validated['izin_turid'] == '1') {
            // getKalanIzin fonksiyonunu store içinde çağır
            $response = $this->getKalanIzin(new Request([
                'personel_id' => $validated['izin_personel'],
                'izin_turid'   => $validated['izin_turid'],
                'izin_yil'     => $validated['izin_yil'],
                'izin_id'      => $request->izin_id ?? null,
            ]));

            $kalanData = json_decode($response->getContent());
            $kalanIzin = $kalanData->kalan_izin ?? 0;

            if ($validated['izin_suresi'] > $kalanIzin) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Kalan izin yetersiz! Personele ait yalnızca {$kalanIzin} gün izin bulunmaktadır."
                ], 422);
            }
        }

        // 3️⃣ Kaydetme işlemi
        $validated['izin_kurumid'] = auth()->user()->kurum_id;
        $validated['izin_birim'] = auth()->user()->birim_id;
        $validated['izin_ekleyen_personel'] = auth()->user()->id;
        $validated['izin_ekleyen_ip'] = $request->ip();

        $isNew = !$request->has('izin_id');

        $izin = Izin::updateOrCreate(
            ['izin_id' => $request->izin_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'İzin Başarıyla Eklendi!' : 'İzin Başarıyla Güncellendi!',
            'data' => $izin
        ]);
    }
    public function IzinZorunlu_saglikli()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $title = 'İzin Zorunlu Listesi';
        $pagetitle = 'Zorunlu İzin Takibi';
        
        if (request()->ajax()) {
            // Son 3 yıl için dinamik yıl hesaplama
            $currentYear = date('Y');
            $years = [$currentYear - 2, $currentYear - 1, $currentYear];
            
            $izinZorunlu = DB::select("
                SELECT 
                    p.personel_id,
                    p.personel_adsoyad,
                    d.durum_ad,
                    u.unvan_ad,
                    MAX(CASE WHEN yillar.yil = ? THEN 
                        COALESCE(ich.izin_hakki - COALESCE(izin_kullanim.toplam_kullanim, 0), 0) 
                        ELSE NULL END) AS izin_kalan_" . $years[0] . ",
                    MAX(CASE WHEN yillar.yil = ? THEN 
                        COALESCE(ich.izin_hakki - COALESCE(izin_kullanim.toplam_kullanim, 0), 0) 
                        ELSE NULL END) AS izin_kalan_" . $years[1] . ",
                    MAX(CASE WHEN yillar.yil = ? THEN 
                        COALESCE(ich.izin_hakki - COALESCE(izin_kullanim.toplam_kullanim, 0), 0) 
                        ELSE NULL END) AS izin_kalan_" . $years[2] . "
                FROM personel p
                INNER JOIN durum d ON d.durum_id = p.personel_durumid
                INNER JOIN unvan u ON u.unvan_id = p.personel_unvan
                CROSS JOIN (
                    SELECT ? AS yil UNION SELECT ? UNION SELECT ?
                ) AS yillar
                LEFT JOIN izin_calisan_haklari ich ON ich.calisan_statu_id = p.personel_durumid 
                    AND ich.izin_tur_id = 1
                    AND ich.alt_tecrube <= (yillar.yil - YEAR(p.personel_isegiristarih))
                    AND ich.ust_tecrube >= (yillar.yil - YEAR(p.personel_isegiristarih))
                LEFT JOIN (
                    SELECT 
                        izin_personel, 
                        izin_yil, 
                        SUM(izin_suresi) AS toplam_kullanim
                    FROM izin 
                    WHERE izin_turid = 1 
                        AND izin_durum = '1'
                        AND izin_yil IN (?, ?, ?)
                    GROUP BY izin_personel, izin_yil
                ) AS izin_kullanim ON izin_kullanim.izin_personel = p.personel_id 
                    AND izin_kullanim.izin_yil = yillar.yil
                WHERE p.personel_durum = '1'
                    AND p.personel_birim = ?
                GROUP BY p.personel_id, p.personel_adsoyad, d.durum_ad, u.unvan_ad
                ORDER BY p.personel_adsoyad ASC
            ", [
                $years[0], $years[1], $years[2], // CASE WHEN için
                $years[0], $years[1], $years[2], // CROSS JOIN için
                $years[0], $years[1], $years[2], // izin tablosu için
                $birim_id
            ]);

            return DataTables()->of($izinZorunlu)
                ->addIndexColumn()
                ->editColumn('izin_kalan_' . $years[0], function ($row) use ($years) {
                    $kalan = $row->{'izin_kalan_' . $years[0]};
                    return $kalan !== null ? $kalan : '-';
                })
                ->editColumn('izin_kalan_' . $years[1], function ($row) use ($years) {
                    $kalan = $row->{'izin_kalan_' . $years[1]};
                    return $kalan !== null ? $kalan : '-';
                })
                ->editColumn('izin_kalan_' . $years[2], function ($row) use ($years) {
                    $kalan = $row->{'izin_kalan_' . $years[2]};
                    return $kalan !== null ? $kalan : '-';
                })
                ->rawColumns(['izin_kalan_' . $years[0], 'izin_kalan_' . $years[1], 'izin_kalan_' . $years[2]])
                ->make(true);
        }

        // View'e yılları gönder
        $currentYear = date('Y');
        $displayYears = [$currentYear - 2, $currentYear - 1, $currentYear];
        
        return view('admin.backend.izin.izinzorunlu', compact(
            'title',
            'pagetitle',
            'displayYears'
        ));
    }
    public function IzinZorunlu()
    {
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $title = 'İzin Zorunlu Listesi';
        $pagetitle = 'Zorunlu İzin Takibi';
        
        if (request()->ajax()) {
            // Son 3 yıl için dinamik yıl hesaplama
            $currentYear = date('Y');
            $years = [$currentYear - 2, $currentYear - 1, $currentYear];
            
            $izinZorunlu = DB::select("
                SELECT 
                    p.personel_id,
                    p.personel_adsoyad,
                    d.durum_ad,
                    u.unvan_ad,
                    MAX(CASE WHEN yillar.yil = ? THEN 
                        CASE 
                            WHEN YEAR(p.personel_isegiristarih) = yillar.yil THEN 0
                            ELSE COALESCE(ich.izin_hakki - COALESCE(izin_kullanim.toplam_kullanim, 0), 0)
                        END
                        ELSE NULL END) AS izin_kalan_" . $years[0] . ",
                    MAX(CASE WHEN yillar.yil = ? THEN 
                        CASE 
                            WHEN YEAR(p.personel_isegiristarih) = yillar.yil THEN 0
                            ELSE COALESCE(ich.izin_hakki - COALESCE(izin_kullanim.toplam_kullanim, 0), 0)
                        END
                        ELSE NULL END) AS izin_kalan_" . $years[1] . ",
                    MAX(CASE WHEN yillar.yil = ? THEN 
                        CASE 
                            WHEN YEAR(p.personel_isegiristarih) = yillar.yil THEN 0
                            ELSE COALESCE(ich.izin_hakki - COALESCE(izin_kullanim.toplam_kullanim, 0), 0)
                        END
                        ELSE NULL END) AS izin_kalan_" . $years[2] . "
                FROM personel p
                INNER JOIN durum d ON d.durum_id = p.personel_durumid
                INNER JOIN unvan u ON u.unvan_id = p.personel_unvan
                CROSS JOIN (
                    SELECT ? AS yil UNION SELECT ? UNION SELECT ?
                ) AS yillar
                LEFT JOIN izin_calisan_haklari ich ON ich.calisan_statu_id = p.personel_durumid 
                    AND ich.izin_tur_id = 1
                    AND ich.alt_tecrube <= (yillar.yil - YEAR(p.personel_isegiristarih))
                    AND ich.ust_tecrube >= (yillar.yil - YEAR(p.personel_isegiristarih))
                LEFT JOIN (
                    SELECT 
                        izin_personel, 
                        izin_yil, 
                        SUM(izin_suresi) AS toplam_kullanim
                    FROM izin 
                    WHERE izin_turid = 1 
                        AND izin_durum = '1'
                        AND izin_yil IN (?, ?, ?)
                    GROUP BY izin_personel, izin_yil
                ) AS izin_kullanim ON izin_kullanim.izin_personel = p.personel_id 
                    AND izin_kullanim.izin_yil = yillar.yil
                WHERE p.personel_durum = '1'
                    AND p.personel_birim = ?
                GROUP BY p.personel_id, p.personel_adsoyad, d.durum_ad, u.unvan_ad
                ORDER BY p.personel_adsoyad ASC
            ", [
                $years[0], $years[1], $years[2], // CASE WHEN için
                $years[0], $years[1], $years[2], // CROSS JOIN için
                $years[0], $years[1], $years[2], // izin tablosu için
                $birim_id
            ]);

            return DataTables()->of($izinZorunlu)
                ->addIndexColumn()
                ->editColumn('izin_kalan_' . $years[0], function ($row) use ($years) {
                    $kalan = $row->{'izin_kalan_' . $years[0]};
                    return $kalan !== null ? $kalan : '-';
                })
                ->editColumn('izin_kalan_' . $years[1], function ($row) use ($years) {
                    $kalan = $row->{'izin_kalan_' . $years[1]};
                    return $kalan !== null ? $kalan : '-';
                })
                ->editColumn('izin_kalan_' . $years[2], function ($row) use ($years) {
                    $kalan = $row->{'izin_kalan_' . $years[2]};
                    return $kalan !== null ? $kalan : '-';
                })
                ->rawColumns(['izin_kalan_' . $years[0], 'izin_kalan_' . $years[1], 'izin_kalan_' . $years[2]])
                ->make(true);
        }

        // View'e yılları gönder
        $currentYear = date('Y');
        $displayYears = [$currentYear - 2, $currentYear - 1, $currentYear];
        
        return view('admin.backend.izin.izinzorunlu', compact(
            'title',
            'pagetitle',
            'displayYears'
        ));
    }
    public function storeIzinMazeret(IzinMazeretEkleRequest $request)
    {
        $validated = $request->validated();

        // Aynı personele, aynı tarihte, çakışan saatlerde izin varsa hata döndür
        $exists = IzinMazeret::where('izinmazeret_personel', $validated['izinmazeret_personel'])
            ->where('izinmazeret_baslayis', $validated['izinmazeret_baslayis'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('izinmazeret_baslayissaat', [$validated['izinmazeret_baslayissaat'], $validated['izinmazeret_bitissaat']])
                    ->orWhereBetween('izinmazeret_bitissaat', [$validated['izinmazeret_baslayissaat'], $validated['izinmazeret_bitissaat']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('izinmazeret_baslayissaat', '<=', $validated['izinmazeret_baslayissaat'])
                            ->where('izinmazeret_bitissaat', '>=', $validated['izinmazeret_bitissaat']);
                    });
            })
            ->when($request->izinmazeret_id, function ($query, $izinmazeret_id) {
                return $query->where('izinmazeret_id', '!=', $izinmazeret_id); // Güncellerken aynı kayıt hariç tutulsun
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Personele ait belirtilen tarihte ve saatlerde zaten izin bulunmaktadır!'
            ], 422);
        }
        // Süre hesapla (HH:MM:SS)
        $start = strtotime($validated['izinmazeret_baslayissaat']);
        $end = strtotime($validated['izinmazeret_bitissaat']);
        $diffSeconds = $end - $start;
        $validated['izinmazeret_suresi'] = gmdate('H:i:s', $diffSeconds);
        $validated['izinmazeret_yil'] = now()->year;
        $validated['izinmazeret_kurumid'] = auth()->user()->kurum_id;
        $validated['izinmazeret_bolge'] = auth()->user()->bolge_id;
        $validated['izinmazeret_birim'] = auth()->user()->birim_id;
        $validated['izinmazeret_ekleyen_personel'] = auth()->user()->id;
        $validated['izinmazeret_ekleyen_ip'] = $request->ip();

        $isNew = !$request->has('izinmazeret_id');

        $izinmazeret = IzinMazeret::updateOrCreate(
            ['izinmazeret_id' => $request->izinmazeret_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'İzin Başarıyla Eklendi!' : 'İzin Başarıyla Güncellendi!',
            'data' => $izinmazeret
        ]);
    }
    public function edit(Request $request)
    {
        $izin = Izin::where('izin_id', $request->izin_id)->first();

        if (!$izin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Izin bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'İzin Başarılı Bir Şekilde Güncelendi!',
            'data' => $izin
        ]);
    }
    public function delete(Request $request)
    {
        $izin = Izin::find($request->izin_id);

        if (!$izin) {
            return response()->json([
                'status' => 'error',
                'message' => 'İzin bulunamadı!'
            ], 404);
        }
        $userId = Auth::id();
        $izin->izin_durum = '0';
        $izin->izin_silen_personel = $userId;
        $izin->save();
        return response()->json([
            'status' => 'success',
            'message' => 'İzin Başarılı Bir Şekilde Silindi!',
            'data' => $izin
        ]);
    }
    public function deleteIzinMazeret(Request $request)
    {
        $izinmazeret = IzinMazeret::find($request->izinmazeret_id);

        if (!$izinmazeret) {
            return response()->json([
                'status' => 'error',
                'message' => 'İzin bulunamadı!'
            ], 404);
        }
        $userId = Auth::id();
        $izinmazeret->izinmazeret_durum = '0';
        $izinmazeret->izinmazeret_silen_personel = $userId;
        $izinmazeret->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Saatlik İzin Başarılı Bir Şekilde Silindi!',
            'data' => $izinmazeret
        ]);
    }
    public function yazdir($id)
    {
        $izin = DB::table('izin as i')
            ->select('i.*', 'p.*', 'd.durum_ad', 'u.unvan_ad', 'it.izin_ad')
            ->leftJoin('personel as p', 'p.personel_id', '=', 'i.izin_personel')
            ->leftJoin('durum as d', 'p.personel_durumid', '=', 'd.durum_id')
            ->leftJoin('unvan as u', 'p.personel_unvan', '=', 'u.unvan_id')
            ->leftJoin('izin_turleri as it', 'i.izin_turid', '=', 'it.izin_turid')
            ->where('i.izin_id', $id)
            ->where('i.izin_kurumid', auth()->user()->kurum_id)
            ->first();

        if (!$izin) {
            abort(404, 'İzin Bulunamadı');
        }

        $view = $this->getIzinView($izin);

        $ayar = DB::table('ayar')->where('ayar_kurumid', auth()->user()->kurum_id)->first(); // Ayar tablon varsa bunu al

        return view($view, compact('izin', 'ayar'));
    }
    protected function getIzinView($izin)
    {
        if ($izin->personel_durumid == 1 && $izin->izin_turid == 1 && $izin->personel_sozlesmelimi == 0) 
        {
            return 'admin.backend.izin.yazdir.memur';
        } 
        elseif ($izin->personel_durumid == 1 && $izin->izin_turid == 3) 
        {
            return 'admin.backend.izin.yazdir.memurhastalik';   
        } 
        elseif ($izin->personel_durumid == 1 && $izin->izin_turid == 8) 
        {
            return 'admin.backend.izin.yazdir.memurbabalik';   
        }
        elseif ($izin->personel_durumid == 1 && $izin->izin_turid == 9) 
        {
            return 'admin.backend.izin.yazdir.memurevlilik';   
        } 
        elseif ($izin->personel_durumid == 1 && $izin->izin_turid == 6) 
        {
            return 'admin.backend.izin.yazdir.memurvefat';   
        }  
        elseif ($izin->personel_durumid == 2 && $izin->izin_turid == 6) 
        {
            return 'admin.backend.izin.yazdir.iscivefat';   
        } 
        elseif ($izin->personel_durumid == 1 && $izin->izin_turid == 1 && $izin->personel_sozlesmelimi == 1) {
            return 'admin.backend.izin.yazdir.memursozlesmeli';
        } elseif ($izin->personel_durumid == 2 && $izin->izin_turid == 3) {
            return 'admin.backend.izin.yazdir.iscihastalik';
        } elseif ($izin->personel_durumid == 2 && $izin->izin_turid == 1 && $izin->personel_sozlesmelimi == 0) {
            return 'admin.backend.izin.yazdir.isci';
        } elseif ($izin->personel_durumid == 5 && $izin->izin_turid == 1) {
            return 'izin.yazdir.firma';
        }
        // Diğer koşulları aynı mantıkla buraya ekle...
        else {
            abort(404, 'İlgili izin türü için form bulunamadı.');
        }
    }
}
