# syntax=docker/dockerfile:1.6
FROM php:8.1-fpm-bullseye

# OS libs for common PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libpng-dev libjpeg62-turbo-dev \
    libfreetype6-dev libicu-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# (Optional cache step) if you already have composer.lock, this speeds up builds
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress || true

# App source
COPY . .

# Permissions for runtime dirs
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

CMD ["php-fpm"]
