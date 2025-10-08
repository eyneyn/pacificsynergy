#!/bin/sh

# Start PHP-FPM (FastCGI Process Manager) in the background
php-fpm &

# Start Nginx in the foreground (so container keeps running)
nginx -g "daemon off;"
