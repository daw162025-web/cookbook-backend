FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN mkdir -p bootstrap/cache storage/framework/sessions \
    storage/framework/views storage/framework/cache storage/logs \
    && chmod -R 777 bootstrap/cache storage

RUN composer install --optimize-autoloader --no-dev

COPY docker/nginx.conf /etc/nginx/sites-available/default

EXPOSE 8080

# Script de inicio que mantiene el proceso vivo
CMD bash -c "set -e && \
    echo '=== Clearing config ===' && \
    php artisan config:clear && \
    echo '=== Running migrations ===' && \
    php artisan migrate --force && \
    echo '=== Starting PHP-FPM ===' && \
    php-fpm -D && \
    echo '=== Starting Nginx ===' && \
    nginx -g 'daemon off;'"
