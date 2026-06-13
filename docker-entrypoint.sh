#!/bin/sh
set -e

sed -i "s/__PORT__/${PORT:-8080}/g" /etc/nginx/nginx.conf

chmod -R 775 storage bootstrap/cache

cp .env.example .env
php artisan key:generate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php-fpm -D

exec nginx -g 'daemon off;'
