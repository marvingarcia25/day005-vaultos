# =============================================================================
# Stage 1: node-builder — compile Vue 3 assets via Vite
# =============================================================================
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json ./
COPY vite.config.js ./
COPY resources/js ./resources/js

RUN mkdir -p public && npm install && npm run build


# =============================================================================
# Stage 2: composer-builder — install PHP production dependencies
# =============================================================================
FROM composer:2 AS composer-builder

WORKDIR /app

COPY composer.json composer.lock* ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist


# =============================================================================
# Stage 3: final — PHP 8.4 FPM + Nginx + Supervisor (no database drivers)
# =============================================================================
FROM php:8.4-fpm-bullseye AS final

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        nginx \
        supervisor \
    && docker-php-ext-install opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=node-builder    /app/public/build ./public/build
COPY --from=composer-builder /app/vendor      ./vendor

COPY . .

COPY nginx.conf      /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p \
        storage/app \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan config:cache || true

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
