@echo off
rem Turkce karakter destegi ve wmic duzeltmesi icin kod sayfasi ayari
chcp 1254 > nul

set "dosyaAdi=sistem_raporu_nihai.txt"

rem Her calistirmada eski dosyayi silerek temiz bir baslangic yap
if exist "%dosyaAdi%" del "%dosyaAdi%"

echo Tam Kapsamli Sistem Raporu - %date% %time% > "%dosyaAdi%"
echo ======================================================= >> "%dosyaAdi%"
echo. >> "%dosyaAdi%"

echo [TEMEL SISTEM VE KULLANICI BILGILERI] >> "%dosyaAdi%"
echo Bilgisayar Adi: %COMPUTERNAME% >> "%dosyaAdi%"
echo Kullanici Adi  : %USERNAME% >> "%dosyaAdi%"
for /f "tokens=1,* delims==" %%a in ('wmic computersystem get Domain /value') do (
    if not "%%b"=="" echo Etki Alani    : %%b >> "%dosyaAdi%"
)
echo. >> "%dosyaAdi%"

echo [BIRINCIL AG BAGLANTISI] >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "try { ((Get-NetRoute -DestinationPrefix 0.0.0.0/0 | Get-NetIPConfiguration).IPv4Address).IPAddress } catch { Write-Host 'Bulunamadi' }"') do (
    echo IP Adresi : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "try { (Get-NetRoute -DestinationPrefix 0.0.0.0/0 | Get-NetAdapter).MacAddress } catch { Write-Host 'Bulunamadi' }"') do (
    echo MAC Adresi: %%i >> "%dosyaAdi%"
)
echo. >> "%dosyaAdi%"

rem === DEGISIKLIK: ISLETIM SISTEMI BILGISI ARTIK POWERSHELL ILE ALINIYOR ===
echo [ISLETIM SISTEMI] >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_OperatingSystem).Caption"') do echo Isletim Sistemi: %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_OperatingSystem).Version"') do echo Versiyon        : %%i >> "%dosyaAdi%"
echo. >> "%dosyaAdi%"

echo [ISLEMCI (CPU)] >> "%dosyaAdi%"
for /f "tokens=1,* delims==" %%a in ('wmic cpu get Name /value') do (
    if not "%%b"=="" echo Islemci: %%b >> "%dosyaAdi%"
)
echo. >> "%dosyaAdi%"

rem === DEGISIKLIK: ANAKART BILGISI ARTIK POWERSHELL ILE ALINIYOR ===
echo [ANAKART] >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_BaseBoard).Manufacturer"') do echo Uretici: %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_BaseBoard).Product"') do echo Model  : %%i >> "%dosyaAdi%"
echo. >> "%dosyaAdi%"

echo [RAM (BELLEK)] >> "%dosyaAdi%"
systeminfo | findstr /B /C:"Total Physical Memory" /C:"Toplam Fiziksel Bellek" >> "%dosyaAdi%"
echo. >> "%dosyaAdi%"

echo [BAGLI MONITORLER] >> "%dosyaAdi%"
for /f "skip=1 tokens=2,* delims=," %%a in ('wmic desktopmonitor get MonitorManufacturer,Name /format:csv') do (
    echo - Uretici: %%a, Model: %%b >> "%dosyaAdi%"
)
echo. >> "%dosyaAdi%"

echo [SISTEM DISKI (%SystemDrive%)] >> "%dosyaAdi%"
powershell "Get-WmiObject Win32_LogicalDisk -Filter \"DeviceID = '%SystemDrive%'\" | Select-Object DeviceID, @{Name='Bos Alan (GB)';Expression={[math]::Round($_.FreeSpace/1GB, 2)}}, @{Name='Toplam Boyut (GB)';Expression={[math]::Round($_.Size/1GB, 2)}} | Format-Table -AutoSize" >> "%dosyaAdi%"
echo. >> "%dosyaAdi%"

echo Rapor olusturuldu: %dosyaAdi%
echo.
echo Pencereyi kapatmak icin bir tusa basin...
pause >nul