FROM php:8.2-fpm

# Install system dependencies & PostgreSQL/Zip drivers
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Run build script
RUN chmod +x build.sh && ./build.sh

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000