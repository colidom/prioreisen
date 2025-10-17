FROM php:8.2-cli

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Permitir composer como superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copiar composer files primero para cache
COPY composer.json composer.lock* ./

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar el resto de la aplicación
COPY . .

# Ejecutar scripts post-install
RUN composer dump-autoload --optimize

# Dar permisos
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8080

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan serve --host=0.0.0.0 --port=8080
