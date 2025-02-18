# Use the official PHP image as the base image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory in the container
WORKDIR /var/www

# Copy the Laravel project into the container
COPY . .

# Install Laravel dependencies using Composer
RUN composer install --no-scripts --no-autoloader

# Set permissions for storage and cache directories (optional but recommended)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose the PHP-FPM port
EXPOSE 9000

# Start PHP-FPM when the container starts
CMD ["php-fpm"]
