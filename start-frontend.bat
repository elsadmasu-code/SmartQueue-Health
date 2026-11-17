@echo off
echo ========================================================
echo  Starting SmartQueue Health (SQHM) - Vue 3 Web Portal
echo ========================================================
cd frontend
node node_modules\vite\bin\vite.js --host=localhost --port=5173
pause
