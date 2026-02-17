<?php

namespace App\Providers;
use App\Models\Arac;
use App\Models\Izin;
use App\Models\Personel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Cihaz;
use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $kurum_id = Auth::user()->kurum_id;
                $cihaz = Cihaz::where('cihaz_durum', '1')
                    ->where('cihaz_kurumid', $kurum_id)
                    ->where('baglanti_durumu', '0')
                    ->get();
            } else {
                $cihaz = collect();
            }

            $view->with('cihaz', $cihaz);
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $birim_id = Auth::user()->birim_id;
                $izinDonus = Izin::select(
                    'personel.personel_adsoyad',
                    'izin_turleri.izin_ad as izin_ad',
                    'izin.izin_isebaslayis',
                    'izin.izin_suresi',
                    'personel.personel_tc',
                    'personel.personel_resim'
                )
                    ->join('personel', 'personel.personel_id', '=', 'izin.izin_personel')
                    ->join('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin.izin_turid')
                    ->where('izin.izin_durum', '1')
                    ->where('izin.izin_birim', $birim_id)
                    ->whereDate('izin.izin_isebaslayis', Carbon::today())
                    ->get();
            } else {
                $izinDonus = collect();
            }
            $view->with('izinDonus', $izinDonus);
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $birim_id = Auth::user()->birim_id;
                $izinOnay = Izin::select(
                    'personel.personel_adsoyad',
                    'izin.izin_id as izin_id',
                    'izin_turleri.izin_ad as izin_ad',
                    'izin.izin_isebaslayis',
                    'izin.izin_suresi',
                    'personel.personel_tc'
                )
                    ->join('personel', 'personel.personel_id', '=', 'izin.izin_personel')
                    ->join('izin_turleri', 'izin_turleri.izin_turid', '=', 'izin.izin_turid')
                    ->where('izin.izin_durum', '1')
                    ->where('izin.izin_birim', $birim_id)
                    ->where('izin.izin_onay', '0')
                    ->get();
            } else {
                $izinOnay = collect();
            }
            $view->with('izinOnay', $izinOnay);
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $kurum_id = Auth::user()->kurum_id;
                $today = Carbon::today();
                $aracMuayene = Arac::where('arac_durum', '1')
                    ->where('arac_kurumid', $kurum_id)
                    ->get()
                    ->map(function ($arac) use ($today) {
                        $muayeneTarihi = Carbon::parse($arac->arac_ilkmuayene);
                        $gunFarki = $today->diffInDays($muayeneTarihi, false); // false => geçmiş tarihleri negatif döndürür
    
                        if ($gunFarki > 0 && $gunFarki <= 30) {
                            $arac->muayeneDurum = $gunFarki . ' gün kaldı';
                        } elseif ($gunFarki === 0) {
                            $arac->muayeneDurum = 'Bugün';
                        } elseif ($gunFarki < 0) {
                            $arac->muayeneDurum = 'Geçti';
                        } else {
                            $arac->muayeneDurum = null; // 30 günden fazla var, uyarı göstermeyebiliriz
                        }
                        return $arac;
                    })
                    ->filter(function ($arac) {
                        return !is_null($arac->muayeneDurum);
                    })
                    ->values(); // indexleri sıfırla
            } else {
                $aracMuayene = collect();
            }
            $view->with('aracMuayene', $aracMuayene);
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $birim_id = Auth::user()->birim_id;
                $today = Carbon::today();

                $dogumGunleri = Personel::select(
                    'personel.personel_adsoyad',
                    'personel.personel_tc',
                    'personel.personel_resim',
                    'personel.personel_dogumtarihi'
                )
                    ->where('personel.personel_birim', $birim_id)
                    ->where('personel.personel_durum', '1')
                    ->whereMonth('personel.personel_dogumtarihi', $today->month)
                    ->whereDay('personel.personel_dogumtarihi', $today->day)
                    ->get();
            } else {
                $dogumGunleri = collect();
            }
            $view->with('dogumGunleri', $dogumGunleri);
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $totalNotifications = 0;
                $cihazCount = isset($view->cihaz) ? $view->cihaz->count() : 0;
                $izinDonusCount = isset($view->izinDonus) ? $view->izinDonus->count() : 0;
                $izinOnayCount = isset($view->izinOnay) ? $view->izinOnay->count() : 0;
                $aracMuayeneCount = isset($view->aracMuayene) ? $view->aracMuayene->count() : 0;
                $totalNotifications = $cihazCount + $izinDonusCount + $izinOnayCount + $aracMuayeneCount;
            } else {
                $totalNotifications = 0;
            }
            $view->with('totalNotifications', $totalNotifications);
        });

    }
}
