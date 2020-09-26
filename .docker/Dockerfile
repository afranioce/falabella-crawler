FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        libicu-dev \
        libxml2-dev \
        git \
        curl \
        libzip-dev \
        zip \
    && docker-php-ext-install \
        intl \
        dom \
        json \
        pdo \
        pdo_mysql \
        exif \
        zip

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin --filename=composer
