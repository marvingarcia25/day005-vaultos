#!/bin/sh
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
fi

# Re-cache config with runtime env vars before any DB calls
php artisan config:cache 2>/dev/null || true

# Create the database if it doesn't exist (MSSQL doesn't auto-create)
php -r "
\$host = getenv('DB_HOST') ?: 'db';
\$port = getenv('DB_PORT') ?: '1433';
\$user = getenv('DB_USERNAME') ?: 'sa';
\$pass = getenv('DB_PASSWORD') ?: '';
\$db   = getenv('DB_DATABASE') ?: 'vaultos';
for (\$i = 0; \$i < 10; \$i++) {
    try {
        \$pdo = new PDO(\"sqlsrv:Server=\$host,\$port;TrustServerCertificate=1\", \$user, \$pass);
        \$pdo->exec(\"IF NOT EXISTS (SELECT name FROM sys.databases WHERE name=N'\$db') CREATE DATABASE [\$db]\");
        echo 'Database ensured.' . PHP_EOL;
        break;
    } catch (Exception \$e) {
        echo 'Waiting for SQL Server... ' . \$e->getMessage() . PHP_EOL;
        sleep(3);
    }
}
"

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
UNIT_COUNT=$(php -r "
require '/var/www/html/vendor/autoload.php';
\$app = require '/var/www/html/bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
echo \App\Models\Unit::count();
" 2>/dev/null || echo "0")
if [ "$UNIT_COUNT" = "0" ]; then
  php artisan db:seed --force 2>/dev/null || true
fi

php artisan route:cache 2>/dev/null || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
