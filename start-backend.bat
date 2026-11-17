@echo off
echo ========================================================
echo  Starting SmartQueue Health (SQHM) - Laravel Backend API
echo ========================================================
cd backend
php -d extension=pdo_sqlite -d extension=sqlite3 artisan serve --host=127.0.0.1 --port=8000
pause
