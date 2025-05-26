FROM php:8.2-fpm

WORKDIR /var/www/laravel_docker

RUN apt-get update

RUN set -eux; \
    apt-get upgrade -y; \
    apt-get install -y\
        libzip-dev \
        zip \
        unzip \
        supervisor \
        git \
        zlib1g-dev \
        curl \
        libmemcached-dev \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        libwebp-dev \
        libxpm-dev \
        libmcrypt-dev \
        libonig-dev; \
        rm -rf /var/lib/apt/lists/*

RUN apt-get install autoconf


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RUN pecl install grpc

RUN docker-php-ext-install \
        gd pdo pdo_pgsql pdo_mysql zip sockets bcmath opcache\
    && docker-php-ext-enable \
        # gd pdo pdo_pgsql pdo_mysql zip sockets grpc bcmath opcache
        gd pdo pdo_pgsql pdo_mysql zip sockets bcmath opcache

RUN usermod -u 1000 www-data
RUN rm -rf /var/cache/apk/*
ADD . /var/www/laravel_docker
RUN chown -R www-data:www-data /var/www/laravel_docker
RUN chmod -R 777 /var/www/laravel_docker
RUN composer install

USER www-data


EXPOSE 9000
CMD ["php-fpm"]



