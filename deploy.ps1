# PanDev - deploy ke Hostinger (panamdeveloper.com) lewat SSH.
#
# Kenapa perlu skrip ini? Pipeline Git Hostinger hanya:
#   1. clone repo          -> kode ikut terupdate otomatis,
#   2. composer install    -> dependency PHP siap,
#   3. Publishing          -> file non-ignored ditimpa, gitignored DIPERTAHANKAN.
# Dua celah yang ditutup di sini:
#   - `npm run build` TIDAK dijalankan (tidak ada Node di server), jadi
#     public/build tetap basi -> aset dibuild lokal lalu di-upload,
#   - bootstrap/cache/* dipertahankan antar deploy, jadi route/config baru
#     belum terbaca -> `php artisan optimize:clear` + `optimize`.
#
# Prasyarat: SSH key sudah terpasang (login tanpa password).
#
# Usage:
#   powershell -ExecutionPolicy Bypass -File .\deploy.ps1
#   powershell -ExecutionPolicy Bypass -File .\deploy.ps1 -SkipBuild   # aset tak berubah
#
# Mengubah server: cukup ubah variabel di bawah.

param(
    [switch]$SkipBuild
)

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

$SshTarget  = 'u384582303@153.92.10.90'
$SshPort    = 65002
$RemotePath = '/home/u384582303/domains/panamdeveloper.com/public_html'
$Site       = 'https://panamdeveloper.com'

function Invoke-Remote([string]$Command) {
    ssh -p $SshPort $SshTarget $Command
}

# Prefix admin itu rahasia (config/admin.php -> ADMIN_PATH). Ambil dari .env
# LOKAL (server memakai nilai yang sama) dan jangan pernah mencetaknya — yang
# ditampilkan hanya status HTTP-nya.
$AdminPath = ''
$EnvFile = Join-Path $PSScriptRoot '.env'
if (Test-Path $EnvFile) {
    $line = Get-Content $EnvFile | Where-Object { $_ -match '^\s*ADMIN_PATH\s*=' } | Select-Object -First 1
    if ($line) {
        $AdminPath = ($line -replace '^\s*ADMIN_PATH\s*=\s*', '').Trim().Trim('"').Trim("'").Trim('/')
    }
}

# Aset hasil build: dibaca dari manifest supaya verifikasi ikut nama file baru.
$AssetCss = ''
$Manifest = Join-Path $PSScriptRoot 'public/build/manifest.json'
if (Test-Path $Manifest) {
    $manifest = Get-Content $Manifest -Raw | ConvertFrom-Json
    $AssetCss = [string] $manifest.'resources/css/app.css'.file
}

Write-Host "=== PanDev Deploy -> Hostinger ===" -ForegroundColor Cyan

# 1) Build aset lokal
if ($SkipBuild) {
    Write-Host "[1/4] Build dilewati (-SkipBuild)." -ForegroundColor DarkGray
} else {
    Write-Host "[1/4] npm run build ..." -ForegroundColor Yellow
    npm run build
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Build gagal." -ForegroundColor Red
        exit 1
    }
}

# 2) Upload public/build ke direktori sementara (build.new)
Write-Host "[2/4] Upload public/build ..." -ForegroundColor Yellow
Invoke-Remote "rm -rf $RemotePath/public/build.new"
scp -P $SshPort -r "public/build" "${SshTarget}:$RemotePath/public/build.new"
if ($LASTEXITCODE -ne 0) {
    Write-Host "Upload gagal." -ForegroundColor Red
    exit 1
}

# 3) Tukar aset (seamless) lalu refresh cache Laravel
#    mv cepat sehingga public/build tidak pernah kosong saat ditukar.
Write-Host "[3/4] Pasang aset & refresh cache ..." -ForegroundColor Yellow
Invoke-Remote "cd $RemotePath && rm -rf public/build.old && mv public/build public/build.old && mv public/build.new public/build && rm -rf public/build.old && php artisan optimize:clear && php artisan optimize"
if ($LASTEXITCODE -ne 0) {
    Write-Host "Langkah remote gagal." -ForegroundColor Red
    exit 1
}

# 4) Verifikasi singkat
Write-Host "[4/4] Verifikasi ..." -ForegroundColor Yellow
$checks = @("echo -n 'home       -> '; curl -sk -o /dev/null -w '%{http_code}\n' $Site/")
if ($AdminPath -ne '') {
    # Path-nya sengaja tidak dicetak (rahasia); hanya statusnya.
    $checks += "echo -n 'admin area -> '; curl -sk -o /dev/null -w '%{http_code}\n' $Site/$AdminPath/login"
}
if ($AssetCss -ne '') {
    $checks += "echo -n 'build css  -> '; curl -sk -o /dev/null -w '%{http_code}\n' $Site/build/$AssetCss"
}
Invoke-Remote ($checks -join '; ')

Write-Host ""
Write-Host "Selesai: $Site" -ForegroundColor Green
