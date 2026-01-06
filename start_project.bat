@echo off
title LoveCrafted Launcher
echo =====================================================
echo Starting LoveCrafted Project...
echo =====================================================

echo.
echo [1/3] Checking dependencies...
call composer install
if %errorlevel% neq 0 (
    echo Composer install failed! Please check your composer setup.
    pause
    exit /b
)

echo.
echo [2/3] Starting PHP Server...
start "LoveCrafted Server" php -S localhost:8000
if %errorlevel% neq 0 (
    echo Failed to start PHP server. Ensure PHP is in your PATH.
    pause
    exit /b
)

echo.
echo [3/3] Opening Browser...
timeout /t 3 >nul
start http://localhost:8000/login.php

echo.
echo Application is running!
echo Do not close the "LoveCrafted Server" window to keep the site active.
echo.
pause
