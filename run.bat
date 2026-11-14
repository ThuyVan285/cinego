@echo off
cd /d "%~dp0"
echo CineGo demo: http://localhost:8000  (nho bat MySQL trong XAMPP va chay lan dau: C:\xampp\php\php.exe database\seeders\DatabaseSeeder.php)
start http://localhost:8000
C:\xampp\php\php.exe -S localhost:8000 -t public public\index.php
