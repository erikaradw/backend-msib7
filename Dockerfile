#gunakan image php 8 dengan apache
FROM php:8-apache

#Aktifkan mod_rewrite di apache
RUN a2enmod rewrite

#setel direktori kerja di dalam kontainer
WORKDIR /app

#salin file file composer
COPY ./composer.json .
COPY ./composer.lock .

#install depedensi PHP
RUN apt-get update &&\
    apt-get install -y git unzip libpq-dev &&\
    docker-php-ext-install pdo pdo_pgsql && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-scripts --no-autoloader && \
    rm -rf var/lib/apt/lists/*

#salin sisa kode ke aplikasi
COPY . .

RUN composer dump-autoload

EXPOSE 8000
