@echo off
REM ============================================================================
REM  CAI DAT TASK SCHEDULER WINDOWS DE CHAY LARAVEL SCHEDULER
REM ============================================================================
REM
REM  Script nay dang ky 1 task chay "php artisan schedule:run" moi phut.
REM  Day la BAT BUOC de cac job schedule (backup, expired services...) hoat dong.
REM
REM  Cach chay:
REM   1. Click chuot phai vao file nay -> "Run as administrator"
REM   2. Hoac mo CMD bang quyen Admin, dieu huong toi day, chay: install-task-scheduler.bat
REM
REM  De go bo:    schtasks /Delete /TN "TruycuuthongtinScheduler" /F
REM  De xem log:  Task Scheduler -> Task Scheduler Library -> TruycuuthongtinScheduler
REM ============================================================================

SETLOCAL

REM --- Detect PHP path (uu tien Laragon, fallback PATH) ---
SET "PHP_EXE="
IF EXIST "C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe" SET "PHP_EXE=C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe"
IF NOT DEFINED PHP_EXE (
    FOR /F "delims=" %%i IN ('dir /B /AD "C:\laragon\bin\php" 2^>nul') DO (
        IF EXIST "C:\laragon\bin\php\%%i\php.exe" (
            SET "PHP_EXE=C:\laragon\bin\php\%%i\php.exe"
            GOTO :found_php
        )
    )
)
:found_php

IF NOT DEFINED PHP_EXE (
    FOR /F "delims=" %%i IN ('where php 2^>nul') DO (
        SET "PHP_EXE=%%i"
        GOTO :have_php
    )
)
:have_php

IF NOT DEFINED PHP_EXE (
    echo [ERROR] Khong tim thay PHP. Cai Laragon hoac them PHP vao PATH.
    pause
    EXIT /B 1
)

REM --- Project path = parent folder of this script ---
SET "SCRIPT_DIR=%~dp0"
SET "PROJECT_DIR=%SCRIPT_DIR%.."
PUSHD "%PROJECT_DIR%"
SET "PROJECT_DIR=%CD%"
POPD

echo.
echo ============================================
echo  Cai dat Laravel Scheduler vao Task Scheduler
echo ============================================
echo  PHP:     %PHP_EXE%
echo  Project: %PROJECT_DIR%
echo  Task:    TruycuuthongtinScheduler (chay moi phut)
echo ============================================
echo.

REM Tao task chay moi phut, ngay ca khi user khong dang nhap, voi quyen cao nhat
schtasks /Create /TN "TruycuuthongtinScheduler" ^
    /TR "\"%PHP_EXE%\" \"%PROJECT_DIR%\artisan\" schedule:run" ^
    /SC MINUTE /MO 1 ^
    /RU "SYSTEM" ^
    /RL HIGHEST ^
    /F

IF %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] Tao task that bai. Hay chay file nay voi quyen Administrator.
    pause
    EXIT /B 1
)

echo.
echo [OK] Da cai dat thanh cong.
echo.
echo Verify bang lenh: schtasks /Query /TN "TruycuuthongtinScheduler"
echo Xem log chay:     mo Event Viewer -^> Microsoft -^> Windows -^> TaskScheduler
echo.
echo Sau 2-3 phut, kiem tra: cd "%PROJECT_DIR%" ^&^& php artisan schedule:list
echo (cot "Last Ran At" se cap nhat khi task da chay)
echo.

pause
ENDLOCAL
