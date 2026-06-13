FROM composer:latest AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts
COPY . .
RUN composer install --no-dev --no-interaction --optimize-autoloader

FROM node:22-slim AS node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-fpm-alpine AS production
RUN apk add --no-cache nginx \
    && docker-php-ext-install -j$(nproc) bcmath
COPY --from=composer /app /var/www/html
COPY --from=node /app/public/build /var/www/html/public/build
COPY nginx.conf /etc/nginx/nginx.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
WORKDIR /var/www/html
EXPOSE 8080
ENTRYPOINT ["docker-entrypoint.sh"]
