#!/bin/sh
set -e

LOCAL_DB="database/database.sqlite"

# Always init fresh DB (works for ephemeral FS like Render, and persistent FS like Fly)
if [ ! -f "$LOCAL_DB" ]; then
    echo "Initializing database..."
    touch "$LOCAL_DB"
    php artisan migrate --force --seed
else
    php artisan migrate --force
fi

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
