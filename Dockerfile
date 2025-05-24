# stage 1: build stage
FROM php:8.2-fpm-alpine AS build

# Installing system dependencies and PHP extensions
RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nodejs \
    npm \
    postgresql-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd

# Install composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy necessary files and change permissions
COPY . .
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install PHP and Node.js dependencies
RUN composer install --no-dev --prefer-dist \
    && npm install \
    && npm run build

RUN chown -R www-data:www-data /var/www/html/vendor \
    && chmod -R 775 /var/www/html/vendor

# stage 2: production stage
FROM php:8.3-fpm-alpine

# Install system dependencies, including NGINX and PostgreSQL extensions
RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
    gettext-dev \
    nginx \
    postgresql-dev \
    bash \
    postgresql-client \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && docker-php-ext-install bcmath \
    && docker-php-ext-enable bcmath \
    && docker-php-ext-install exif \
    && docker-php-ext-enable exif \
    && docker-php-ext-install gettext \
    && docker-php-ext-enable gettext \
    && docker-php-ext-install opcache \
    && docker-php-ext-enable opcache \
    && rm -rf /var/cache/apk/*

# Copy files from the build stage
COPY --from=build /var/www/html /var/www/html
COPY ./nginx.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www/html

# Add all folders where files are being stored that require persistence (if needed).
VOLUME ["/var/www/html/storage/app"]

# # Auto-migrate and run services (nginx and php-fpm)
# CMD ["sh", "-c", "until pg_isready -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE; do echo 'Waiting for database...'; sleep 2; done; echo 'Database is ready. Running migrations...'; php artisan migrate --force; nginx -g 'daemon off;' & php-fpm -F"]



# Auto-migrate and run services (nginx and php-fpm)
CMD ["sh", "-c", "nginx && php-fpm"]

# Expose port 8080 for the application
EXPOSE 8080