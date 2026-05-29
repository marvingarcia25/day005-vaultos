# =============================================================================
# Stage 1: node-builder
# Compiles Vue 3 assets via Vite and outputs the build to /app/public/build
# =============================================================================
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json ./
COPY vite.config.js ./
COPY resources/js ./resources/js

RUN npm install && npm run build


# =============================================================================
# Stage 2: composer-builder
# Installs PHP production dependencies and outputs the vendor directory
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
# Stage 3: final
# Debian-based PHP 8.2 FPM image with Nginx, Supervisor, and MSSQL drivers.
# Microsoft ODBC driver installation requires a Debian/Ubuntu base; Alpine is
# not supported by the official Microsoft packages.microsoft.com repository.
# =============================================================================
FROM php:8.2-fpm-bullseye AS final

WORKDIR /var/www/html

# Install Nginx, Supervisor, and prerequisites for the Microsoft ODBC driver
RUN apt-get update && apt-get install -y \
        nginx \
        supervisor \
        curl \
        gnupg2 \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/11/prod.list \
        > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && docker-php-ext-install opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy compiled Vue assets from the node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Copy Composer-installed vendor directory from the composer-builder stage
COPY --from=composer-builder /app/vendor ./vendor

# Copy all application source files
COPY . .

# Copy Nginx and Supervisor configuration files
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create Laravel storage and cache directories, then assign ownership to www-data
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Pre-warm the config cache. This will fail without a .env file at build time,
# which is expected — environment variables are injected at container runtime.
RUN php artisan config:cache || true

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
