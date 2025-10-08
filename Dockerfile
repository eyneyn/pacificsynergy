# =========================
# Stage 1: Build PHP + Composer + NPM
# =========================
FROM php:8.3-fpm-alpine AS build

# Install build dependencies + PHP extension libs
RUN apk add --no-cache \
    git unzip curl bash icu-dev oniguruma-dev libzip-dev \
    nodejs npm build-base linux-headers autoconf \
    freetype-dev libpng-dev libjpeg-turbo-dev

# Install PHP extensions (pdo, intl, zip, mbstring, opcache, gd)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql intl mbstring zip opcache gd

# ✅ Copy composer only AFTER extensions are installed
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source code
WORKDIR /var/www/html
COPY . .

# Debug check: ensure gd is installed
RUN php -m | grep gd

# Install dependencies (composer now sees gd)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install && npm run build


# =========================
# Stage 2: Runtime Image
# =========================
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install runtime dependencies
RUN apk add --no-cache bash icu libzip oniguruma freetype libpng libjpeg-turbo \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache

# Copy built app from build stage
COPY --from=build /var/www/html /var/www/html

# Copy PHP extensions config
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080
CMD ["sh", "/usr/local/bin/start.sh"]
