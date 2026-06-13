FROM composer:latest AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader
COPY . .
RUN composer dump-autoload --optimize

FROM node:22-slim AS node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-fpm-alpine AS production
RUN apk add --no-cache nginx \
    && apk add --no-cache --virtual .build-deps oniguruma-dev \
    && docker-php-ext-install -j$(nproc) pdo pdo_sqlite mbstring bcmath \
    && apk del .build-deps
COPY --from=composer /app /var/www/html
COPY --from=node /app/public/build /var/www/html/public/build
COPY nginx.conf /etc/nginx/nginx.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
WORKDIR /var/www/html
EXPOSE 8080
ENTRYPOINT ["docker-entrypoint.sh"]
