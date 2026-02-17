<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fatura;

class FaturaController extends Controller
{
    public function listele()
    {
        $kurum_id = auth()->user()->kurum_id;
        $title = 'Fatura İşlemleri';
        $pagetitle = 'Fatura Listesi';
        if (request()->ajax()) {
            $query = Fatura::select(
                '*'
            )
                ->where('fatura.fatura_durum', '1')
                ->where('fatura.fatura_kurum', auth()->user()->kurum_id);
            // Eğer tarih aralığı seçilmişse filtre uygula
            if (!empty(request()->date_range)) {
                $dates = explode(' - ', request()->date_range);
                if (count($dates) == 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    $query->whereBetween('fatura.fatura_duzenlemetarih', [$startDate, $endDate]);
                }
            }
            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.fatura.fatura-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin.backend.fatura.fatura', compact(
            'title',
            'pagetitle'
        ));
    }
}
