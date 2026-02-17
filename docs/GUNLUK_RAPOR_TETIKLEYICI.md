# Günlük Rapor Tetikleyici (Windows / WAMP)

Bu dokümanda PDKS günlük raporunun nasıl tetikleneceği açıklanır. Ortam: Windows, WAMP.

## Otomatik tetikleme (önerilen)

Windows'ta cron olmadığı için Laravel Scheduler tek başına çalışmaz. Aşağıdaki yöntemlerden **biri** kullanılmalıdır.

### Seçenek A: rapor.bat + Windows Görev Zamanlayıcı

1. **Görev Zamanlayıcı**'yı açın (taskschd.msc).
2. "Temel Görev Oluştur" ile yeni bir görev ekleyin.
3. **Tetikleyici:** Günlük, saat **20:00** (veya istediğiniz saat).
4. **Eylem:** "Program başlat" seçin.
5. **Program:** `c:\wamp64\www\proje\rapor.bat` (veya projenizin gerçek yolu).
6. **Başlama yeri (isteğe bağlı):** `c:\wamp64\www\proje` — `rapor.bat` zaten kendi dizinine geçtiği için gerekmez; yine de doldurmanız iyi olur.

Bu sayede her gün 20:00'de günlük rapor maili gönderilir.

### Seçenek B: Laravel Scheduler + Görev Zamanlayıcı

Laravel'in tüm zamanlanmış işlerini kullanmak isterseniz:

1. **Görev Zamanlayıcı**'da her **1 dakikada** bir çalışacak görev oluşturun.
2. **Program:** WAMP PHP yolu, örn. `C:\wamp64\bin\php\php8.2.0\php.exe`.
3. **Bağımsız değişkenler:** `artisan schedule:run`
4. **Başlama yeri:** `c:\wamp64\www\proje`

Böylece `app/Console/Kernel.php` içindeki `report:daily` (dailyAt('20:00')) tanımı da çalışır; ileride başka zamanlanmış komut eklerseniz aynı görev hepsini tetikler.

---

## Manuel tetikleme

- **Komut satırı:** Proje dizininde `php artisan report:daily` (bugün) veya geçmiş tarih için: `php artisan report:daily --date=2026-02-10`
- **Batch:** `rapor.bat` dosyasını çift tıklayın veya görevden çağırın. Geçmiş tarih için batch içeriğini `php artisan report:daily --date=YYYY-AA-GG` olarak değiştirebilirsiniz.
- **Admin panel:** PDKS İşlemleri → Günlük Rapor Gönder sayfasında tarih seçip raporu gönderin; tarih boşsa bugünün raporu gider. Geçmiş bir tarih seçerek o güne ait raporu e-posta ile gönderebilirsiniz.

---

## Özet

| Yöntem              | Ne zaman kullanılır                    |
|---------------------|----------------------------------------|
| rapor.bat + Görev   | Sadece günlük raporu otomatik göndermek |
| schedule:run + Görev| Tüm Laravel zamanlamalarını kullanmak   |
| php artisan report:daily | Tek seferlik veya test için       |
