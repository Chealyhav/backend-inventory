# ======================
# Stage 1: Build Stage
# ======================
FROM php:8.2-fpm-alpine AS build

# Install PHP dependencies
RUN apk add --no-cache \
    zip libzip-dev freetype freetype-dev libjpeg-turbo libjpeg-turbo-dev libpng libpng-dev \
    nodejs npm postgresql-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Install PHP and Node dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && npm install && npm run build

# ======================
# Stage 2: Production
# ======================
FROM php:8.2-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    zip libzip-dev freetype freetype-dev libjpeg-turbo libjpeg-turbo-dev libpng libpng-dev \
    oniguruma-dev gettext-dev postgresql-dev bash postgresql-client \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql bcmath exif gettext opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Copy built application from build stage
COPY --from=build /var/www/html /var/www/html

# Set working directory
WORKDIR /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html


# Expose port used by PHP-FPM
EXPOSE 9000

# Run PHP-FPM
CMD ["php-fpm"]
