FROM php:8.2-fpm

WORKDIR /var/www/laravel_docker

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
	    libzip-dev \
	    zip \
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
        # docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        # docker-php-ext-install gd mbstring pdo pdo_mysql pdo_pgsql

# RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
#     gd xdebug pdo pdo_pgsql pdo_mysql

# COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
# RUN install-php-extensions gd xdebug pdo pdo_pgsql pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install \
        gd pdo pdo_pgsql pdo_mysql zip \
    && docker-php-ext-enable \
        gd pdo pdo_pgsql pdo_mysql zip
RUN usermod -u 1000 www-data
RUN rm -rf /var/cache/apk/*
#COPY --chown=www-data:www-data . /var/www/laravel_docker
ADD . /var/www/laravel_docker
RUN chown -R www-data:www-data /var/www/laravel_docker
RUN chmod -R 777 /var/www/laravel_docker
RUN composer install

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
