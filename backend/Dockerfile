# syntax=docker/dockerfile:1
FROM php:8.4-fpm-alpine AS base

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql bcmath intl zip opcache gd exif \
    && apk add --no-cache mysql-client >/dev/null 2>&1 || true

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# ---------- development: সোর্স কোড volume দিয়ে মাউন্ট হয় ----------
FROM base AS dev
ENV APP_ENV=local

# ---------- production: কোড + vendor image-এর ভেতরেই ----------
FROM base AS prod
ENV APP_ENV=production APP_DEBUG=false
COPY docker/php-prod.ini /usr/local/etc/php/conf.d/zz-prod.ini
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
