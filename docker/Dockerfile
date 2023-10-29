FROM php:8.2-apache

RUN apt-get update
RUN apt-get install -y wget vim git zip unzip zlib1g-dev libzip-dev libpng-dev

RUN apt-get update && apt-get install -y \
    libssh2-1 \
    && pecl install ssh2-1.3.1 \
    && docker-php-ext-enable ssh2 \
    && docker-php-ext-install sockets \

RUN docker-php-ext-install mysqli pdo_mysql gd zip pcntl exif
RUN docker-php-ext-enable mysqli

RUN a2enmod headers expires rewrite

RUN pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html