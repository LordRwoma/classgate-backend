# ============================================================
# ClassGate - Dockerfile untuk Railway (tanpa Apache)
# Base: PHP 8.3 CLI + php artisan serve
# Alasan ganti dari Apache: base image php:8.3-apache di Railway
# terus-menerus error "More than one MPM loaded" walau sudah 2x
# ditambal manual. Daripada terus tambal-sulam Apache, kita pakai
# server bawaan Laravel yang jauh lebih simpel dan tidak punya
# konsep MPM sama sekali - cocok untuk kebutuhan demo/uji coba.
# ============================================================

# --- STAGE 1: build asset frontend (Vite/Tailwind) ---
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources/ resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./
RUN npm run build

# --- STAGE 2: aplikasi PHP final (CLI, tanpa Apache) ---
FROM php:8.3-cli

# Ekstensi PHP yang dibutuhkan Laravel + MySQL + GD (untuk manipulasi gambar AI)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev unzip git curl \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source code project
COPY . .

# Copy hasil build asset dari Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install dependency PHP (tanpa dev dependency, dioptimasi untuk production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permission wajib supaya Laravel bisa nulis ke storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Script startup: migrate, storage:link, cache config, baru jalankan server
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["entrypoint.sh"]
