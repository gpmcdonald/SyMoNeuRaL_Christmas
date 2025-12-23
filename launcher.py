import os, subprocess, time, sys, signal

ROOT = os.path.dirname(os.path.abspath(__file__))

PHP_CGI = "php-cgi"      # or r"C:\PHP\php-cgi.exe"
CADDY   = "caddy"        # or full path to caddy.exe
PYTHON  = sys.executable # current python

PHP_BIND = "127.0.0.1:9000"

procs = []

def start(cmd, cwd=ROOT):
    p = subprocess.Popen(cmd, cwd=cwd)
    procs.append(p)
    return p

def stop_all():
    for p in reversed(procs):
        try:
            p.terminate()
        except Exception:
            pass

def main():
    os.makedirs(os.path.join(ROOT, "logs"), exist_ok=True)

    print("[1/3] Starting php-cgi...")
    start([PHP_CGI, "-b", PHP_BIND])

    time.sleep(0.7)

    print("[2/3] Starting caddy...")
    start([CADDY, "run"], cwd=ROOT)

    time.sleep(0.7)

    print("[3/3] Starting symonstat.py...")
    start([PYTHON, os.path.join("backend", "symonstat.py")], cwd=ROOT)

    print("\nRUNNING:")
    print(" - http://localhost:8080")
    print("Press Ctrl+C to stop.\n")

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\nStopping...")
        stop_all()

if __name__ == "__main__":
    main()