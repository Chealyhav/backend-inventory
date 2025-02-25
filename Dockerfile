# Use PHP 8.1 FPM image as base
FROM php:8.2-fpm AS base

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy Composer from the official Composer image
FROM base AS composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Switch to non-root user for better security
USER www-data

# Start PHP-FPM server
CMD ["php-fpm"]
