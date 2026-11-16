FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    sqlite-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_sqlite zip intl bcmath opcache

WORKDIR /var/www/html

COPY . .

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
