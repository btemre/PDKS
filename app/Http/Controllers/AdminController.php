<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
//use Illuminate\Http\RedirectResponse;
use App\Models\Admin;
use App\Models\Cihaz;
use App\Models\Izin;
use App\Models\Kart;
use App\Models\Personel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DB;
class AdminController extends Controller
{
    public function AdminDashboard_onceki()
    {
        return view('admin.index');
    }
    public function AdminDashboard()
    {
        $title = 'Personel Devam Kontrol Sistemi';
        $pagetitle = 'Personel devam kontrol sistemi';
        $kurum_id = auth()->user()->kurum_id;
        $birim_id = auth()->user()->birim_id;
        $bolge_id = auth()->user()->bolge_id;
        $yonetici = auth()->user()->yonetici ==1;
        // Temel sorgu
        $where = DB::table('personel')
            ->where('personel_durum', '1');
            //->where('personel_kurumid', $kurum_id);

        // Yönetici kontrolü ile birim/bolge filtreleme
        if ($yonetici) {
            $where->where('personel_bolge', $bolge_id);
        } else {
            $where->where('personel_birim', $birim_id);
        }

        // Toplam personel sayısı
        $toplamPersonel = $where->count();

        // Temel sorgu
        $unvanQuery = DB::table('personel as p')
            ->join('gorev as g', 'g.gorev_id', '=', 'p.personel_gorev')
            ->select('p.personel_gorev as gorev_id', 'g.gorev_ad', DB::raw('COUNT(*) as count'))
            ->where('p.personel_durum', '1');
            //->where('p.personel_kurumid', $kurum_id);

        // Yönetici kontrolü ile filtreleme
        if ($yonetici) {
            $unvanQuery->where('p.personel_bolge', $bolge_id);
        } else {
            $unvanQuery->where('p.personel_birim', $birim_id);
        }

        // Son sorgu
        $unvanlar = $unvanQuery
            ->groupBy('p.personel_gorev', 'g.gorev_ad')
            ->orderBy('g.gorev_sira', 'asc')
            ->get();


        // İzinli personel verisi
        $izinModel =  new Izin();
        $izinli = $izinModel->izinli($kurum_id, $birim_id);
        $izinli = $izinli['izinli'];

        $izinli2 = $izinModel->izinli2($kurum_id, $birim_id);
        $izinli2 = $izinli2['izinli2'];

        // Blade'de kullanmak üzere diziye çeviriyoruz
        $izinli_personel = $izinli2->pluck('izin_ad', 'personel_adsoyad')->toArray();


        // Bugün gelmeyen personeller
        $data = Kart::BugunGelmeyen($birim_id);

        $gelmeyen = $data['liste'];  
        $gelmeyenUnvanGrafik = $data['grafik'];  
        

        // Bugün geç gelen personeller
        $data = Kart::BugunGecGelen($birim_id);

        $gecGelen = $data['liste'];   // tablo için
        $gecGelenUnvanGrafik = $data['grafik']; // grafik için
        
        

        $Personel = Personel::select(
            'personel.*',
            'unvan.unvan_ad',
            'durum.durum_ad',
            'gorev.gorev_ad as gorev_ad',
            'birim.birim_ad as birim_ad',
            'ogrenim.ogrenim_tur as ogrenim_tur'
        )
            ->join('unvan', 'unvan.unvan_id', '=', 'personel.personel_unvan')  // Unvan join
            ->join('durum', 'durum.durum_id', '=', 'personel.personel_durumid')  // Durum join
            ->join('gorev', 'gorev.gorev_id', '=', 'personel.personel_gorev') // Gorev join
            ->join('birim', 'birim.birim_id', '=', 'personel.personel_birim') // Birim join
            ->leftjoin('ogrenim', 'ogrenim.ogrenim_id', '=', 'personel.personel_ogrenim')
            ->where('personel.personel_durum', '1')
            ->where('personel.personel_kurumid', $kurum_id)
            ->where('personel.personel_birim', $birim_id)
            ->orderBy('gorev.gorev_sira', 'asc')
            ->orderBy('personel.personel_isegiristarih', 'asc')
            ->orderBy('personel.personel_sicilno', 'asc')

            ->get()
            ->map(function ($item) {
                if ($item->personel_isegiristarih) {
                    $iseGiris = Carbon::parse($item->personel_isegiristarih);
                    $bugun = Carbon::now();
                    $diff = $iseGiris->diff($bugun);

                    $parts = [];
                    if ($diff->y > 0)
                        $parts[] = "{$diff->y} yıl";
                    if ($diff->m > 0)
                        $parts[] = "{$diff->m} ay";
                    if ($diff->d > 0)
                        $parts[] = "{$diff->d} gün";

                    // Eğer hepsi sıfırsa (aynı gün ise) "0 gün" göster
                    $item->calisma_suresi = count($parts) > 0 ? implode(' ', $parts) : '0 gün';
                } else {
                    $item->calisma_suresi = '-';
                }
                return $item;
            });

        return view('admin.index', compact(
            'unvanlar',
            'toplamPersonel',
            'title',
            'pagetitle',
            'Personel',
            'izinli',
            'izinli_personel',
            'gelmeyen',
            'gelmeyenUnvanGrafik',
            'gecGelen',
            'gecGelenUnvanGrafik'
        ));
    }
    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login'); // logout sonrası login sayfasına yönlendir
    }
    public function AsdminLogin()
    {
        return view('admin.admin_login');
    }
    public function AdminLogin()
{
    if (Auth::check()) {
        // Zaten login olmuşsa dashboard'a yönlendir
        return redirect()->route('admin.dashboard');
    }
    return view('admin.admin_login'); // Login formu view
}
    public function AdminProfile()
    {
        $title = 'Profil';
        $pagetitle = 'Personel Güncelleme';
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view', compact('profileData', 'title', 'pagetitle'));
    }
    public function AdminProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->username = $request->username;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->detay = $request->detay;
        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/' . $data->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data['photo'] = $filename;
        }
        $data->save();
        $notifiaction = array(
            'message' => 'Profil Güncellenme işlemi başarılı',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notifiaction);
    }
    public function AdminChangePassword(Request $request)
    {
        $title = 'Şifre Güncelleme';
        $pagetitle = 'Şifre Güncelleme';
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_change_password', compact('profileData', 'title', 'pagetitle'));
    }
    public function AdminPasswordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        if (!Hash::check($request->old_password, Auth::user()->password)) {
            $notifiaction = array(
                'message' => 'Eski Parola Hatalı!',
                'alert-type' => 'error'
            );
            return back()->with($notifiaction);
        }

        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        $notifiaction = array(
            'message' => 'Parola Gücelleme Başarılı',
            'alert-type' => 'success'
        );
        return back()->with($notifiaction);

    }
}
