# PDKS Giriş/Çıkış ve Mükerrer Kayıt Analizi

## Mevcut Durum

- **Cihazlar**: Hem giriş hem çıkış için aynı cihaza okutma yapılıyor.
- **Veri**: `pdks_cihaz_gecisler` tablosunda her okutma = bir satır (kart_id, gecis_tarihi, cihaz_id).
- **Raporlama**: Giriş = o gün **ilk** geçiş (MIN), çıkış = o gün **son** geçiş (MAX). Cihaz tipi kullanılmıyor.
- **Cihaz tipi**: `pdks_cihazlar.cihaz_gecistipi` zaten var: 1=Giriş, 2=Çıkış, 3=Giriş/Çıkış. Şu an raporlarda bu alan **kullanılmıyor**.

## Mükerrer Kayıt Nedenleri

1. **Aynı anda çift okutma**: Personel kartı iki kez okutunca iki satır oluşuyor.
2. **Senkron/manuel tekrar**: Aynı geçiş iki kez eklenebiliyor (sync veya manuel ekleme).
3. **Tek cihazda giriş/çıkış karışıklığı**: Aynı cihazda pek çok okutma olunca “ilk = giriş, son = çıkış” dışındaki okumalar (öğlen çıkış/giriş vb.) raporlarda tek giriş/tek çıkış olarak yansıyor; ek okutmalar “mükerrer” hissi verebiliyor.

---

## Seçenek 1: Tek Cihaz (Giriş + Çıkış) – Mevcut Yapı

**Nasıl çalışır:** Herkes aynı cihaza girerken ve çıkarken okutuyor. Sistem giriş = MIN, çıkış = MAX alıyor.

**Artıları:**
- Tek cihaz, kurulum basit.
- Öğlen çıkış/giriş, ara çıkışlar otomatik olarak “ilk giriş” ve “son çıkış” içinde kalıyor.

**Eksileri:**
- Çift okutma aynı saniyede/dakikada olursa mükerrer satır oluşur.
- “Giriş mi çıkış mı” bilgisi kayıtta yok; sadece zaman sırasına göre yorum yapılıyor.

**Mükerrer önleme (tek cihazda):**
- **Kayıt eklerken**: Aynı kart + aynı cihaz için son X saniye (örn. 60–90 sn) içinde kayıt varsa **yeni kayıt eklenmesin** (veya tek kayıt sayılsın).
- **Raporlama**: Zaten MIN/MAX kullanıldığı için tek giriş/tek çıkış hesaplanıyor; ek olarak aynı dakikadaki çift kayıtları birleştiren bir view/query de yazılabilir.

---

## Seçenek 2: İki Cihaz (Biri Giriş, Biri Çıkış) – Revizyon

**Nasıl çalışır:** Bir cihaz “Giriş”, diğeri “Çıkış” olarak tanımlanır. Personel sabah giriş cihazına, akşam çıkış cihazına okutur.

**Artıları:**
- Giriş ve çıkış **cihaz tipine göre** net ayrılır; mükerrer giriş/çıkış yorumu azalır.
- Raporlarda: Giriş = sadece “Giriş” (ve gerekirse “Giriş/Çıkış”) cihazlardan MIN, çıkış = sadece “Çıkış” (ve “Giriş/Çıkış”) cihazlardan MAX alınabilir.
- Yanlış cihaza okutma (örn. sabah çıkış cihazına okutma) istenirse raporlarda yok sayılabilir veya uyarı üretilebilir.

**Eksileri:**
- İki cihaz gerekir; giriş ve çıkış kapıları/konumları net olmalı.
- Tek cihazlı noktalar için “Giriş/Çıkış” (3) tipi kullanılmaya devam edilebilir; raporlama mantığı buna göre yazıldı.

**Sistem tarafı:**
- Cihaz ekranında “Geçiş Tipi” zaten var: **Giriş**, **Çıkış**, **Giriş/Çıkış**.
- Yapılacak revizyon: Raporlama sorgularında giriş için `cihaz_gecistipi IN (1, 3)`, çıkış için `cihaz_gecistipi IN (2, 3)` kullanmak. Böylece:
  - İki cihaz (1 Giriş + 1 Çıkış) kullanıldığında giriş/çıkış cihaz tipine göre ayrılır.
  - Tek cihaz “Giriş/Çıkış” (3) kaldığında mevcut MIN/MAX davranışı korunur.

---

## Öneri Özeti

| Hedef | Öneri |
|------|--------|
| **Mükerrer satırı azaltmak** | Kayıt eklerken (manuel + varsa cihaz sync) **aynı kart + cihaz için son 60–90 saniye içinde kayıt varsa yeni kayıt eklenmesin**. |
| **Tek cihaz kalsın mı?** | Kalabilir; mükerrer önleme ile çift okutma engellenir, raporlama MIN/MAX ile aynı kalır. |
| **İki cihaz (giriş/çıkış) kullanılsın mı?** | İki cihaz kullanılacaksa: Cihazları “Giriş” ve “Çıkış” olarak ayarlayın; raporlama **cihaz tipine göre** güncellenir (giriş = Giriş/Giriş-Çıkış cihazlardan MIN, çıkış = Çıkış/Giriş-Çıkış cihazlardan MAX). |
| **Hem tek hem iki cihaz** | Mükerrer önleme + cihaz tipine göre giriş/çıkış mantığı birlikte uygulanabilir; tek cihazda tip “Giriş/Çıkış”, iki cihazda “Giriş” ve “Çıkış” seçilir. |

---

## Uygulama Adımları (Kod Tarafı)

1. **Mükerrer önleme**
   - `PdksGecisEkle`: Insert öncesi aynı `kart_id` (+ istenirse `cihaz_id`) için son 90 saniyede kayıt var mı kontrol et; varsa insert yapma, başarılı mesajı dön (veya “Mükerrer kayıt engellendi”).
   - **`App\Services\PdksGecisService`**: Ortak kural bu serviste. `isMukerrerGecis()` ve `insertGecis()` ile aynı kart (+ isteğe bağlı cihaz) için son 90 saniyede kayıt varsa ekleme yapılmaz. `PdksGecisEkle` bu servisi kullanıyor.
   - **Cihaz sync/API**: POST `/pdks/cihaz/gecis` (`CihazGecisKayit`) — `kart_id`, `gecis_tarihi`, `cihaz_id` ile cihazdan gelen geçiş alınır; aynı mükerrer kuralı uygulanır.

2. **Cihaz tipine göre giriş/çıkış (iki cihaz desteği)**
   - `SendDailyReport` ve diğer PDKS raporlarında:
     - `pdks_cihazlar` join edilip `cihaz_gecistipi` kullanılacak.
     - Giriş: `MIN(gecis_tarihi)` koşulu `cihaz_gecistipi IN (1, 3)` (Giriş veya Giriş/Çıkış).
     - Çıkış: `MAX(gecis_tarihi)` koşulu `cihaz_gecistipi IN (2, 3)` (Çıkış veya Giriş/Çıkış).
   - Böylece tek cihaz “Giriş/Çıkış” iken davranış değişmez; iki cihaz kullanıldığında giriş/çıkış net ayrılır.

Bu doküman, mevcut kod incelemesine ve `pdks_cihazlar` / `pdks_cihaz_gecisler` yapısına dayanmaktadır.
