ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install zip \
    intl \
    pdo \
    pdo_mysql \
    && docker-php-ext-enable intl \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && \
    apt-get install -y --no-install-recommends gnupg && \
    curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get update && \
    apt-get install -y --no-install-recommends nodejs

WORKDIR /srv/www

COPY conf.d/app.ini $PHP_INI_DIR/conf.d/app.ini

COPY ../.. .

EXPOSE 9000

CMD ["php-fpm"]