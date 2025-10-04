# ======================
# 1. Base stage
# ======================
FROM php:8.3-fpm-alpine AS base

# Install system dependencies
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

# Copy composer files and install dependencies (without dev)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader || true

# Copy only necessary app files (ignore node_modules, tests, docs, etc.)
COPY . .

# Optimize Laravel
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan optimize

# ======================
# 3. Production stage
# ======================
FROM php:8.3-fpm-alpine AS prod

# Copy PHP extensions from base
COPY --from=base /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=base /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy built Laravel app
WORKDIR /var/www/html
COPY --from=build /var/www/html /var/www/html

# Remove dev files & cache
RUN rm -rf node_modules tests storage/logs/* bootstrap/cache/*.php \
    && find . -type f -name "*.md" -delete \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
