FROM php:8.0.10-fpm-buster

RUN  apt-get update \
     && apt-get install -y \
        libzip-dev \
        zip \
        git

RUN docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html/app
