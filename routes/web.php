<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\AdresController;
use App\Http\Controllers\Backend\AracController;
use App\Http\Controllers\Backend\AyarController;
use App\Http\Controllers\Backend\CihazController;
use App\Http\Controllers\Backend\DosyaController;
use App\Http\Controllers\Backend\JeneratorHaftalikController;
use App\Http\Controllers\Backend\JetfanController;
use App\Http\Controllers\Backend\JeneratorController;
use App\Http\Controllers\Backend\KartController;
use App\Http\Controllers\Backend\PersonelController;
use App\Http\Controllers\Backend\IzinController;
use App\Http\Controllers\Backend\KazaController;
use App\Http\Controllers\Backend\EvrakController;
use App\Http\Controllers\Backend\PdksController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\TunelController;
use App\Http\Controllers\Backend\BilgisayarEnvanterController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// >> YENİ ENVANTER ROTASI BURAYA EKLENDİ <<
// Bu rota, hiçbir kimlik doğrulama grubunun içinde değildir.
// Bu sayede dışarıdan gelen script isteği engellenmez.
Route::post('/envanter/kaydet', [BilgisayarEnvanterController::class, 'store'])->name('envanter.store');

// PDKS cihaz geçiş kaydı (cihazdan sync/API). Mükerrer kuralı PdksGecisService ile uygulanır.
Route::post('/pdks/cihaz/gecis', [PdksController::class, 'CihazGecisKayit'])->name('pdks.cihaz.gecis');
// Ana sayfaya girildiğinde yönlendirme
Route::get('/', function () {
    return redirect()->route('admin.login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
// Ajax için il -> ilçe
Route::get('/get-ilce/{il_id}', [AdresController::class, 'getIlce']);
// Admin login sayfası
Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');
////// Başlangıç Admin Grup middleware
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');
    Route::middleware(['permission:pdks.rapor'])->group(function () {
        Route::get('/rapor', function () {
            $mailler = \App\Models\RaporMail::where('rapor_mail.durum', 1)
                ->where('rapor_mail.id', '!=', 1)
                ->join('birim', 'birim.birim_id', '=', 'rapor_mail.birim')
                ->select('rapor_mail.*', 'birim.birim_ad')
                ->orderBy('birim.birim_ad')
                ->orderBy('rapor_mail.email')
                ->get();
            $birimler = \App\Models\Birim::orderBy('birim_ad')->get();
            return view('admin.backend.rapor.gunluk', [
                'title' => 'Günlük Rapor',
                'pagetitle' => 'Günlük Rapor Gönder',
                'mailler' => $mailler,
                'birimler' => $birimler,
            ]);
        })->name('rapor.sayfa');
        Route::post('/rapor/gonder', function (\Illuminate\Http\Request $request) {
            try {
                $params = [];
                if ($request->filled('date')) {
                    $request->validate(['date' => 'date_format:Y-m-d']);
                    $params['--date'] = $request->date;
                }
                if ($request->filled('emails') && is_array($request->emails)) {
                    $params['--emails'] = implode(',', $request->emails);
                }
                Artisan::call('report:daily', $params);
                $tarih = $params['--date'] ?? now()->format('Y-m-d');
                return redirect()->route('rapor.sayfa')
                    ->with('message', $tarih . ' tarihli rapor başarıyla gönderildi.')
                    ->with('alert-type', 'success');
            } catch (\Exception $e) {
                return redirect()->route('rapor.sayfa')
                    ->with('message', $e->getMessage())
                    ->with('alert-type', 'error');
            }
        })->name('rapor.gonder');
        Route::post('/rapor/mail/ekle', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'email' => 'required|email|unique:rapor_mail,email',
                'birim' => 'required|integer|exists:birim,birim_id',
            ], [
                'email.required' => 'E-posta adresi zorunludur.',
                'email.email' => 'Geçerli bir e-posta adresi girin.',
                'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
                'birim.required' => 'Birim seçimi zorunludur.',
            ]);
            \App\Models\RaporMail::create([
                'email' => $request->email,
                'kurumid' => auth()->user()->kurum_id,
                'birim' => $request->birim,
                'bolge' => auth()->user()->bolge_id ?? 1,
                'durum' => 1,
            ]);
            return redirect()->route('rapor.sayfa')
                ->with('message', 'Mail adresi başarıyla eklendi.')
                ->with('alert-type', 'success');
        })->name('rapor.mail.ekle');
        Route::post('/rapor/mail/guncelle', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'mail_id' => 'required|integer|exists:rapor_mail,id',
                'email' => 'required|email|unique:rapor_mail,email,' . $request->mail_id,
                'birim' => 'required|integer|exists:birim,birim_id',
            ], [
                'email.required' => 'E-posta adresi zorunludur.',
                'email.email' => 'Geçerli bir e-posta adresi girin.',
                'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
                'birim.required' => 'Birim seçimi zorunludur.',
            ]);
            $mail = \App\Models\RaporMail::findOrFail($request->mail_id);
            $mail->update([
                'email' => $request->email,
                'birim' => $request->birim,
            ]);
            return redirect()->route('rapor.sayfa')
                ->with('message', 'Mail adresi güncellendi.')
                ->with('alert-type', 'success');
        })->name('rapor.mail.guncelle');
        Route::post('/rapor/mail/sil', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'mail_id' => 'required|integer|exists:rapor_mail,id',
            ]);
            \App\Models\RaporMail::where('id', $request->mail_id)->delete();
            return redirect()->route('rapor.sayfa')
                ->with('message', 'Mail adresi silindi.')
                ->with('alert-type', 'success');
        })->name('rapor.mail.sil');
    });
    Route::controller(PersonelController::class)->group(function () {
        Route::get('/personel/listesi', 'Personel')->name('personel.listesi');
        Route::get('/all/personelayrilan', 'PersonelAyrilan')->name('personel.ayrilan');
        Route::post('/personel/store', 'store')->name('personel.store');
        Route::post('/personel/edit', 'edit')->name('personel.edit');
        Route::post('/personel/delete', 'delete')->name('personel.delete');
        Route::get('/personel/detay', 'PersonelDetay')->name('personel.detay');
        Route::get('/personel/kartgecmisi', 'PersonelKartGecmisi')->name('personel.kartgecmisi');
        Route::post('/personel/kartgecmisstore', 'KartGecmisStore')->name('kartgecmis.store');
        Route::post('/personel/kartgecmisedit', 'KartGecmisEdit')->name('kartgecmis.edit');
        Route::post('/personel/kartgecmisdelete', 'KartGecmisDelete')->name('kartgecmis.delete');
        Route::get('/personel/memurkapak', 'PersonelKapakMemur')->name('personel.memurkapak');
        Route::get('/personel/iscikapak', 'PersonelKapakIsci')->name('personel.iscikapak');
        Route::get('/personel/firmakapak', 'PersonelKapakFirma')->name('personel.firmakapak');
        Route::get('/personel/bilgidetay/{id}', 'PersonelBilgiDetay')->name('personel.bilgidetay');



    });
    Route::controller(IzinController::class)->group(function () {
        Route::get('/personel/izin', 'Izin')->name('personel.izin');
        Route::get('/personel/izinonay', 'IzinOnay')->name('personel.izinonay');
        Route::post('/izin/onayla', 'izinOnayla')->name('izin.onayla');
        Route::post('/izin/toplu-onay', 'izinTopluOnay')->name('izin.topluOnay');

        Route::get('/personel/izinkullanim', 'IzinKullanim')->name('personel.izinkullanim');
        Route::get('/personel/izinmazeret', 'IzinMazeret')->name('personel.izinmazeret');
        Route::post('/personel/izinstore', 'store')->name('izin.store');
        Route::post('/personel/izinmazeretstore', 'storeIzinMazeret')->name('izinmazeret.store');
        Route::post('/personel/izinedit', 'edit')->name('izin.edit');
        Route::post('/personel/izindelete', 'delete')->name('izin.delete');
        Route::post('/personel/izinmazeretdelete', 'deleteIzinMazeret')->name('izinmazeret.delete');
        Route::get('izin/yazdir/{id}', 'yazdir')->name('izin.yazdir');
        Route::get('/izin/kalan', 'getKalanIzin')->name('izin.kalan');
        Route::get('/izin/izinzorunlu', 'IzinZorunlu')->name('izin.zorunlu');

    });
    

    Route::middleware(['pdks.permission'])->controller(PdksController::class)->group(function () {
        Route::get('/pdks/bugun', 'Bugun')->name('pdks.bugun');
        Route::post('/pdks/gecisekle', 'PdksGecisEkle')->name('pdksgecisekle.store');
        Route::get('/pdks/giriscikis', 'GirisCikis')->name('pdks.giriscikis');
        Route::get('/pdks/giriscikis/export', 'GirisCikisExport')->name('pdks.giriscikis.export');
        Route::get('/pdks/gecgelen', 'GecGelen')->name('pdks.gecgelen');
        Route::get('/pdks/erkencikan', 'ErkenCikan')->name('pdks.erkencikan');
        Route::get('/pdks/gelmeyen', 'Gelmeyen')->name('pdks.gelmeyen');
        Route::get('/pdks/gecislog', 'GecisLog')->name('pdks.gecislog');
        Route::get('/pdks/hareket', 'Hareket')->name('pdks.hareket');
        Route::get('/pdks/personel-kart-ara', 'PersonelKartAra')->name('pdks.personel-kart-ara');
        Route::get('/pdks/gunluk-aciklama', 'GunlukAciklamaGetir')->name('pdks.gunluk-aciklama.get');
        Route::post('/pdks/gunluk-aciklama', 'GunlukAciklamaKaydet')->name('pdks.gunluk-aciklama.store');
    });
    Route::controller(KazaController::class)->group(function () {
        Route::get('/kaza', 'Kaza')->name('kaza.listesi');
        Route::get('/kaza/istatistik', 'KazaIstatistik')->name('kaza.istatistik');
        Route::get('/kaza/detay/{yil}/{ay}', 'KazaDetay')->name('kaza.detay')->where(['yil' => '[0-9]{4}', 'ay' => '^(0?[1-9]|1[0-2])$']);
        Route::post('/kaza/store', 'store')->name('kaza.store');
        Route::post('/kaza/edit', 'edit')->name('kaza.edit');
        Route::post('/kaza/delete', 'delete')->name('kaza.delete');
        Route::get('/kaza/grafik', 'KazaGrafik')->name('kaza.grafik');
        Route::post('/kaza/show', 'show')->name('kaza.show');
        Route::post('/kaza/resim/delete', 'deleteImage')->name('kaza.resim.delete');
    });
    Route::controller(EvrakController::class)->group(function () {
        Route::get('/evrak', 'Evrak')->name('evrak.listesi');
        Route::get('/evrak/next-sira', 'getNextSira')->name('evrak.nextSira');
        Route::post('/evrak/store', 'store')->name('evrak.store');
        Route::post('/evrak/delete', 'delete')->name('evrak.delete');
    });
    Route::controller(KartController::class)->group(function () {
        Route::get('/personel/kartlistesi', 'Kart')->name('personel.kartlistesi');
        Route::post('/personel/kart/store', 'store')->name('kart.store');
        Route::post('/personel/kart/delete', 'delete')->name('kart.delete');
        Route::post('/personel/kart/update',  'update')->name('kart.update');

    });
    Route::controller(CihazController::class)->group(function () {
        Route::get('/cihaz/cihazlistesi', 'Cihaz')->name('cihaz.listesi');
        Route::post('/cihaz/store', 'store')->name('cihaz.store');
        Route::post('/cihaz/delete', 'delete')->name('cihaz.delete');
    });
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/permission', 'AllPermission')->name('all.permission');
        Route::get('/add/permission', 'AddPermission')->name('add.permission');
        Route::post('/store/permission', 'StorePermission')->name('store.permission');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');
        Route::post('/update/permission', 'UpdatePermission')->name('update.permission');
    });
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/roles', 'AllRoles')->name('all.roles');
        Route::get('/add/roles', 'AddRoles')->name('add.roles');
        Route::post('/store/roles', 'StoreRoles')->name('store.roles');
        Route::get('/edit/roles/{id}', 'EditRoles')->name('edit.roles');
        Route::get('/delete/roles/{id}', 'DeleteRoles')->name('delete.roles');
        Route::post('/update/roles', 'UpdateRoles')->name('update.roles');
    });
    Route::controller(RoleController::class)->group(function () {
        Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission');
        Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store');
        Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission');
        Route::get('/admin/edit/roles/{id}', 'AdminEditRoles')->name('admin.edit.roles');
        Route::get('/admin/delete/roles/{id}', 'AdminRolesDelete')->name('admin.delete.roles');
        Route::post('/admin/roles/update/{id}', 'AdminRolesUpdate')->name('admin.roles.update');

    });
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/admin', 'AllAdmin')->name('all.admin');
        Route::get('/all/adminlog', 'AllAdminLog')->name('all.adminlog');
        Route::get('/add/admin', 'AddAdmin')->name('add.admin');
        Route::post('/store/admin', 'StoreAdmin')->name('store.admin');
        Route::get('/edit/admin/{id}', 'EditAdmin')->name('edit.admin');
        Route::get('/delete/admin/{id}', 'DeleteAdmin')->name('delete.admin');
        Route::post('/update/admin/{id}', 'UpdateAdmin')->name('update.admin');

    });
    Route::controller(AracController::class)->group(function () {
        Route::get('/arac/araclistesi', 'Arac')->name('arac.listesi');
        Route::post('/arac/store', 'store')->name('arac.store');
        Route::post('/arac/delete', 'delete')->name('arac.delete');
        Route::post('/arac/edit', 'edit')->name('arac.edit');

    });
        Route::controller(AyarController::class)->group(function () {
        Route::get('/ayar/ayarlistesi', 'Ayar')->name('ayar.listesi');
        Route::post('/ayar/store', 'store')->name('ayar.store');
        Route::post('/ayar/delete', 'delete')->name('ayar.delete');
        Route::post('/ayar/edit', 'edit')->name('ayar.edit');
        Route::get('/ayar/backup', 'backupDatabase')->name('ayar.backup');
        Route::post('/ayar/backup-mail', 'backupDatabaseWithMail')->name('ayar.backup.mail');
    });
    Route::controller(BilgisayarEnvanterController::class)->group(function () {
        Route::get('/bilgisayar/listesi', 'BilgisayarListesi')->name('bilgisayar.listesi');
        Route::post('/envanter/edit', 'edit')->name('bilgisayar.edit');
        Route::post('/envanter/delete', 'delete')->name('bilgisayar.delete');
        Route::post('/envanter/guncelle', 'guncelle')->name('bilgisayar.guncelle');





    });
        Route::controller(TunelController::class)->group(function () {
        Route::get('/tunel/jetfan', 'Jetfan')->name('jetfan.listesi');
        Route::post('/jetfan/store', 'store')->name('jetfan.store');
        Route::post('/jetfan/delete', 'delete')->name('jetfan.delete');
        Route::post('/jetfan/edit', 'edit')->name('jetfan.edit');

    });
    Route::controller(JeneratorController::class)->group(function () {
        Route::get('/tunel/jenerator', 'Jenerator')->name('jenerator.listesi');
        Route::post('/jenerator/store', 'store')->name('jenerator.store');
        Route::post('/jenerator/delete', 'delete')->name('jenerator.delete');
        Route::post('/jenerator/edit', 'edit')->name('jenerator.edit');
    });
    Route::controller(JeneratorHaftalikController::class)->group(function () {
        Route::get('/jenerator/{id}/kontroller', 'JeneratorHaftalikKontrol')->name('jenerator.haftalik.kontrol');
        //Route::get('/tunel/jenerator', 'Jenerator')->name('jenerator.listesi');
        // Haftalık kontroller liste sayfası + DataTable ajax kaynağı (aynı route)
        //Route::get('/jenerator/{id}/kontroller', 'JeneratorHaftalikKontrol')->name('jenerator.kontroller');
        // Haftalık kontrol ekleme (Ajax)
        Route::post('/jenerator/{id}/kontroller', 'JeneratorHaftalikKontrolKaydet')->name('jenerator.kontrol.store');
        Route::post('/jenerator/kontrol/edit', 'JeneratorHaftalikKontrolEdit')->name('jenerator.kontrol.edit');
        Route::post('/jenerator/kontrol/update', 'JeneratorHaftalikKontrolUpdate')->name('jenerator.kontrol.update');
        Route::post('/jenerator/kontrol/delete', 'JeneratorHaftalikKontrolDelete')->name('jenerator.kontrol.delete');
    
        Route::controller(DosyaController::class)->group(function () {
            Route::post('/personel/{id}/dosya-upload', 'store')->name('personel.dosya.upload');
            Route::delete('/personel/dosya/{id}/delete', 'destroy')->name('personel.dosya.delete');
        });
        
        
    
        
    });
    

});
////// Bitiş Admin Grup middleware

Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');

//////Başlangıç Instructor Grup middleware
Route::middleware(['auth', 'roles:instructor'])->group(function () {
    Route::get('/instructor/dashboard', [InstructorController::class, 'InstructorDashboard'])->name('instructor.dashboard');
});
/////Bitiş Instructor Grup middleware

//Route::get('/user/dashboard', [UserController::class, 'UserDashboard'])->name('user.dashboard');