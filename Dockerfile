# ======================
# 1. Base stage
# ======================
FROM php:8.3-fpm-alpine AS base

# Install system/build dependencies
RUN apk add --no-cache \
    git unzip bash \
    autoconf g++ make \
    libzip-dev oniguruma-dev \
    libpng-dev jpeg-dev freetype-dev \
    icu-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis opcache \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
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
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress

# Copy the rest of the application
COPY . .

# Optimize for production
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache


# ======================
# 3. Production stage
# ======================
FROM base AS prod

WORKDIR /var/www/html

# Copy built app from build stage
COPY --from=build /var/www/html /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
