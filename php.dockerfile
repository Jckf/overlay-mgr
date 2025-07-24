####################
# DEPS STAGE       #
####################

FROM composer AS deps

WORKDIR /var/www
COPY composer.* .
RUN composer install --ignore-platform-reqs

####################
# BUILD STAGE      #
####################

FROM php:8.3-cli-alpine AS build

RUN docker-php-ext-install pdo pdo_mysql

# Dependencies
COPY --from=deps /var/www/vendor /var/www/vendor

# Move to project dir and drop root privileges.
WORKDIR /var/www
RUN chown -R www-data: .
USER www-data

# Install RoadRunner.
RUN ./vendor/bin/rr get-binary

# Define RoadRunner as the command to run when the container starts.
CMD [ "./rr", "serve" ]

####################
# PRODUCTION STAGE #
####################

FROM build AS prod

# Copy project files.
COPY --chown=www-data: . .
