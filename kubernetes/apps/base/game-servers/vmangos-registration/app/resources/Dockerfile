FROM composer:latest AS vendor

WORKDIR /build
COPY app/application/composer.json app/application/composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libxml2-dev \
        libgmp-dev \
        libpng-dev \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" soap gmp gd zip pdo pdo_mysql \
    && a2enmod rewrite \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/*

COPY app/ /var/www/html/
COPY --from=vendor /build/vendor/ /var/www/html/application/vendor/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
