@echo off
cd /d "%~dp0"

echo Laravel cache temizleniyor...

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

echo Tüm cache temizlendi!
exit
