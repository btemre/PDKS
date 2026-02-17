<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ayar;
use Auth;
use File;
use Illuminate\Http\Request;

class AyarController extends Controller
{
    public function Ayar()
    {
        if (!Auth::user()->hasPermissionTo('ayar.menu')) {
            abort(403, 'Yetkiniz Bulunmamakta!');
        }
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Genel Ayarlar';
        $pagetitle = 'Ayarlar';
        if (request()->ajax()) {
            $query = Ayar::select(
                '*'
            )
                //->where('ayar_durum', '1')
                ->where('ayar_kurumid', auth()->user()->kurum_id);

            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.ayar.ayar-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.backend.ayar.ayar', compact(
            'title',
            'pagetitle'
        ));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ayar_kurum' => 'required|string',
            'ayar_yonetici' => 'required|string',
            'ayar_yoneticiunvan' => 'required|string',
            'ayar_basmuhendis' => 'string',
            'ayar_basmuhendisunvan' => 'string',
            'ayar_mudur' => 'required|string',
            'ayar_mudurunvan' => 'required|string',

        ]);


        $validated['ayar_kurumid'] = auth()->user()->kurum_id;
        //$validated['arac_durum'] = 1;

        $isNew = !$request->has('ayar_id');

        $ayar = Ayar::updateOrCreate(
            ['ayar_id' => $request->ayar_id],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => $isNew ? 'Ayar Kaydı Başarıyla Eklendi!' : 'Ayar Kaydı Başarıyla Güncellendi!',
            'data' => $ayar
        ]);
    }
    public function edit(Request $request)
    {
        $ayar = Ayar::where('ayar_id', $request->ayar_id)->first();

        if (!$ayar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ayar bulunamadı!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ayar Başarılı Bir Şekilde Güncelendi!',
            'data' => $ayar
        ]);
    }
    public function backupDatabase()
    {
        // SQL klasörü yoksa oluştur
        $backupPath = base_path('sql');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $filePath = $backupPath . '/' . $filename;

        // DB bilgileri
        $dbConnection = config('database.default');
        $dbHost = config("database.connections.$dbConnection.host");
        $dbPort = config("database.connections.$dbConnection.port");
        $dbName = config("database.connections.$dbConnection.database");
        $dbUser = config("database.connections.$dbConnection.username");
        $dbPass = config("database.connections.$dbConnection.password");

        // OS uyumlu mysqldump komutu
        $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} --port={$dbPort} {$dbName} > {$filePath}";

        // Komutu çalıştır
        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return redirect()->back()->with('error', 'Veritabanı yedeklenirken bir hata oluştu!');
        }

        return redirect()->back()->with('success', "Veritabanı başarıyla yedeklendi: {$filename}");
    }
}
