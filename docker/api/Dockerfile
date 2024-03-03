FROM php:8.2-apache

RUN apt-get update
RUN apt-get install -y wget vim git zip unzip zlib1g-dev libzip-dev libpng-dev

RUN docker-php-ext-install mysqli pdo_mysql gd zip pcntl exif
RUN docker-php-ext-enable mysqli

RUN a2enmod headers expires rewrite

RUN pecl install xdebug-3.2.2 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copy custom apache config file
COPY apache.config /etc/apache2/apache2.conf

WORKDIR /var/www/html