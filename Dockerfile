FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && composer install --no-dev --optimize-autoloader

EXPOSE 9000
