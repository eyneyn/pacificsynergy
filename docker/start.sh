#!/bin/sh
# Start PHP-FPM and Nginx
php-fpm83 -D
nginx -g 'daemon off;'
