# Use the official PHP image with Laravel dependencies
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev git unzip libpq-dev && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy the Laravel project files into the container
COPY . .

# Install PHP dependencies with Composer
RUN composer install --no-interaction --optimize-autoloader

# Expose port
EXPOSE 9000

# Command to run the PHP-FPM server
CMD ["php-fpm"]
