<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TunelJetfanTestTurSeeder extends Seeder
{
    public function run()
    {
        $testTurleri = [
            'Data Kablosu Kısa',
            'Data Kablosu Yerinde Yok',
            'Enerji Kablolarının Pabuçları Yok',
            'Enerji Kablosu Yok',
            'PT-100 Arızalı',
            'Kablo Pabuç Eksik',
            'Kesit Küçültme ile Fan Arası kablosu yok',
            'Kontaktör Arızalı',
            'Kontaktör Yerinde Yok',
            'Bakım Anahtarı Yok',
            'Kontrol Edilecek',
            'Tamamlandı',
            'Pervane Arızalı',
            'Pervane Kontrol Edilecek',
            'Data Kablosu Arızalı',
            'PT-100 Yerinde Yok',
            'Role Arızalı',
            'Role Eksik',
            'Soft Starter Arızalı',
            'Soft Starter Kontrol Edilecek',
            'Trip Yapmakta',
            'Üst Kontaktör Çekiyor',
            'Vibrasyon Sorunu',
            'Devre Kesici Arızası',
            'Sargı Sıcaklık Arızası',
            'Start Almıyor',
            'Titreşim Arızası',
            'Ters Bağlantı',
            'TMŞ Arızası',
            'Elle Kumanda Panosu Sorunlu',
            'Sigorta Atıyor',
        ];

        foreach ($testTurleri as $tur) {
            DB::table('tunel_jetfantesttur')->insert([
                'test_tur'   => $tur,
                'test_statu' => null,
                'test_tarife'=> null,
                'test_durum' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
