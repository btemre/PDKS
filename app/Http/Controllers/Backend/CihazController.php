<?php
namespace App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cihaz;

class CihazController extends Controller
{
    public function Cihaz()
    {
        if (!Auth::user()->hasPermissionTo('cihaz.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
    
        $kurum_id = auth()->user()->kurum_id;
        $kurum = DB::table('ayar')->where('ayar_bakim', operator: '1')->orderBy('ayar_kurum', 'asc')->get();

        $title = 'Cihaz Listesi';
        $pagetitle = 'Cihaz Listesi';
    
        if (request()->ajax()) {
            $query = Cihaz::select(
                    'pdks_cihazlar.*',
                    'ayar.ayar_kurum',   // ayar tablosundan istediğin kolonlar
                    'ayar.ayar_kurumid'
                )
                ->join('ayar', 'pdks_cihazlar.cihaz_kurumid', '=', 'ayar.ayar_kurumid')
                ->where('pdks_cihazlar.cihaz_durum', '1');
                // ->where('pdks_cihazlar.cihaz_kurumid', $kurum_id); // Eğer kullanıcıya göre filtre istiyorsan
    
            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.cihaz.cihaz-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.cihaz.cihaz', compact(
            'title',
            'pagetitle',
            'kurum'
        ));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cihaz_adi' => 'required|string',
            'cihaz_kurumid' => 'required|string',
            'cihaz_ip' => 'required|string',
            'cihaz_port' => 'required|integer',
            'cihaz_model' => 'required|string',
            'cihaz_gecistipi' => 'required|integer',
            'cihaz_aciklama' => 'nullable|string',
        ]);


        $validated['cihaz_ekleyenip'] = $request->ip();
        $validated['cihaz_ekleyenkullanici'] = auth()->user()->id;
        //$validated['cihaz_kurumid'] = auth()->user()->kurum_id;
        //$validated['cihaz_bolgeid'] = auth()->user()->bolge_id;
        $validated['cihaz_durum'] = '1';

        $isNew = !$request->has('cihaz_id');

        $cihaz = Cihaz::updateOrCreate(
            ['cihaz_id' => $request->cihaz_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Cihaz Kaydı Başarıyla Eklendi!' : 'Cihaz Kaydı Başarıyla Güncellendi!',
            'data' => $cihaz
        ]);
    }
    public function edit(Request $request)
    {
        $cihaz = Cihaz::where('cihaz_id', $request->cihaz_id)->first();

        if (!$cihaz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cihaz bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cihaz Başarılı Bir Şekilde Güncelendi!',
            'data' => $cihaz
        ]);
    }
    public function delete(Request $request)
    {
        $cihaz = Cihaz::find($request->cihaz_id);

        if (!$cihaz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cihaz bulunamadı!'
            ], 404);
        }

        // Kart sayısı kontrolü
        if (!is_null($cihaz->kart_sayisi) && $cihaz->kart_sayisi > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bu cihazda tanımlı kartlar var. Önce kartları silin!'
            ], 400);
        }

        // Silme işlemi (durumu pasif yapıyoruz)
        $cihaz->cihaz_durum = '0';
        $cihaz->cihaz_silenkullanici = auth()->user()->id;
        $cihaz->cihaz_silenip = $request->ip();
        $cihaz->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cihaz başarılı bir şekilde silindi!',
            'data' => $cihaz
        ]);
    }
    public function getBilgilendirmeData()
    {
        $kurum_id = Auth::user()->kurum_id;

        // Bildirim verisini sorgulama
        $cihazlar = Cihaz::where('cihaz_durum', '1')
            ->where('cihaz_kurumid', $kurum_id)
            ->where('baglanti_durumu', '0')
            ->get();
            
        // "header" view'ine veriyi gönder
        // Ve "header" içinde "bilgilendirme" include edildiği için o da bu veriye erişebilir.
        return view('admin.body.header', compact('cihazlar'));
    }
}
