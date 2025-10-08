# Stage 1: Build PHP dependencies
FROM php:8.3-fpm-alpine AS build

# Install build dependencies + PHP extension libs
RUN apk add --no-cache \
    git unzip curl bash icu-dev oniguruma-dev libzip-dev \
    nodejs npm build-base linux-headers autoconf \
    freetype-dev libpng-dev libjpeg-turbo-dev

# Install PHP extensions (with GD)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql intl mbstring zip opcache gd

# Copy app
WORKDIR /var/www
COPY . .

# Install composer AFTER gd is available
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install && npm run build

# Stage 2: Runtime image
FROM php:8.3-fpm-alpine

WORKDIR /var/www

# Runtime libs for GD + others
RUN apk add --no-cache bash icu libzip oniguruma freetype libpng libjpeg-turbo

# Copy compiled PHP extensions
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy app
COPY --from=build /var/www /var/www

# Startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080
CMD ["sh", "/usr/local/bin/start.sh"]
