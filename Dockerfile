# ============================================================
# ClassGate - Dockerfile untuk Render (Web Service, Docker)
# Base: PHP 8.3 + Apache resmi (predictable, gampang didebug)
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

# --- STAGE 2: aplikasi PHP final ---
FROM php:8.3-apache

# Ekstensi PHP yang dibutuhkan Laravel + MySQL + GD (untuk manipulasi gambar AI)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libonig-dev unzip git curl \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# FIX BUG: image dasar ini kadang mengaktifkan 2 modul MPM sekaligus
# (mpm_event + mpm_prefork), yang menyebabkan Apache gagal start dengan
# error "More than one MPM loaded". a2dismod/a2enmod terbukti tidak cukup
# di image ini, jadi kita hapus paksa symlink mods-enabled yang konflik
# dan pasang ulang HANYA mpm_prefork secara langsung di filesystem.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && ls -la /etc/apache2/mods-enabled/ | grep mpm

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Arahkan Apache DocumentRoot ke /public (bukan root project)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

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

# Script startup: migrate, storage:link, cache config, baru jalankan Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
