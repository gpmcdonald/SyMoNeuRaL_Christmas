import time
import os
import psutil
import requests

# -----------------------------
# SyMoNeuRaL Heartbeat Client
# -----------------------------
# Posts to: htdocs/api/heartbeat.php
#
# Token must match $SECRET_TOKEN in heartbeat.php
TOKEN = "2671bfae89d86d8489401f888358e4bfa845ba85f26444e3a3e55e4702547041"

# If you want to override without editing the file:
#   set SYMONEURAL_ENDPOINT=http://symoneural.com/api/heartbeat.php
ENDPOINT = os.environ.get("SYMONEURAL_ENDPOINT", "http://localhost:8080/api/heartbeat.php")

COD_PROCS = {"cod.exe", "codhq.exe", "modernwarfare.exe", "codsp.exe"}
VSC_PROCS = {"code.exe"}

def is_running(names: set[str]) -> bool:
    try:
        for p in psutil.process_iter(["name"]):
            n = (p.info.get("name") or "").lower()
            if n in names:
                return True
    except Exception:
        return False
    return False

def post_heartbeat() -> None:
    payload = {
        "token": TOKEN,
        "cod": is_running(COD_PROCS),
        "vscode": is_running(VSC_PROCS),
        "note": "",
    }
    try:
        requests.post(ENDPOINT, json=payload, timeout=5)
    except Exception:
        pass

if __name__ == "__main__":
    while True:
        post_heartbeat()
        time.sleep(15)
