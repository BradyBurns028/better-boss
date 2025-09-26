FROM php:8.2-fpm

WORKDIR /var/www/backend

# Install system deps
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY . /var/www/backend

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader && \
    php artisan key:generate && \
    php artisan config:clear && \
    php artisan config:cache

# Set correct permissions
RUN chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache

# Expose default PHP-FPM port (not HTTP)
EXPOSE 9000

CMD ["php-fpm"]
