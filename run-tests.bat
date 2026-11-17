@echo off
echo ========================================================
echo  Running SmartQueue Health (SQHM) - System Test Suite
echo ========================================================
cd backend
php -d extension=pdo_sqlite -d extension=sqlite3 artisan sqhm:test
pause
