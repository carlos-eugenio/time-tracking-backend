FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libonig-dev \
    && docker-php-ext-install \
        bcmath \
        intl \
        mbstring \
        pdo_mysql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN groupmod -g ${GID} www-data \
    && usermod -u ${UID} -g ${GID} www-data

COPY --chown=www-data:www-data composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

COPY --chown=www-data:www-data . .

RUN composer dump-autoload --optimize

USER www-data

CMD ["php-fpm"]
