FROM php:8.2-cli

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Ejecutar migraciones y cachear al inicio
RUN php artisan config:cache && php artisan route:cache

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
