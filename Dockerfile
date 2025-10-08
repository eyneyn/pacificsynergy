# Stage 1: Build PHP dependencies
FROM php:8.3-fpm-alpine AS build

# Install system dependencies
RUN apk add --no-cache \
    git unzip curl bash icu-dev oniguruma-dev libzip-dev \
    nodejs npm build-base linux-headers autoconf

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache

# Copy app files
WORKDIR /var/www
COPY . .

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader

# Install npm dependencies
RUN npm install && npm run build

# Stage 2: Production image
FROM php:8.3-fpm-alpine

WORKDIR /var/www

# Install runtime dependencies
RUN apk add --no-cache bash icu libzip oniguruma

# Copy extensions and vendor from builder
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=build /var/www /var/www

# Copy docker startup scripts
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080
CMD ["sh", "/usr/local/bin/start.sh"]
