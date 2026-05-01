@echo off
REM ============================================================================
REM  KHOI DONG TELEGRAM BOT (long polling)
REM ============================================================================
REM  Cach dung: double-click file nay.
REM  Cua so CMD se mo va bot bat dau lang nghe.
REM  De dung: dong cua so hoac Ctrl+C.
REM ============================================================================

SETLOCAL

SET "SCRIPT_DIR=%~dp0"
SET "PROJECT_DIR=%SCRIPT_DIR%.."
PUSHD "%PROJECT_DIR%"

REM Detect PHP
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
    SET "PHP_EXE=php"
)

echo ============================================
echo  Telegram Bot Listener
echo  Project: %CD%
echo  PHP:     %PHP_EXE%
echo ============================================
echo.

"%PHP_EXE%" artisan telegram:listen

echo.
echo Bot da dung. Nhan phim bat ky de dong.
pause >nul

POPD
ENDLOCAL
