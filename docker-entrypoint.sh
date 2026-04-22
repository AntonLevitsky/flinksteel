#!/bin/sh
set -e

# Use persistent volume for SQLite if available, otherwise use local
DB_PATH="/data/database.sqlite"
LOCAL_DB="database/database.sqlite"

if [ -d "/data" ]; then
    # Fly.io volume is mounted — use it
    if [ ! -f "$DB_PATH" ]; then
        echo "Initializing database on persistent volume..."
        touch "$DB_PATH"
        ln -sf "$DB_PATH" "$LOCAL_DB"
        php artisan migrate --force --seed
    else
        ln -sf "$DB_PATH" "$LOCAL_DB"
        php artisan migrate --force
    fi
else
    # No volume — local ephemeral DB (e.g. during docker build test)
    if [ ! -f "$LOCAL_DB" ]; then
        touch "$LOCAL_DB"
        php artisan migrate --force --seed
    else
        php artisan migrate --force
    fi
fi

# Generate app key if not set
php artisan key:generate --force --no-interaction 2>/dev/null || true

# Cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
