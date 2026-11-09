
@echo off

echo Starting ARCLUS services...

start "ARCLUS Laravel Server" cmd /k "cd /d C:\xampp\htdocs\argus-lan && C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8000"

start "ARCLUS Face Verification" cmd /k "cd /d C:\xampp\htdocs\argus-lan && face-env\Scripts\python.exe -m uvicorn face_verification_service:app --host 127.0.0.1 --port 9000"

start "ARCLUS Laravel Scheduler" cmd /k "cd /d C:\xampp\htdocs\argus-lan && C:\xampp\php\php.exe artisan schedule:work"

echo All ARCLUS services have been launched.
