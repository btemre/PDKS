<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ilce;

class AdresController extends Controller
{
    public function getIlce($il_plaka)
    {
        // Doğru kolon: ilce_ilkodu
        $ilceler = Ilce::where('ilce_ilkodu', $il_plaka)->get();
        return response()->json($ilceler);
    }
}
