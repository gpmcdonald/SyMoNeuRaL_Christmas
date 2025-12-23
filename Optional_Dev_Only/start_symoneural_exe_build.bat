@echo off
setlocal enabledelayedexpansion

REM === Go to repo root (this script's folder)
cd /d "%~dp0"

REM === CONFIG (edit if needed)
set "PHP_CGI=php-cgi"
set "CADDY=caddy"
set "PYTHON=python"

set "PHP_BIND=127.0.0.1:9000"

REM === Make dirs
if not exist "logs" mkdir logs
if not exist "run" mkdir run

echo.
echo [1/3] Starting PHP-CGI on %PHP_BIND% ...
powershell -NoProfile -Command ^
  "$p = Start-Process -FilePath '%PHP_CGI%' -ArgumentList '-b %PHP_BIND%' -PassThru -WindowStyle Hidden; ^
   $p.Id | Out-File -Encoding ascii 'run\php.pid'; ^
   'PHP PID=' + $p.Id"
timeout /t 1 >nul

echo.
echo [2/3] Starting Caddy ...
powershell -NoProfile -Command ^
  "$p = Start-Process -FilePath '%CADDY%' -ArgumentList 'run' -WorkingDirectory '%CD%' -PassThru; ^
   $p.Id | Out-File -Encoding ascii 'run\caddy.pid'; ^
   'CADDY PID=' + $p.Id"
timeout /t 1 >nul

echo.
echo [3/3] Starting Python backend (backend\symonstat.py) ...
powershell -NoProfile -Command ^
  "$p = Start-Process -FilePath '%PYTHON%' -ArgumentList 'backend\symonstat.py' -WorkingDirectory '%CD%' -PassThru; ^
   $p.Id | Out-File -Encoding ascii 'run\symonstat.pid'; ^
   'PY PID=' + $p.Id"

echo.
echo DONE.
echo - Site: http://localhost:8080
echo - Status API: http://localhost:8080/api/status.php
echo.
pause