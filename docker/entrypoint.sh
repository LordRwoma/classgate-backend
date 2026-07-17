#!/bin/bash
set -e

echo "==> Menyiapkan Laravel..."

# PENTING: APP_KEY HARUS sudah di-set sebagai Environment Variable di Railway.
# Container ini TIDAK punya file .env (sengaja di-.dockerignore), jadi
# `artisan key:generate` tidak bisa dipakai di sini - generate di lokal dulu
# lalu tempel hasilnya ke Railway (lihat panduan).
if [ -z "$APP_KEY" ]; then
    echo "!!! APP_KEY belum di-set di Environment Variables Railway. Container akan gagal jalan."
    exit 1
fi

# Buat symlink storage supaya foto profil / gambar AI bisa diakses browser
php artisan storage:link || true

# Cache config & route (mempercepat, wajib untuk production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan migration otomatis setiap kali container start
echo "==> Menjalankan migration..."
php artisan migrate --force

# Railway memberi port lewat env var $PORT (bukan selalu 8080)
RAILWAY_PORT="${PORT:-8080}"
echo "==> Selesai, menjalankan php artisan serve di port ${RAILWAY_PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${RAILWAY_PORT}"
