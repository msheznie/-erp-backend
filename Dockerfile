#Build Frontend
FROM gearsacr.azurecr.io/pbsgears-gears-frontend AS assets-build

#Install Dependencies for backend
FROM php:7.2.34-fpm-alpine AS base

RUN apk add --update zlib-dev libpng-dev libzip-dev $PHPIZE_DEPS
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install exif
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql
RUN pecl install apcu
RUN docker-php-ext-enable apcu


FROM base AS build-fpm
WORKDIR /var/www/html

#copy backend files to container
COPY . /var/www/html

#setup composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#copy frontend dist into backend public folder
RUN rm -rf /var/www/html/public/assets

COPY --from=assets-build /var/www/html/dist/ /var/www/html/public/

RUN ls /var/www/html/public/assets

RUN composer install

RUN cp .env.example .env &&  php artisan passport:keys && php artisan key:generate


FROM build-fpm AS fpm-k8
RUN mkdir -p /app
COPY --from=build-fpm /var/www/html /app

expose 9000
