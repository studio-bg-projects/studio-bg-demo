FROM php:8.3-fpm

RUN apt update

# Composer
RUN apt install -y zip unzip git
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

# PHP Extensions
RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install redis && \
    docker-php-ext-enable redis

RUN apt install -y libzip-dev libpng-dev libjpeg-dev libwebp-dev && \
    docker-php-ext-configure gd --with-jpeg  --with-webp && \
    docker-php-ext-install gd

RUN apt install -y libzip-dev && \
    docker-php-ext-install zip

RUN apt clean && \
    apt autoremove -y && \
    rm -rf /var/lib/apt/lists/*

RUN mkdir -p /tmp-php \
    && chown www-data:www-data /tmp-php \
    && chmod 700 /tmp-php

COPY php.ini /usr/local/etc/php/php.ini

EXPOSE 9000
