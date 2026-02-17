@echo off
rem Turkce karakter destegi ve wmic duzeltmesi icin kod sayfasi ayari
chcp 1254 > nul

set "dosyaAdi=%TEMP%\sistem_raporu_nihai.txt"
rem !!! DIKKAT: Bu adresi kendi calisan sunucu adresinizle degistirin !!!
set "laravel_url=http://10.33.5.201/envanter/kaydet"

rem Her calistirmada eski dosyayi silerek temiz bir baslangic yap
if exist "%dosyaAdi%" del "%dosyaAdi%"

rem RAPOR OLUSTURMA KISMI
echo Bilgisayar Adi   : %COMPUTERNAME% >> "%dosyaAdi%"
echo Kullanici Adi    : %USERNAME% >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_ComputerSystem).Domain"') do (
    echo Etki Alani       : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "try { (Get-NetRoute -DestinationPrefix 0.0.0.0/0 | Sort-Object RouteMetric | Select-Object -First 1 | Get-NetIPConfiguration).IPv4Address.IPAddress } catch { Write-Host 'Bulunamadi' }"') do (
    echo IP Adresi        : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "try { (Get-NetRoute -DestinationPrefix 0.0.0.0/0 | Sort-Object RouteMetric | Select-Object -First 1 | Get-NetAdapter).MacAddress } catch { Write-Host 'Bulunamadi' }"') do (
    echo MAC Adresi       : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_OperatingSystem).Caption"') do echo Isletim Sistemi  : %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_OperatingSystem).Version"') do echo Versiyon         : %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_Processor).Name"') do (
    echo Islemci          : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_Processor).NumberOfCores"') do (
    echo Islemci Cekirdek : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_BaseBoard).Manufacturer"') do echo Anakart Uretici  : %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_BaseBoard).Product"') do echo Anakart Model    : %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_BIOS).SerialNumber"') do echo Sistem Seri No   : %%i >> "%dosyaAdi%"
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_BIOS).SMBIOSBIOSVersion"') do (
    echo Bios Surumu      : %%i >> "%dosyaAdi%"
)
for /f "tokens=1,* delims=:" %%a in ('systeminfo ^| findstr /B /C:"Total Physical Memory" /C:"Toplam Fizsel Bellek"') do (
    for /f "tokens=*" %%c in ("%%b") do echo Toplam Bellek    : %%c >> "%dosyaAdi%"
)
rem --- YENI EKLENEN RAM TURU ---
for /f "delims=" %%i in ('powershell -Command "$memType = (Get-CimInstance Win32_PhysicalMemory | Select-Object -First 1).SMBIOSMemoryType; $typeString = switch ($memType) { 20 {'DDR'} 21 {'DDR2'} 22 {'DDR2 FB-DIMM'} 24 {'DDR3'} 26 {'DDR4'} 30 {'DDR5'} default {'Bilinmiyor'} }; Write-Host $typeString"') do (
    echo RAM Turu         : %%i >> "%dosyaAdi%"
)
rem --- RAM TURU BITIS ---
for /f "delims=" %%i in ('powershell -Command "[math]::Round(((Get-WmiObject Win32_LogicalDisk | Where-Object { $_.DeviceID -eq $env:SystemDrive }).Size) / 1GB, 2)"') do (
    echo Sistem Diski     : %%i GB (%SystemDrive%) >> "%dosyaAdi%"
)
rem --- YENI EKLENEN DISK TURU ---
for /f "delims=" %%i in ('powershell -Command "(Get-PhysicalDisk | Where-Object { $_.BusType -ne 'USB' } | Select-Object -First 1).MediaType"') do (
    echo Disk Turu        : %%i >> "%dosyaAdi%"
)
rem --- DISK TURU BITIS ---
for /f "delims=" %%i in ('powershell -Command "(Get-CimInstance Win32_VideoController).Caption -join '', ''"') do (
    echo Ekran Karti      : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "try { (Get-CimInstance -Namespace \"root\SecurityCenter2\" -ClassName AntiVirusProduct).displayName -join ', ' } catch { Write-Host 'Bulunamadi' }"') do (
    echo Antivirus        : %%i >> "%dosyaAdi%"
)
for /f "delims=" %%i in ('powershell -Command "$officeVersion = 'Bulunamadi'; $c2r = Get-ItemProperty -Path 'HKLM:\SOFTWARE\Microsoft\Office\ClickToRun\Configuration' -Name 'ProductReleaseIds' -ErrorAction SilentlyContinue; if ($c2r) { $officeVersion = 'Microsoft 365/2016+' } else { $outlookPath = (Get-ItemProperty -Path 'HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\App Paths\OUTLOOK.EXE' -ErrorAction SilentlyContinue).'(default)'; if ($outlookPath) { $fileVersion = (Get-Item -Path $outlookPath).VersionInfo.FileVersion; if ($fileVersion -like '16.*') { $officeVersion = 'Office 2016 (MSI)' } elseif ($fileVersion -like '15.*') { $officeVersion = 'Office 2013' } elseif ($fileVersion -like '14.*') { $officeVersion = 'Office 2010' } } }; Write-Host $officeVersion"') do (
    echo Ofis Versiyonu   : %%i >> "%dosyaAdi%"
)


echo Rapor olusturuldu: %dosyaAdi%
echo Veri Laravel'e gonderiliyor...

rem --- GELISTIRILMIS HATA YAKALAMA ILE VERI GONDERME ---
powershell -Command "$ErrorActionPreference = 'Stop'; try { $response = Invoke-RestMethod -Uri '%laravel_url%' -Method Post -Body (Get-Content -Path '%dosyaAdi%' -Raw) -ContentType 'text/plain'; Write-Host 'Basarili:' -ForegroundColor Green; Write-Host $response; } catch { Write-Host '--- HATA DETAYI ---' -ForegroundColor Red; Write-Host $_.Exception.Message -ForegroundColor Yellow; if ($_.Exception.Response) { Write-Host 'HTTP Durum Kodu: ' -NoNewLine -ForegroundColor Yellow; Write-Host $_.Exception.Response.StatusCode.Value__; Write-Host '--- Sunucu Yaniti ---' -ForegroundColor Red; $content = [System.IO.File]::ReadAllText($_.Exception.Response.GetResponseStream()); Write-Host $content; } }"

echo Islem tamamlandi.
rem Gecici rapor dosyasini sil
if exist "%dosyaAdi%" del "%dosyaAdi%"

