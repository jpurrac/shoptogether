#!/usr/bin/env bash
set -e

# Crear proyecto Laravel si no existe
if [ ! -f "artisan" ]; then
  echo "Laravel project not found, creating..."
  composer create-project laravel/laravel /tmp/laravel
  composer require laravel/sanctum
  cp -r /tmp/laravel/. /var/www/html/
  rm -rf /tmp/laravel
  echo "Laravel project created."
fi

HOST=${DB_HOST:-mysql}
PORT=${DB_PORT:-3306}
MAX_RETRIES=30
i=0

echo "Waiting for ${HOST}:${PORT}..."
until nc -z $HOST $PORT; do
  i=$((i+1))
  if [ $i -ge $MAX_RETRIES ]; then
    echo "Timeout waiting for DB"
    exit 1
  fi
  sleep 2
done

echo "DB is available."

if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
else
  echo "APP_KEY already exists, skipping..."
fi

# Migraciones
echo "Running migrations..."
php artisan migrate --force

exec "$@"