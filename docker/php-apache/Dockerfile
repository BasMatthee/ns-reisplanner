FROM php:7.4-apache

RUN apt-get update && apt-get install -y

RUN pecl install xdebug
RUN docker-php-ext-install pdo pdo_mysql opcache
RUN a2enmod rewrite headers

ADD xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD vhost.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/ns-reisplanner

RUN mkdir -p var/cache
RUN chown www-data:www-data var/cache
