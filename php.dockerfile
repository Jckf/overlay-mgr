####################
# DEPS STAGE       #
####################

FROM composer AS deps

WORKDIR /var/www
COPY composer.* .
RUN composer install

####################
# BUILD STAGE      #
####################

FROM php:8.2-fpm AS build

RUN docker-php-ext-install pdo pdo_mysql

# Dependencies
COPY --from=deps /var/www/vendor /var/www/vendor

# Move to project dir and drop root privileges.
WORKDIR /var/www
RUN chown -R www-data: .
USER www-data

####################
# DEV STAGE        #
####################

FROM build AS dev

####################
# PRODUCTION STAGE #
####################

FROM build AS prod

# Copy project files.
COPY --chown=www-data: . .
