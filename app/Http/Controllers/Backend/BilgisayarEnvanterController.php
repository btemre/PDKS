<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\BilgisayarEnvanter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Auth'u eklemeyi unutmayın
use DataTables; // Yajra DataTables'ı eklemeyi unutmayın

class BilgisayarEnvanterController extends Controller
{
    /**
     * Client'tan gelen envanter verisini alır ve veritabanına kaydeder/günceller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function store21(Request $request)
    {
        // Gelen isteğin gövdesindeki ham metin verisini alıyoruz.
        $reportContent = $request->getContent();

        // Metni satırlara ayırıyoruz.
        $lines = explode("\n", $reportContent);

        $parsedData = [];
        foreach ($lines as $line) {
            // Sadece ':' içeren satırları işleme alıyoruz.
            if (strpos($line, ':') !== false) {
                // Satırı ilk ':' karakterinden ikiye ayırıyoruz.
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Gelen anahtar kelimelere göre veritabanı sütunlarına eşleştirme yapıyoruz.
                switch ($key) {
                    case 'Bilgisayar Adi':
                        $parsedData['bilgisayar_adi'] = $value;
                        break;
                    case 'Kullanici Adi':
                        $parsedData['kullanici_adi'] = $value;
                        break;
                    case 'Etki Alani':
                        $parsedData['domain'] = $value;
                        break;
                    case 'IP Adresi':
                        $parsedData['ip_adresi'] = $value;
                        break;
                    case 'MAC Adresi':
                        // MAC adresini benzersiz anahtar olarak kullanacağız.
                        $parsedData['mac_adresi'] = str_replace('-', ':', $value);
                        break;
                    case 'Isletim Sistemi':
                        $parsedData['isletim_sistemi'] = $value;
                        break;
                    case 'Versiyon':
                        $parsedData['isletim_sistemi_surumu'] = $value;
                        break;
                    case 'Islemci':
                        $parsedData['islemci_modeli'] = $value;
                        break;
                    case 'Anakart Model':
                        // Anakart Üretici bilgisini de ekleyerek tam model oluşturuyoruz.
                        if(isset($parsedData['anakart_modeli'])){
                            $parsedData['anakart_modeli'] .= ' ' . $value;
                        } else {
                            $parsedData['anakart_modeli'] = $value;
                        }
                        break;
                    case 'Anakart Uretici':
                         $parsedData['anakart_modeli'] = $value;
                        break;
                    case 'Sistem Seri No':
                        $parsedData['seri_numarasi'] = $value;
                        break;
                    case 'Toplam Bellek':
                        $parsedData['ram_boyutu'] = $value;
                        break;
                    case 'Sistem Diski':
                        $parsedData['disk_boyutu'] = $value;
                        break;
                }
            }
        }
        
        // Eğer MAC adresi alınamadıysa veya boşsa, işlemi durdur.
        if (empty($parsedData['mac_adresi']) || $parsedData['mac_adresi'] === 'Bulunamadi') {
            return response()->json(['message' => 'Hata: Gerekli olan MAC adresi bilgisi alınamadı.'], 400);
        }

        // Envanter tarihini ve durumu ekleyelim
        $parsedData['envanter_tarihi'] = now();
        $parsedData['durum'] = 1;

        // updateOrCreate metodu ile MAC adresine göre kaydı bulur, varsa günceller, yoksa yeni oluşturur.
        // Bu, aynı bilgisayar için tekrar tekrar kayıt oluşturulmasını engeller.
        $envanter = BilgisayarEnvanter::updateOrCreate(
            ['mac_adresi' => $parsedData['mac_adresi']], // Aranacak anahtar
            $parsedData                                 // Kaydedilecek veya güncellenecek veriler
        );

        return response()->json(['message' => 'Envanter başarıyla kaydedildi!', 'data' => $envanter], 200);
    }
    public function store22(Request $request)
    {
        // Gelen isteğin gövdesindeki ham metin verisini alıyoruz.
        $reportContent = $request->getContent();

        // Metni satırlara ayırıyoruz.
        $lines = explode("\n", $reportContent);

        $parsedData = [];
        foreach ($lines as $line) {
            // Sadece ':' içeren satırları işleme alıyoruz.
            if (strpos($line, ':') !== false) {
                // Satırı ilk ':' karakterinden ikiye ayırıyoruz.
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Gelen anahtar kelimelere göre veritabanı sütunlarına eşleştirme yapıyoruz.
                switch ($key) {
                    case 'Bilgisayar Adi':
                        $parsedData['bilgisayar_adi'] = $value;
                        break;
                    case 'Kullanici Adi':
                        $parsedData['kullanici_adi'] = $value;
                        break;
                    case 'Etki Alani':
                        $parsedData['domain'] = $value;
                        break;
                    case 'IP Adresi':
                        $parsedData['ip_adresi'] = $value;
                        break;
                    case 'MAC Adresi':
                        // MAC adresini benzersiz anahtar olarak kullanacağız.
                        $parsedData['mac_adresi'] = str_replace('-', ':', $value);
                        break;
                    case 'Isletim Sistemi':
                        $parsedData['isletim_sistemi'] = $value;
                        break;
                    case 'Versiyon':
                        $parsedData['isletim_sistemi_surumu'] = $value;
                        break;
                    case 'Islemci':
                        $parsedData['islemci_modeli'] = $value;
                        break;
                    case 'Anakart Model':
                        // Anakart Üretici bilgisini de ekleyerek tam model oluşturuyoruz.
                        if(isset($parsedData['anakart_modeli'])){
                            $parsedData['anakart_modeli'] .= ' ' . $value;
                        } else {
                            $parsedData['anakart_modeli'] = $value;
                        }
                        break;
                    case 'Anakart Uretici':
                         $parsedData['anakart_modeli'] = $value;
                        break;
                    case 'Sistem Seri No':
                        $parsedData['seri_numarasi'] = $value;
                        break;
                    case 'Toplam Bellek':
                        $parsedData['ram_boyutu'] = $value;
                        break;
                    case 'Sistem Diski':
                        $parsedData['disk_boyutu'] = $value;
                        break;
                    
                    // YENİ EKLENEN KISIM BAŞLANGIÇ
                    case 'Antivirus':
                        $parsedData['antivirus'] = $value;
                        break;
                    case 'Ofis Versiyonu':
                        $parsedData['ofis_versiyonu'] = $value;
                        break;
                    // YENİ EKLENEN KISIM BİTİŞ

                }
            }
        }
        
        // Eğer MAC adresi alınamadıysa veya boşsa, işlemi durdur.
        if (empty($parsedData['mac_adresi']) || $parsedData['mac_adresi'] === 'Bulunamadi') {
            return response()->json(['message' => 'Hata: Gerekli olan MAC adresi bilgisi alınamadı.'], 400);
        }

        // Envanter tarihini ve durumu ekleyelim
        $parsedData['envanter_tarihi'] = now();
        $parsedData['durum'] = 1;

        // updateOrCreate metodu ile MAC adresine göre kaydı bulur, varsa günceller, yoksa yeni oluşturur.
        $envanter = BilgisayarEnvanter::updateOrCreate(
            ['mac_adresi' => $parsedData['mac_adresi']], // Aranacak anahtar
            $parsedData                                 // Kaydedilecek veya güncellenecek veriler
        );

        return response()->json(['message' => 'Envanter başarıyla kaydedildi!', 'data' => $envanter], 200);
    }
    public function store222(Request $request){
        // Gelen isteğin gövdesindeki ham metin verisini alıyoruz.
        $reportContent = $request->getContent();

        // Metni satırlara ayırıyoruz.
        $lines = explode("\n", $reportContent);

        $parsedData = [];
        foreach ($lines as $line) {
            // Sadece ':' içeren satırları işleme alıyoruz.
            if (strpos($line, ':') !== false) {
                // Satırı ilk ':' karakterinden ikiye ayırıyoruz.
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Gelen anahtar kelimelere göre veritabanı sütunlarına eşleştirme yapıyoruz.
                switch ($key) {
                    case 'Bilgisayar Adi':
                        $parsedData['bilgisayar_adi'] = $value;
                        break;
                    case 'Kullanici Adi':
                        $parsedData['kullanici_adi'] = $value;
                        break;
                    case 'Etki Alani':
                        $parsedData['domain'] = $value;
                        break;
                    case 'IP Adresi':
                        $parsedData['ip_adresi'] = $value;
                        break;
                    case 'MAC Adresi':
                        // MAC adresini benzersiz anahtar olarak kullanacağız.
                        $parsedData['mac_adresi'] = str_replace('-', ':', $value);
                        break;
                    case 'Isletim Sistemi':
                        $parsedData['isletim_sistemi'] = $value;
                        break;
                    case 'Versiyon':
                        $parsedData['isletim_sistemi_surumu'] = $value;
                        break;
                    case 'Islemci':
                        $parsedData['islemci_modeli'] = $value;
                        break;
                    case 'Anakart Model':
                        // Anakart Üretici bilgisini de ekleyerek tam model oluşturuyoruz.
                        if(isset($parsedData['anakart_modeli'])){
                            $parsedData['anakart_modeli'] .= ' ' . $value;
                        } else {
                            $parsedData['anakart_modeli'] = $value;
                        }
                        break;
                    case 'Anakart Uretici':
                         $parsedData['anakart_modeli'] = $value;
                        break;
                    case 'Sistem Seri No':
                        $parsedData['seri_numarasi'] = $value;
                        break;
                    case 'Toplam Bellek':
                        $parsedData['ram_boyutu'] = $value;
                        break;
                    case 'Sistem Diski':
                        $parsedData['disk_boyutu'] = $value;
                        break;
                    case 'Antivirus':
                        $parsedData['antivirus'] = $value;
                        break;
                    case 'Ofis Versiyonu':
                        $parsedData['ofis_versiyonu'] = $value;
                        break;
                    
                    // YENİ EKLENEN EKRAN KARTI KISMI
                    case 'Ekran Karti':
                        $parsedData['ekran_karti'] = $value;
                        break;
                    // YENİ EKLENEN KISIM BİTİŞ
                }
            }
        }
        
        // Eğer MAC adresi alınamadıysa veya boşsa, işlemi durdur.
        if (empty($parsedData['mac_adresi']) || $parsedData['mac_adresi'] === 'Bulunamadi') {
            return response()->json(['message' => 'Hata: Gerekli olan MAC adresi bilgisi alınamadı.'], 400);
        }

        // Envanter tarihini ve durumu ekleyelim
        $parsedData['envanter_tarihi'] = now();
        $parsedData['durum'] = 1;

        // updateOrCreate metodu ile MAC adresine göre kaydı bulur, varsa günceller, yoksa yeni oluşturur.
        $envanter = BilgisayarEnvanter::updateOrCreate(
            ['mac_adresi' => $parsedData['mac_adresi']], // Aranacak anahtar
            $parsedData                                 // Kaydedilecek veya güncellenecek veriler
        );

        return response()->json(['message' => 'Envanter başarıyla kaydedildi!', 'data' => $envanter], 200);
    }
    public function store2(Request $request)
    {
        // Gelen isteğin gövdesindeki ham metin verisini alıyoruz.
        $reportContent = $request->getContent();

        // Metni satırlara ayırıyoruz.
        $lines = explode("\n", $reportContent);

        $parsedData = [];
        foreach ($lines as $line) {
            // Sadece ':' içeren satırları işleme alıyoruz.
            if (strpos($line, ':') !== false) {
                // Satırı ilk ':' karakterinden ikiye ayırıyoruz.
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Gelen anahtar kelimelere göre veritabanı sütunlarına eşleştirme yapıyoruz.
                switch ($key) {
                    case 'Bilgisayar Adi':
                        $parsedData['bilgisayar_adi'] = $value;
                        break;
                    case 'Kullanici Adi':
                        $parsedData['kullanici_adi'] = $value;
                        break;
                    case 'Etki Alani':
                        $parsedData['domain'] = $value;
                        break;
                    case 'IP Adresi':
                        $parsedData['ip_adresi'] = $value;
                        break;
                    case 'MAC Adresi':
                        // MAC adresini benzersiz anahtar olarak kullanacağız.
                        $parsedData['mac_adresi'] = str_replace('-', ':', $value);
                        break;
                    case 'Isletim Sistemi':
                        $parsedData['isletim_sistemi'] = $value;
                        break;
                    case 'Versiyon':
                        $parsedData['isletim_sistemi_surumu'] = $value;
                        break;
                    case 'Islemci':
                        $parsedData['islemci_modeli'] = $value;
                        break;
                    // YENİ EKLENEN KISIM
                    case 'Islemci Cekirdek':
                        $parsedData['islemci_cekirdek_sayisi'] = $value;
                        break;
                    // BİTİŞ
                    case 'Anakart Model':
                        // Anakart Üretici bilgisini de ekleyerek tam model oluşturuyoruz.
                        if(isset($parsedData['anakart_modeli'])){
                            $parsedData['anakart_modeli'] .= ' ' . $value;
                        } else {
                            $parsedData['anakart_modeli'] = $value;
                        }
                        break;
                    case 'Anakart Uretici':
                         $parsedData['anakart_modeli'] = $value;
                        break;
                    case 'Sistem Seri No':
                        $parsedData['seri_numarasi'] = $value;
                        break;
                    // YENİ EKLENEN KISIM
                    case 'Bios Surumu':
                        $parsedData['bios_surumu'] = $value;
                        break;
                    // BİTİŞ
                    case 'Toplam Bellek':
                        $parsedData['ram_boyutu'] = $value;
                        break;
                    case 'Sistem Diski':
                        $parsedData['disk_boyutu'] = $value;
                        break;
                    case 'Ekran Karti':
                        $parsedData['ekran_karti'] = $value;
                        break;
                    case 'Antivirus':
                        $parsedData['antivirus'] = $value;
                        break;
                    case 'Ofis Versiyonu':
                        $parsedData['ofis_versiyonu'] = $value;
                        break;
                }
            }
        }
        
        // Eğer MAC adresi alınamadıysa veya boşsa, işlemi durdur.
        if (empty($parsedData['mac_adresi']) || $parsedData['mac_adresi'] === 'Bulunamadi') {
            return response()->json(['message' => 'Hata: Gerekli olan MAC adresi bilgisi alınamadı.'], 400);
        }

        // Envanter tarihini ve durumu ekleyelim
        $parsedData['envanter_tarihi'] = now();
        $parsedData['durum'] = 1;

        // updateOrCreate metodu ile MAC adresine göre kaydı bulur, varsa günceller, yoksa yeni oluşturur.
        $envanter = BilgisayarEnvanter::updateOrCreate(
            ['mac_adresi' => $parsedData['mac_adresi']], // Aranacak anahtar
            $parsedData                                 // Kaydedilecek veya güncellenecek veriler
        );

        return response()->json(['message' => 'Envanter başarıyla kaydedildi!', 'data' => $envanter], 200);
    }
    public function store(Request $request)
    {
        // Gelen isteğin gövdesindeki ham metin verisini alıyoruz.
        $reportContent = $request->getContent();

        // Metni satırlara ayırıyoruz.
        $lines = explode("\n", $reportContent);

        $parsedData = [];
        foreach ($lines as $line) {
            // Sadece ':' içeren satırları işleme alıyoruz.
            if (strpos($line, ':') !== false) {
                // Satırı ilk ':' karakterinden ikiye ayırıyoruz.
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Gelen anahtar kelimelere göre veritabanı sütunlarına eşleştirme yapıyoruz.
                switch ($key) {
                    case 'Bilgisayar Adi':
                        $parsedData['bilgisayar_adi'] = $value;
                        break;
                    case 'Kullanici Adi':
                        $parsedData['kullanici_adi'] = $value;
                        break;
                    case 'Etki Alani':
                        $parsedData['domain'] = $value;
                        break;
                    case 'IP Adresi':
                        $parsedData['ip_adresi'] = $value;
                        break;
                    case 'MAC Adresi':
                        // MAC adresini benzersiz anahtar olarak kullanacağız.
                        $parsedData['mac_adresi'] = str_replace('-', ':', $value);
                        break;
                    case 'Isletim Sistemi':
                        $parsedData['isletim_sistemi'] = $value;
                        break;
                    case 'Versiyon':
                        $parsedData['isletim_sistemi_surumu'] = $value;
                        break;
                    case 'Islemci':
                        $parsedData['islemci_modeli'] = $value;
                        break;
                    case 'Islemci Cekirdek':
                        $parsedData['islemci_cekirdek_sayisi'] = $value;
                        break;
                    case 'Anakart Model':
                        // Anakart Üretici bilgisini de ekleyerek tam model oluşturuyoruz.
                        if(isset($parsedData['anakart_modeli'])){
                            $parsedData['anakart_modeli'] .= ' ' . $value;
                        } else {
                            $parsedData['anakart_modeli'] = $value;
                        }
                        break;
                    case 'Anakart Uretici':
                         $parsedData['anakart_modeli'] = $value;
                        break;
                    case 'Sistem Seri No':
                        $parsedData['seri_numarasi'] = $value;
                        break;
                    case 'Bios Surumu':
                        $parsedData['bios_surumu'] = $value;
                        break;
                    case 'Toplam Bellek':
                        $parsedData['ram_boyutu'] = $value;
                        break;
                    // YENİ EKLENEN KISIM
                    case 'RAM Turu':
                        $parsedData['ram_turu'] = $value;
                        break;
                    // BİTİŞ
                    case 'Sistem Diski':
                        $parsedData['disk_boyutu'] = $value;
                        break;
                    // YENİ EKLENEN KISIM
                    case 'Disk Turu':
                        $parsedData['disk_turu'] = $value;
                        break;
                    // BİTİŞ
                    case 'Ekran Karti':
                        $parsedData['ekran_karti'] = $value;
                        break;
                    case 'Antivirus':
                        $parsedData['antivirus'] = $value;
                        break;
                    case 'Ofis Versiyonu':
                        $parsedData['ofis_versiyonu'] = $value;
                        break;
                }
            }
        }
        
        // Eğer MAC adresi alınamadıysa veya boşsa, işlemi durdur.
        if (empty($parsedData['mac_adresi']) || $parsedData['mac_adresi'] === 'Bulunamadi') {
            return response()->json(['message' => 'Hata: Gerekli olan MAC adresi bilgisi alınamadı.'], 400);
        }

        // Envanter tarihini ve durumu ekleyelim
        $parsedData['envanter_tarihi'] = now();
        $parsedData['durum'] = 1;

        // updateOrCreate metodu ile MAC adresine göre kaydı bulur, varsa günceller, yoksa yeni oluşturur.
        $envanter = BilgisayarEnvanter::updateOrCreate(
            ['mac_adresi' => $parsedData['mac_adresi']], // Aranacak anahtar
            $parsedData                                 // Kaydedilecek veya güncellenecek veriler
        );

        return response()->json(['message' => 'Envanter başarıyla kaydedildi!', 'data' => $envanter], 200);
    }
    public function EnvanterListesiw()
    {
        // Spatie Permission ile yetki kontrolü
        if (!Auth::user()->hasPermissionTo('ayar.menu')) {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz bulunmamaktadır!');
        }

        $title = 'Bilgisayar Envanteri';
        $pagetitle = 'Envanter Listesi';

        if (request()->ajax()) {
            // Tüm envanter verisini çekiyoruz
            $query = BilgisayarEnvanter::select('*');

            return DataTables()->of($query)
                // 'action' sütununa envanter-action.blade.php dosyasını render ediyoruz
                // Her satırın 'id' sini bu dosyaya gönderiyoruz
                ->addColumn('action', 'admin.backend.envanter.bilgisayar-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        // View dosyasını başlıklarla birlikte döndürüyoruz
        return view('admin.backend.envanter.bilgisayar', compact(
            'title',
            'pagetitle'
        ));
    }
    public function BilgisayarListesi()
    {
        if (!Auth::user()->hasPermissionTo('bilgisayar.menu')) {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz bulunmamaktadır!');
        }
    
        $title = 'Bilgisayar Listesi';
        $pagetitle = 'Bilgisayar Envanter Listesi';
    
        if (request()->ajax()) {
            $query = BilgisayarEnvanter::select('*');
    
            return DataTables()->of($query)
                ->addColumn('action', 'admin.backend.envanter.bilgisayar-action')
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('admin.backend.envanter.bilgisayar', compact(
            'title',
            'pagetitle'
        ));
    }

}