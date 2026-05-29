#!/bin/sh
set -e

if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
fi

php artisan config:cache  2>/dev/null || true
php artisan route:cache   2>/dev/null || true

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
