SyMoNeuRaL_Christmas
===================

Overview
--------
SyMoNeuRaL_Christmas is a local Windows-based stack that combines:

- Caddy (web server)
- PHP (API endpoints)
- Python backend (process heartbeat + system status)
- A single packaged Windows EXE launcher (via PyInstaller)

The system displays live system status in a browser and is designed
to start cleanly with one click.

Architecture
------------
Browser
  └── Caddy (localhost:8080)
        ├── frontend/index.html
        ├── frontend/index.php
        └── frontend/api/
              ├── status.php
              ├── heartbeat.php
              └── wishlist.php
                    ↑
                    │ (POST JSON heartbeat)
                    │
              backend/symonstat.py
                    ↑
                    │ (started by launcher)
                    │
        SyMoNeuRaL_Christmas_Launcher.exe

Folder Structure
----------------
SyMoNeuRaL_Christmas/
│
├── backend/
│   └── symonstat.py            # Python heartbeat + process monitor
│
├── frontend/
│   ├── index.html              # UI
│   ├── index.php               # PHP entry
│   ├── api/
│   │   ├── heartbeat.php       # Receives heartbeat POSTs
│   │   ├── status.php          # Serves latest status
│   │   └── wishlist.php
│   └── data/
│       └── heartbeat.json      # Shared state file
│
├── dist/
│   └── SyMoNeuRaL_Christmas_Launcher.exe
│
├── build/                      # PyInstaller build artifacts
│
├── Legacy_pre_exe/
│   ├── start_symoneural.bat    # Old manual startup (kept for reference)
│   └── stop_symoneural.bat
│
├── Optional_Dev_Only/
│   └── start_symoneural_exe_build.bat
│
├── logs/
│
├── Caddyfile
├── launcher.py                 # Python launcher used to build EXE
├── SyMoNeuRaL_Christmas_Launcher.spec
└── README.txt


What Each Component Does
------------------------

Caddy
-----
- Serves frontend files
- Proxies PHP requests to php-cgi
- Runs on http://localhost:8080

PHP
---
- Handles API endpoints under /api/
- Validates secret token
- Writes heartbeat data to JSON
- Reads status for frontend polling

Python (symonstat.py)
---------------------
- Monitors running processes (COD, VS Code, etc.)
- Sends POST heartbeat every few seconds to:
  http://localhost:8080/api/heartbeat.php
- Uses:
  - psutil
  - requests

Launcher EXE
------------
- Starts PHP-CGI
- Starts Caddy
- Starts Python backend
- Acts as the single-click entry point

Installation Steps (What Was Done)
----------------------------------

1) Install Python 3.10
   - https://www.python.org/
   - Ensure python is on PATH

2) Install Python dependencies

pip install psutil requests pyinstaller

3) Install PHP (Windows build)
- Extract PHP to: C:\PHP
- Ensure php.exe and php-cgi.exe are available

4) Install Caddy
- https://caddyserver.com/
- Ensure caddy.exe is on PATH

5) Verify PHP-CGI

php -v
php-cgi -b 127.0.0.1:9000

6) Verify Python backend

cd backend
python symonstat.py

7) Verify site
Open:
http://localhost:8080

Building the EXE (What Actually Matters)
----------------------------------------

The EXE was built directly with PyInstaller using launcher.py.

Command used:

python -m PyInstaller –onefile –name SyMoNeuRaL_Christmas_Launcher launcher.pyinstaller

dist/SyMoNeuRaL_Christmas_Launcher.exe

IMPORTANT:
----------
- The EXE does NOT embed Caddy or PHP binaries
- It EXPECTS them to exist on the system
- The EXE simply orchestrates startup

About the BAT Files
-------------------

Legacy_pre_exe/start_symoneural.bat
- Original manual startup
- Kept only as reference
- NOT used by the EXE

Optional_Dev_Only/start_symoneural_exe_build.bat
- Convenience helper to rebuild EXE
- Optional
- Safe to delete if not rebuilding

The EXE build does NOT depend on either BAT file.

Runtime Usage
-------------

To run the system:

dist/SyMoNeuRaL_Christmas_Launcher.exe

Then open:

http://localhost:8080

Dev Notes
---------
- heartbeat.json is the single shared state file
- PHP and Python must agree on SECRET_TOKEN
- Python runs continuously until closed
- Caddy auto-reloads config if changed

This structure is intentional and stable.