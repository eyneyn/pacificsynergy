# ------------------------------------
# Stage 1: Build dependencies & assets
# ------------------------------------
FROM php:8.3-fpm-alpine AS build

# System dependencies
RUN apk add --no-cache \
    git unzip curl bash icu-dev oniguruma-dev libzip-dev \
    nodejs npm

# PHP extensions (Laravel 12 requires intl, mbstring, etc.)
RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP deps (no dev, optimized for prod)
RUN composer install --no-dev --optimize-autoloader

# Build front-end assets with Vite
RUN npm ci && npm run build

# ------------------------------------
# Stage 2: Runtime container
# ------------------------------------
FROM nginx:1.27-alpine

# Add PHP-FPM 8.3 runtime
RUN apk add --no-cache php83 php83-fpm php83-opcache php83-session \
    php83-pdo_mysql php83-intl php83-mbstring php83-zip

WORKDIR /var/www/html

# Copy built Laravel app from build stage
COPY --from=build /var/www/html /var/www/html

# Configure Nginx
RUN rm /etc/nginx/conf.d/default.conf
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Start script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Laravel storage & cache permissions
RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
    && chmod -R 0777 storage bootstrap/cache

EXPOSE 8080
CMD ["/start.sh"]
