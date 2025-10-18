@echo off
REM ============================================================================
REM Database Backup Script for Media Pro (Windows)
REM
REM This script creates automated backups of the MySQL database on Windows
REM
REM Usage: backup-database.bat
REM ============================================================================

setlocal enabledelayedexpansion

REM Configuration
set APP_NAME=MediaPro
set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

REM Directories
set SCRIPT_DIR=%~dp0
set PROJECT_DIR=%SCRIPT_DIR%..
set BACKUP_DIR=%PROJECT_DIR%\storage\backups
set MYSQL_BIN=C:\xampp\mysql\bin

REM Database credentials (update these or read from .env)
set DB_NAME=socialmedia_manager
set DB_USER=root
set DB_PASS=
set DB_HOST=127.0.0.1
set DB_PORT=3306

REM Backup settings
set BACKUP_FILENAME=%APP_NAME%_backup_%TIMESTAMP%.sql
set RETENTION_DAYS=30

echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║           🗄️  Database Backup Script - Media Pro            ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.

echo ℹ Checking dependencies...

REM Check if mysqldump exists
if not exist "%MYSQL_BIN%\mysqldump.exe" (
    echo ❌ Error: mysqldump.exe not found at %MYSQL_BIN%
    echo Please update MYSQL_BIN path in this script
    pause
    exit /b 1
)

echo ✅ All dependencies are installed

echo ℹ Creating backup directories...

REM Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
    echo ✅ Created backup directory: %BACKUP_DIR%
)

echo ℹ Starting database backup...
echo ℹ Database: %DB_NAME%
echo ℹ Date: %TIMESTAMP%

REM Perform mysqldump
if "%DB_PASS%"=="" (
    REM No password
    "%MYSQL_BIN%\mysqldump.exe" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% %DB_NAME% > "%BACKUP_DIR%\%BACKUP_FILENAME%"
) else (
    REM With password
    "%MYSQL_BIN%\mysqldump.exe" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%BACKUP_DIR%\%BACKUP_FILENAME%"
)

if errorlevel 1 (
    echo ❌ Database backup failed!
    pause
    exit /b 1
)

REM Check if backup file exists and is not empty
if not exist "%BACKUP_DIR%\%BACKUP_FILENAME%" (
    echo ❌ Backup file was not created!
    pause
    exit /b 1
)

for %%A in ("%BACKUP_DIR%\%BACKUP_FILENAME%") do set BACKUP_SIZE=%%~zA

if %BACKUP_SIZE% LSS 1000 (
    echo ❌ Backup file is too small or empty!
    pause
    exit /b 1
)

echo ✅ Database backup created successfully!

echo ℹ Cleaning up old backups (older than %RETENTION_DAYS% days)...

REM Delete backups older than RETENTION_DAYS
forfiles /P "%BACKUP_DIR%" /M "%APP_NAME%_backup_*.sql" /D -%RETENTION_DAYS% /C "cmd /c del @path" 2>nul

if errorlevel 1 (
    echo ℹ No old backups to delete
) else (
    echo ✅ Deleted old backups
)

REM Count total backups
set BACKUP_COUNT=0
for %%F in ("%BACKUP_DIR%\%APP_NAME%_backup_*.sql") do set /a BACKUP_COUNT+=1

echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                    📊 Backup Summary                          ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
echo   Database:        %DB_NAME%
echo   Backup File:     %BACKUP_FILENAME%
echo   Location:        %BACKUP_DIR%
echo   Size:            %BACKUP_SIZE% bytes
echo   Created:         %TIMESTAMP%
echo.
echo   Total Backups:   %BACKUP_COUNT%
echo   Retention:       %RETENTION_DAYS% days
echo.
echo ✅ Backup completed successfully! 🎉
echo.

pause
endlocal
