@echo off
cd /d "%~dp0"
echo CineGo demo: http://localhost:8000  (nho bat MySQL trong XAMPP va chay: C:\xampp\php\php.exe public\install.php lan dau)
start http://localhost:8000
C:\xampp\php\php.exe -S localhost:8000 -t public public\index.php
