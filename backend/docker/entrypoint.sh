#!/bin/sh
set -e
cd /var/www/html

if [ "$1" = "php-fpm" ]; then
  [ -f .env ] || cp .env.example .env

  if [ ! -d vendor ]; then
    echo ">> vendor নেই — composer install চলছে..."
    composer install --no-interaction --prefer-dist
  fi

  if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=.\+' .env; then
    php artisan key:generate --force
  fi

  mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache

  if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo ">> ডাটাবেস প্রস্তুত হওয়ার অপেক্ষা..."
    i=0
    until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT")?:3306), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); } catch (Exception $e) { exit(1); }' 2>/dev/null; do
      i=$((i+1)); [ $i -gt 40 ] && echo "ডাটাবেস পাওয়া যায়নি" && exit 1
      sleep 2
    done
    php artisan migrate --force
  fi

  [ -L public/storage ] || php artisan storage:link || true

  if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache && php artisan route:cache
  fi
fi

exec "$@"
