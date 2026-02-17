# PDKS İşlemleri Menüsü – İyileştirme Öneri Listesi

Bu dokümanda **sadece PDKS İşlemleri** menüsü (Bugün, Giriş Çıkış, Geç Gelenler, Erken Çıkanlar, Gelmeyenler, Cihaz Kayıtları, Tüm Hareketler, Mail Raporu Gönder) için hız, teknik, tasarım ve pratik kullanım açısından öneriler yer almaktadır.

---

## 1. Hız (Performans)

| # | Öneri | Açıklama | Öncelik |
|---|--------|----------|---------|
| 1.1 | **Ortak verileri cache’le** | Her sayfada `birim`, `unvan`, `gorev`, `durum`, `izin_turleri` tekrar tekrar DB’den çekiliyor. Bunları `Cache::remember()` veya view composer ile 5–15 dakika cache’leyin (örn. `pdks_sidebar_data`). | Yüksek |
| 1.2 | **DataTables’ta sayfalama limiti** | AJAX’ta tüm sonuçları `get()` ile alıp DataTables’a vermek yerine gerçek server-side sayfalama kullanın. Özellikle Giriş Çıkış’taki tarih aralığı + hafta sonu ekleme mantığı büyük veride yavaşlar; mümkünse sorguyu sayfalı (limit/offset) yapın. | Yüksek |
| 1.3 | **Gereksiz join’leri azaltın** | Listeleme sorgularında sadece tabloda gösterilen alanları `select` edin. Gerekmeyen `pdks_gecis_turu`, `pdks_cihazlar` join’leri sadece filtre için kullanılıyorsa subquery veya exists ile sadeleştirilebilir. | Orta |
| 1.4 | **İndeks kontrolü** | `pdks_cihaz_gecisler.gecis_tarihi`, `pdks_cihaz_gecisler.kart_id`, `personel.personel_birim`, `personel.personel_bolge` üzerinde indeks olduğundan emin olun. Tarih + kart_id bileşik indeks sorgu süresini ciddi düşürür. | Yüksek |
| 1.5 | **Personel/kart listesini lazy yükleme** | Geçiş Ekle / İzin Ekle modallarında tüm personel ve kart listesi sayfa açılışında geliyor. Select2 ile arama tabanlı (search/API) yükleme yapın; böylece ilk sayfa yükü hafifler. | Orta |
| 1.6 | **Export’u kuyruğa alın** | Büyük tarih aralığında Excel export uzun sürebilir. `pdks.giriscikis.export` için job queue (Laravel Queue) kullanıp “Hazır olunca indir” veya mail ile gönderin. | Orta |

---

## 2. Teknik

| # | Öneri | Açıklama | Öncelik |
|---|--------|----------|---------|
| 2.1 | **Controller’ı bölün** | `PdksController` tek dosyada çok büyük. İlgili metodları `PdksBugunController`, `PdksGirisCikisController`, `PdksGecGelenController` vb. veya tek bir `Pdks` namespace altında trait’lere bölün. Bakım ve test kolaylaşır. | Yüksek |
| 2.2 | **Sorgu mantığını service’e taşıyın** | Giriş/çıkış, geç gelen, erken çıkan, gelmeyen listesi sorguları birbirine benziyor. `PdksRaporService` veya `PdksListeService` gibi bir sınıfta `getBugunListesi()`, `getGirisCikisListesi()`, `getGecGelenler()` vb. toplayın; controller sadece request/response ve yetki kontrolü yapsın. | Yüksek |
| 2.3 | **Form Request kullanın** | Geçiş ekleme, izin ekleme gibi POST işlemlerinde validasyonu `PdksGecisEkleRequest`, `IzinMazeretEkleRequest` gibi Form Request sınıflarına taşıyın. Controller daha okunaklı olur. | Orta |
| 2.4 | **Yetki kontrolünü ortaklaştırın** | Her metodda `hasPermissionTo('pdks.bugun')` vb. tekrarlanıyor. Middleware veya controller constructor’da route’a göre tek noktadan yetki kontrolü yapın (örn. `pdks` middleware’i). | Orta |
| 2.5 | **Yönetici / birim filtre mantığını tek yerde toplayın** | `yonetici == 1 => personel_bolge`, değilse `personel_birim` filtreleri birçok yerde tekrarlanıyor. Bunu bir scope veya helper’da (örn. `scopePdksYetki($query)`) toplayın. | Orta |
| 2.6 | **API / rapor için resource sınıfları** | İleride mobil veya harici rapor API’si eklenecekse, JSON çıktıyı Laravel API Resource ile standartlaştırın. Aynı liste mantığı hem web hem API’de kullanılabilir. | Düşük |

---

## 3. Tasarım (UI/UX)

| # | Öneri | Açıklama | Öncelik |
|---|--------|----------|---------|
| 3.1 | **Sidebar menüyü tek dropdown yapın** | PDKS altında her yetki için ayrı `collapse` div’i var; aynı `id="sidebarPdks"` ile birleştirip tek listede tüm linkleri gösterin. Hem HTML sadeleşir hem menü tutarlı görünür. | Yüksek |
| 3.2 | **Tarih seçimini standartlaştırın** | Giriş Çıkış’ta tarih aralığı var; diğer sayfalarda yok. Geç Gelen / Erken Çıkan / Gelmeyen sayfalarına da “Tarih” veya “Tarih aralığı” filtresi ekleyin; varsayılan “bugün” veya “bu hafta” olabilir. | Yüksek |
| 3.3 | **Tablo sütunları tutarlı olsun** | Bugün ve Giriş Çıkış tabloları aynı sütunları (Sıra, Birim, Statu, Personel, Tarih, Giriş, Çıkış, Açıklama) kullanıyor; diğer PDKS sayfalarında da mümkün olduğunca aynı sıra ve isimlendirme kullanılması kullanıcı alışkanlığı için iyi olur. | Orta |
| 3.4 | **Yükleme ve boş durum gösterimi** | DataTables yüklenirken “Yükleniyor…” metni veya skeleton; veri yokken “Bu tarih için kayıt bulunamadı” gibi net bir mesaj gösterin. | Orta |
| 3.5 | **Export butonunu görünür kılın** | Giriş Çıkış sayfasında Excel export varsa, filtre alanının yanına “Excel’e Aktar” butonu ekleyin; kullanıcılar raporu kolay fark etsin. | Orta |
| 3.6 | **Mobil uyum** | Tabloları yatay kaydırmalı (responsive) tutun; kart görünümü veya özet satır eklemek mobilde kullanımı kolaylaştırabilir. | Düşük |

---

## 4. Pratik Kullanım

| # | Öneri | Açıklama | Öncelik |
|---|--------|----------|---------|
| 4.1 | **Varsayılan tarih** | Giriş Çıkış sayfasında varsayılan tarih aralığı “bu hafta” veya “bu ay” olsun; kullanıcı her seferinde tarih seçmek zorunda kalmasın. | Yüksek |
| 4.2 | **Hızlı filtreler** | “Bugün”, “Dün”, “Bu hafta”, “Bu ay” gibi tek tıkla seçilebilir butonlar ekleyin; tarih aralığı seçici ile birlikte kullanılabilir. | Yüksek |
| 4.3 | **Birim filtresi** | Yönetici kullanıcılar için listelerde “Birim” filtresi ekleyin; tek birim seçerek sadece o birimin raporunu görsünler. | Orta |
| 4.4 | **Açıklama / not alanı** | Geç gelen veya erken çıkan kayıtlar için kısa açıklama/not ekleme (modal veya inline) imkânı olursa, raporlama ve takip pratikleşir. | Orta |
| 4.5 | **Kısayol / favori sayfa** | Sık kullanılan sayfa (örn. Bugün veya Giriş Çıkış) dashboard’da kısayol kartı olarak sunulabilir; PDKS menüsüne girmeden tek tıkla erişim sağlanır. | Düşük |
| 4.6 | **Mail Raporu sayfasında önizleme** | “Mail Raporu Gönder” sayfasında, göndermeden önce raporun hangi tarih/birim için hazırlanacağını ve özet bilgiyi (kişi sayısı vb.) gösterin. | Orta |

---

## 5. Özet Öncelik Matrisi

| Kategori | Hemen yapılabilecekler | Kısa vadede | Orta / uzun vadede |
|----------|------------------------|-------------|----------------------|
| **Hız** | İndeks kontrolü (1.4), ortak veriyi cache (1.1) | DataTables sayfalama (1.2), personel/kart lazy load (1.5) | Export queue (1.6) |
| **Teknik** | Yetki middleware (2.4), yönetici/birim scope (2.5) | Controller bölme (2.1), sorgu service (2.2), Form Request (2.3) | API Resource (2.6) |
| **Tasarım** | Sidebar tek dropdown (3.1), tarih filtresi diğer sayfalara (3.2) | Tablo tutarlılığı (3.3), yükleme/boş mesaj (3.4), export butonu (3.5) | Mobil iyileştirme (3.6) |
| **Pratik** | Varsayılan tarih (4.1), hızlı filtre butonları (4.2) | Birim filtresi (4.3), mail önizleme (4.6), açıklama alanı (4.4) | Dashboard kısayol (4.5) |

---

## 6. Dokümantasyon ve Notlar

- Mevcut PDKS sayfaları: **Bugün**, **Giriş Çıkış**, **Geç Gelenler**, **Erken Çıkanlar**, **Gelmeyenler**, **Cihaz Kayıtları**, **Tüm Hareketler**, **Mail Raporu Gönder**.
- Controller: `App\Http\Controllers\Backend\PdksController`.
- View’lar: `resources/views/admin/backend/pdks/`.
- Sidebar: `resources/views/admin/body/sidebar.blade.php` (PDKS menü bloğu).
- Bu öneriler sadece PDKS İşlemleri menüsü ile sınırlıdır; Personel, Cihaz, Kart modülleri bu listenin dışındadır.

Bu liste, geliştirme planlaması ve sprint önceliklendirmesi için referans olarak kullanılabilir.
