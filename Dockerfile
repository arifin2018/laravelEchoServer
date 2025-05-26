FROM php:8.2-fpm

WORKDIR /var/www/laravel_docker

RUN apt-get update && \
    set -eux; \
    apt-get upgrade -y; \
    apt-get install -y \
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
        libonig-dev \
        autoconf; \
        rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Pastikan ini dijalankan setelah semua dependensi sistem untuk pecl terpenuhi
# RUN pecl install grpc && docker-php-ext-enable grpc

RUN docker-php-ext-install \
        gd pdo pdo_pgsql pdo_mysql zip sockets bcmath opcache\
    && docker-php-ext-enable \
        gd pdo pdo_pgsql pdo_mysql zip sockets bcmath opcache

RUN usermod -u 1000 www-data
# RUN rm -rf /var/cache/apk/* # Hapus ini, tidak relevan untuk Debian base

# PINDAHKAN BARIS INI KE BAWAH!
ADD . /var/www/laravel_docker
RUN chown -R www-data:www-data /var/www/laravel_docker
RUN chmod -R 777 /var/www/laravel_docker # Pertimbangkan untuk memperketat izin ini

# Tambahkan baris ini untuk mengatasi error Git ownership jika ada
RUN git config --global --add safe.directory /var/www/laravel_docker

USER www-data
RUN composer install --no-dev --optimize-autoloader # Saran untuk produksi

EXPOSE 9000
CMD ["php-fpm"]