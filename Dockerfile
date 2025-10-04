# ======================
# 1) Base
# ======================
FROM php:8.3-fpm-alpine AS base

# System/build deps and PHP extensions
RUN apk add --no-cache \
    git unzip bash icu-dev libxml2-dev \
    autoconf g++ make libzip-dev oniguruma-dev \
    libpng-dev jpeg-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis opcache \
    && { \
    echo "opcache.enable=1"; \
    echo "opcache.enable_cli=0"; \
    echo "opcache.validate_timestamps=0"; \
    echo "opcache.memory_consumption=256"; \
    echo "opcache.interned_strings_buffer=16"; \
    echo "opcache.max_accelerated_files=20000"; \
    } > /usr/local/etc/php/conf.d/opcache.ini \
    && rm -rf /tmp/* /var/cache/apk/*

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ======================
# 2) Build deps & vendor
# ======================
FROM base AS build
WORKDIR /var/www/html

# Install PHP deps first (layer caching)
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts --no-dev

# Copy app source
COPY . .

# Optimize autoloader only (no artisan calls in build!)
RUN composer dump-autoload -o

# ======================
# 3) Runtime image
# ======================
FROM base AS prod
WORKDIR /var/www/html

# Bring in app
COPY --from=build /var/www/html /var/www/html

# Writable dirs
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000
# Foreground mode
CMD ["php-fpm", "-F"]
