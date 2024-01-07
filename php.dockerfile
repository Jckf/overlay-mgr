####################
# BUILD STAGE      #
####################

FROM ubuntu:23.10 AS build

RUN apt update && apt install -y php8.2-cli composer

WORKDIR /var/www

RUN chown -R www-data: .

COPY composer.* ./

USER www-data

RUN composer install

####################
# PRODUCTION STAGE #
####################

FROM php:8.2-fpm AS prod

RUN docker-php-ext-install pdo pdo_mysql

#COPY config/php.ini /etc/php/

WORKDIR /var/www

RUN chown -R www-data: .

COPY src ./

COPY --from=build /var/www/vendor /var/www/vendor
