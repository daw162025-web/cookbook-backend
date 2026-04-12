FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
git \
curl \
libpng-dev \
libonig-dev \
libxml2-dev \
zip \
unzip

# Instalar extensiones PHP necesarias para Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar el proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Comando de inicio
CMD php artisan config:clear && \
php artisan cache:clear && \
php artisan route:clear && \
php artisan migrate --force && \
php artisan serve --host=0.0.0.0 --port=8000
