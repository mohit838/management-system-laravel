# ======================
# 1. Base stage
# ======================
FROM php:8.3-fpm-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git unzip bash shadow \
    autoconf g++ make \
    libzip-dev oniguruma-dev \
    libpng-dev jpeg-dev freetype-dev \
    icu-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis opcache \
    && rm -rf /tmp/* /var/cache/apk/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


# ======================
# 2. Build stage
# ======================
FROM base AS build

WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader || true

# Copy application files
COPY . .

# Make sure storage and bootstrap/cache exist
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chmod -R 755 storage bootstrap/cache

# Clean old caches
RUN rm -f bootstrap/cache/*.php


# ======================
# 3. Production stage
# ======================
FROM base AS prod

WORKDIR /var/www/html

# Copy app from build
COPY --from=build /var/www/html /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

EXPOSE 9000

# Run artisan optimizations when container starts
CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan optimize && php-fpm"]
