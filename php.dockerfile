####################
# BUILD STAGE      #
####################

FROM php:8.2-fpm AS build

RUN docker-php-ext-install pdo pdo_mysql

# Install Composer.
COPY container-config/php/getcomposer.sh ./
RUN ./getcomposer.sh && mv composer.phar /usr/local/bin/composer && rm getcomposer.sh

# Move to project dir and drop root privileges.
WORKDIR /var/www
RUN chown -R www-data: .
USER www-data

####################
# DEV STAGE        #
####################

FROM build AS dev

COPY container-config/php/dev-entrypoint.sh /usr/local/bin/docker-dev-entrypoint

ENTRYPOINT ["docker-dev-entrypoint"]

####################
# PRODUCTION STAGE #
####################

FROM build AS prod

# Copy project files.
COPY --chown=www-data: . .

# Install dependencies.
RUN composer install --no-dev --no-interaction --optimize-autoloader
