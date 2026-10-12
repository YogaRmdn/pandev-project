# PanDev - one-click startup (Windows / XAMPP, non-admin)
# Starts local MySQL (XAMPP) if it isn't running, then starts the Laravel dev server.
# Usage:  powershell -ExecutionPolicy Bypass -File .\start-dev.ps1

$ErrorActionPreference = 'SilentlyContinue'

$XAMPP = 'C:\xampp'
$MySQLBin = "$XAMPP\mysql\bin\mysqld.exe"
$Port    = 3306

function Test-Port([int]$p) {
    return (Test-NetConnection -ComputerName 127.0.0.1 -Port $p -WarningAction SilentlyContinue).TcpTestSucceeded
}

Write-Host "=== PanDev Dev Server ===" -ForegroundColor Cyan

# 1) MySQL
if (Test-Port 3306) {
    Write-Host "[1/2] MySQL sudah berjalan di port 3306." -ForegroundColor Green
} else {
    Write-Host "[1/2] Menyalakan MySQL (XAMPP) ..." -ForegroundColor Yellow
    if (-not (Test-Path $MySQLBin)) {
        Write-Host "mysqld.exe tidak ditemukan di $MySQLBin. Periksa lokasi XAMPP." -ForegroundColor Red
        exit 1
    }
    Start-Process -FilePath $MySQLBin -ArgumentList "--defaults-file=$XAMPP\mysql\data\my.ini" -WorkingDirectory $XAMPP -WindowStyle Hidden
    $tries = 0
    while (-not (Test-Port 3306) -and $tries -lt 15) {
        Start-Sleep -Seconds 2
        $tries++
    }
    if (Test-Port 3306) {
        Write-Host "MySQL berjalan." -ForegroundColor Green
    } else {
        Write-Host "MySQL gagal start. Cek C:\xampp\mysql\data\DESKTOP-*-5.err" -ForegroundColor Red
        exit 1
    }
}

# 2) Laravel dev server
if (Test-Port 8000) {
    Write-Host "[2/2] Laravel server sudah berjalan di http://127.0.0.1:8000" -ForegroundColor Green
} else {
    Write-Host "[2/2] Menyalakan Laravel server ..." -ForegroundColor Yellow
    Start-Process -FilePath "php" -ArgumentList "artisan","serve","--host=127.0.0.1","--port=8000" -WorkingDirectory $PSScriptRoot -WindowStyle Hidden
    $tries = 0
    while (-not (Test-Port 8000) -and $tries -lt 15) {
        Start-Sleep -Seconds 2
        $tries++
    }
    if (Test-Port 8000) {
        Write-Host "Laravel server berjalan." -ForegroundColor Green
    } else {
        Write-Host "Laravel server gagal start." -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "Buka:  http://localhost:8000" -ForegroundColor Cyan
Write-Host "Login: adminpandev@pandev.com / password123" -ForegroundColor DarkGray