FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install sockets

RUN docker-php-ext-install bcmath

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install 


CMD ["php-fpm", "-F"]