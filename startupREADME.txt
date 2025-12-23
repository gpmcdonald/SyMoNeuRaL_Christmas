SyMoNeuRaL Christmas – Local → Public Deployment Steps
=====================================================

This document describes the VERIFIED working procedure to run
SyMoNeuRaL Christmas locally and expose it securely to the internet
using Cloudflare Tunnel (no port forwarding).

-----------------------------------------------------
SYSTEM REQUIREMENTS
-----------------------------------------------------
- Windows 10 / 11
- Python 3.10+
- PHP (CGI enabled)
- Caddy
- Cloudflare account + domain (symoneural.com)
- cloudflared (Cloudflare Tunnel client)

-----------------------------------------------------
PROJECT STRUCTURE (FINAL)
-----------------------------------------------------
SyMoNeuRaL_Christmas/
│
├─ backend/
│   └─ symonstat.py
│
├─ frontend/
│   ├─ index.html
│   ├─ index.php
│   ├─ api/
│   │   ├─ status.php
│   │   ├─ heartbeat.php
│   │   └─ wishlist.php
│   └─ data/
│
├─ Caddyfile
├─ launcher.py
├─ README.txt
└─ logs/

-----------------------------------------------------
STEP 1 – VERIFY LOCAL BACKEND (PYTHON)
-----------------------------------------------------
Open PowerShell or VS Code terminal:

cd C:\Users\Garrett\SyMoNeuRaL_Christmas\backend
python symonstat.py

Expected:
- Script runs with no errors
- heartbeat.json updates periodically
- API endpoints respond locally

-----------------------------------------------------
STEP 2 – VERIFY LOCAL FRONTEND (CADDY + PHP)
-----------------------------------------------------
Caddyfile (working config):

:8080 {
    root * "C:\Users\Garrett\SyMoNeuRaL_Christmas\frontend"
    php_fastcgi 127.0.0.1:9000
    file_server
}

Start PHP CGI (example):

php-cgi -b 127.0.0.1:9000

Start Caddy:

cd C:\Users\Garrett\SyMoNeuRaL_Christmas
caddy run

Verify locally in browser:
http://localhost:8080

-----------------------------------------------------
STEP 3 – INSTALL CLOUDFLARED
-----------------------------------------------------
Run as Administrator (PowerShell):

winget install Cloudflare.cloudflared

Close and reopen PowerShell after install.

-----------------------------------------------------
STEP 4 – AUTHENTICATE CLOUDFLARE
-----------------------------------------------------
cloudflared tunnel login

- Browser opens
- Log into Cloudflare
- Select symoneural.com
- cert.pem is saved automatically

-----------------------------------------------------
STEP 5 – CREATE TUNNEL
-----------------------------------------------------
cloudflared tunnel create symoneural-christmas

This creates:
C:\Users\Garrett\.cloudflared\symoneural-christmas.json

-----------------------------------------------------
STEP 6 – CREATE DNS ROUTE (SUBDOMAIN)
-----------------------------------------------------
IMPORTANT:
DO NOT route the root domain again if it already exists.

Use a subdomain instead:

cloudflared tunnel route dns symoneural-christmas christmas.symoneural.com

-----------------------------------------------------
STEP 7 – CONFIGURE CLOUDFLARED
-----------------------------------------------------
Edit file:
C:\Users\Garrett\.cloudflared\config.yml

Correct working config:

tunnel: symoneural-christmas
credentials-file: C:\Users\Garrett\.cloudflared\symoneural-christmas.json

ingress:
  - hostname: christmas.symoneural.com
    service: http://localhost:8080
  - service: http_status:404

-----------------------------------------------------
STEP 8 – START SERVICES (ORDER MATTERS)
-----------------------------------------------------

1) Start Python backend
   cd backend
   python symonstat.py

2) Start PHP CGI
   php-cgi -b 127.0.0.1:9000

3) Start Caddy
   caddy run

4) Start Cloudflare Tunnel
   cloudflared tunnel run symoneural-christmas

-----------------------------------------------------
STEP 9 – VERIFY
-----------------------------------------------------
Local:
http://localhost:8080

Public:
https://christmas.symoneural.com

If Cloudflare shows:
- Browser Working
- Cloudflare Working
- Host Error ❌

→ One of the local services is not running (Python, PHP, or Caddy).

-----------------------------------------------------
COMMON FIXES
-----------------------------------------------------
- If nothing loads:
  taskkill /F /IM python.exe
  taskkill /F /IM caddy.exe
  taskkill /F /IM php-cgi.exe

Then restart in correct order.

- If port 8080 is refused:
  netstat -ano | findstr :8080

-----------------------------------------------------
STATUS: VERIFIED WORKING
-----------------------------------------------------
✔ Local backend running
✔ Local frontend serving
✔ Cloudflare Tunnel connected
✔ Public HTTPS access working
✔ No port forwarding required
✔ Zero exposed inbound firewall rules

-----------------------------------------------------
END
-----------------------------------------------------