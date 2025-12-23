@echo off
setlocal

cd /d "%~dp0"

echo Stopping processes...

for %%F in (symonstat caddy php) do (
  if exist "run\%%F.pid" (
    set /p PID=<"run\%%F.pid"
    echo Killing %%F PID !PID!
    taskkill /PID !PID! /F >nul 2>&1
    del "run\%%F.pid" >nul 2>&1
  )
)

echo Done.
pause