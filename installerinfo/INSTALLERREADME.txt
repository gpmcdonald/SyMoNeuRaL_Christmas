SyMoNeuRaL_Christmas — Caddy + PHP-CGI + Python Backend (Windows)
===============================================================

What this project is
-------------------
This repo runs a small local web app using:
- Caddy = web server (serves /frontend and routes PHP)
- PHP-CGI = executes the PHP endpoints under /frontend/api
- Python backend (backend/symonstat.py) = posts heartbeat to /api/heartbeat.php
- Frontend = status page + wishlist UI

Folder layout (current)
-----------------------
SyMoNeuRaL_Christmas/
  backend/
    symonstat.py
  frontend/
    index.html
    index.php
    api/
      heartbeat.php
      status.php
      wishlist.php
    data/
      heartbeat.json
      wishlist.sqlite
  Caddyfile
  launcher.py
  dist/
    SyMoNeuRaL_Christmas_Launcher.exe        (built by PyInstaller)
  build/
    (PyInstaller build artifacts)
  Legacy_pre_exe/
    start_symoneural.bat
    stop_symoneural.bat
  Optional_Dev_Only/
    start_symoneural_exe_build.bat           (optional helper)
  run/
    (optional runtime PID/log files if used)
  logs/
    (optional logs)

What we installed / configured
------------------------------
1) Caddy
- Installed Caddy for Windows (so `caddy` works in terminal).
- We run it with: `caddy run` from the project root (where the Caddyfile is).

2) PHP (standalone zip)
- Downloaded PHP Windows zip and extracted it to:
  C:\PHP
- Verified:
  php -v
  php-cgi -v
- Added C:\PHP to PATH so `php` and `php-cgi` can be found.

3) Python dependencies
- Python 3.10 in this case.
- Installed modules needed by backend/symonstat.py:
  pip install psutil requests

4) PHP-CGI FastCGI server
- Started PHP FastCGI listener on localhost:
  php-cgi -b 127.0.0.1:9000
- Caddy forwards PHP requests to that FastCGI port.

5) Packaging
- Used PyInstaller to create a launcher EXE:
  python -m pip install pyinstaller
  python -m PyInstaller --onefile --name SyMoNeuRaL_Christmas_Launcher launcher.py
- Output EXE is in:
  dist\SyMoNeuRaL_Christmas_Launcher.exe

How to run (dev/manual way)
---------------------------
Open THREE terminals (PowerShell is fine) and run:

A) Start PHP-CGI (FastCGI):
  php-cgi -b 127.0.0.1:9000

B) Start Caddy (from repo root where Caddyfile is):
  cd C:\Users\Garrett\SyMoNeuRaL_Christmas
  caddy run

C) Start Python backend (from backend folder):
  cd C:\Users\Garrett\SyMoNeuRaL_Christmas\backend
  python symonstat.py

Then open:
  http://localhost:8080

If the page shows "offline (no heartbeat)" then Python backend is not posting.

How to run (recommended way)
----------------------------
Run the EXE:
  dist\SyMoNeuRaL_Christmas_Launcher.exe

This is intended to start:
- php-cgi
- caddy
- backend/symonstat.py

Notes / common issues
---------------------
- “No module named psutil/requests”
  -> Install into the SAME python you’re running:
     python -m pip install psutil requests

- “No input file specified.” in browser
  -> PHP routing/FastCGI isn’t connected.
     Confirm php-cgi is running on 127.0.0.1:9000
     Confirm Caddyfile points php_fastcgi to that address.

- If commands work in one terminal but not another
  -> Close and reopen terminal after PATH changes (or reboot).

About the extra BAT file confusion
----------------------------------
- Legacy_pre_exe/start_symoneural.bat was the original “manual launcher”.
- Optional_Dev_Only/start_symoneural_exe_build.bat is OPTIONAL and only helps rebuild the EXE later.
- The actual EXE build you ran was:
    python -m PyInstaller --onefile --name SyMoNeuRaL_Christmas_Launcher launcher.py
  (so yes: the exe_build.bat was basically “for later convenience”, not required.)

Requirements (if you want a requirements.txt)
---------------------------------------------
Create backend/requirements.txt with:
  psutil
  requests

Install with:
  python -m pip install -r backend\requirements.txt

Ports
-----
- Caddy: http://localhost:8080
- PHP FastCGI: 127.0.0.1:9000

Done.