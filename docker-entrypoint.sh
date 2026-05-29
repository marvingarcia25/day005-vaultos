#!/bin/sh
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
fi

# Wait for SQL Server to be ready
echo "Waiting for SQL Server..."
for i in $(seq 1 30); do
  if php artisan migrate --force 2>/dev/null; then
    echo "Database ready."
    break
  fi
  echo "Attempt $i failed, retrying in 5s..."
  sleep 5
done

# Seed only if the units table is empty
UNIT_COUNT=$(php artisan tinker --execute="echo \App\Models\Unit::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$UNIT_COUNT" = "0" ]; then
  php artisan db:seed --force 2>/dev/null || true
fi

php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
