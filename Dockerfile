# Use the official PHP image as the base image
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git libzip-dev libpng-dev libicu-dev libssl-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql intl && \
    pecl install redis && \
    docker-php-ext-enable redis

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory inside the container
WORKDIR /var/www

# Copy the existing Laravel project files into the container
COPY . .

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Copy the example environment configuration and set the correct permissions
RUN cp .env.example .env && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Generate Laravel application key
RUN php artisan key:generate

# Expose the application port (typically 80 or 8000)
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]
