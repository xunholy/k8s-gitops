# Runtime rebuild of the portal image. The application source
# (cmangos-registration) was removed from this repo in 0afff7639, so the
# app tree and composer vendor dir are lifted from the last image built
# from it and only the PHP/Apache layer is rebuilt. The templates and
# config.php are still mounted over the top from the ConfigMap.
FROM ghcr.io/xunholy/cmangos-registration:da24e639b3b8e8366f792423399cb8ef0ca2f4b7 AS app

# PHP >= 8.3.36 fixes php2shell (CVE-2026-109618, CVE-2026-109619).
FROM php:8.3.36-apache@sha256:d89e8b3d2033c3179dcd9999d913670eb12a07410a95e60205c64e7d0fac7aef

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
    && rm -rf /var/lib/apt/lists/* \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY --from=app --chown=www-data:www-data /var/www/html/ /var/www/html/

EXPOSE 80
