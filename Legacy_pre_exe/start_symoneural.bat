@echo off
title SyMoNeuRaL Launcher

REM ===== CONFIG =====
set PROJECT_DIR=C:\Users\Garrett\SyMoNeuRaL_Christmas
set BACKEND_DIR=%PROJECT_DIR%\backend
set PHP_DIR=C:\PHP
set PHP_CGI_PORT=127.0.0.1:9000

REM ===== START PHP-CGI =====
echo Starting PHP-CGI...
start "PHP-CGI" cmd /k "%PHP_DIR%\php-cgi.exe -b %PHP_CGI_PORT%"

timeout /t 2 >nul

REM ===== START CADDY =====
echo Starting Caddy...
start "Caddy" cmd /k "cd /d %PROJECT_DIR% && caddy run"

timeout /t 2 >nul

REM ===== START PYTHON BACKEND =====
echo Starting SyMoNeuRaL Backend...
start "SyMoNeuRaL Backend" cmd /k "cd /d %BACKEND_DIR% && python symonstat.py"

echo.
echo SyMoNeuRaL is starting...
echo DO NOT close these windows.
pause