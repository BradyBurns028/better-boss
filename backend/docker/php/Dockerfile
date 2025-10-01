FROM php:8.2-fpm

ARG user
ARG uid
ARG working_dir

WORKDIR ${working_dir}

# Install system deps
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    supervisor \
    zip \
    curl \
    unzip \
    git \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g ${uid} ${user} 2>/dev/null || true && \
    id -u ${user} >/dev/null 2>&1 || useradd -m -u ${uid} -g ${user} -s /bin/bash ${user}

RUN sed -ri "s|^user = .*$|user = ${user}|; s|^group = .*$|group = ${user}|" /usr/local/etc/php-fpm.d/www.conf

COPY . ${working_dir}

# Copy Supervisor configuration
COPY docker/php/supervisor/supervisord.conf /etc/supervisord.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader && \
    php artisan key:generate && \
    php artisan config:clear && \
    php artisan config:cache

# Set correct permissions
RUN chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache

# Expose default PHP-FPM port (not HTTP)
EXPOSE 9000

CMD ["supervisord", "-c", "/etc/supervisord.conf"]
