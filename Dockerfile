FROM php:8.2-fpm

WORKDIR /var/www/laravel_docker

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
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
        rm -rf /var/lib/apt/lists/* \
        # docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        docker-php-ext-install gd mbstring pdo pdo_mysql pdo_pgsql

RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
    gd xdebug pdo pdo_pgsql pdo_mysql

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd xdebug pdo pdo_pgsql pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y \
        libicu-dev \
    && docker-php-ext-install \
        pdo_pgsql pdo \
    && docker-php-ext-enable \
        pdo_pgsql pdo

RUN apt-get update
RUN apt-get install -y libpq-dev
RUN apt-get install -y zlib1g-dev libzip-dev libpng-dev
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libgd-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql

RUN usermod -u 1000 www-data
RUN rm -rf /var/cache/apk/*
COPY --chown=www-data:www-data . /var/www/laravel_docker
USER www-data

EXPOSE 9123
CMD ["php-fpm"]


# FROM php:8.0-fpm

# # COPY wait-for-it.sh /usr/bin/wait-for-it

# # RUN chmod +x /usr/bin/wait-for-it

# RUN apt-get update && \
#     apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat libxml2-dev libpq-dev libzip-dev && \
#     pecl install apcu && \
#     docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
#     docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql && \
#     docker-php-ext-enable apcu pdo_pgsql sodium && \
#     apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# COPY --from=composer /usr/bin/composer /usr/bin/composer

# WORKDIR /var/www/laravel_docker

# CMD composer i -o ; wait-for-it db:5432 -- bin/console doctrine:migrations:migrate ;  php-fpm

# EXPOSE 9000
