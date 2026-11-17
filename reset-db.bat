@echo off
echo ========================================================
echo  Resetting SmartQueue Health (SQHM) Database & Seeders
echo ========================================================
cd backend
php -d extension=pdo_sqlite -d extension=sqlite3 artisan migrate:fresh --seed
echo.
echo Database refreshed and re-seeded with Ethiopian demo hospital data.
pause
