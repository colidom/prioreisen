FROM php:8.2-fpm

# Instala dependencias
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crea directorio de trabajo
WORKDIR /var/www

# Copia archivos
COPY . .

# Instala dependencias de Laravel
RUN composer install

# Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
