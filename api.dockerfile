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

####################
# PRODUCTION STAGE #
####################

FROM build AS prod

# Move to project dir and drop root privileges.
WORKDIR /var/www
RUN chown -R www-data: .
USER www-data

# Install RoadRunner.
RUN ./vendor/bin/rr get-binary

# Copy project files.
COPY --chown=www-data: . .

# Define RoadRunner as the command to run when the container starts.
CMD [ "./rr", "serve" ]

####################
# DEV STAGE        #
####################

FROM build AS dev

# Default to first user on Debian based distros.
# Pass in your own UID and GID as environment variables to override.
ARG UID=1000
ARG GID=1000

# Create a user with the same UID and GID as the host user if they don't already exist.
RUN getent group $GID || groupadd -g $GID app
RUN getent passwd $UID || useradd -m -u $UID -g $GID -s /bin/bash -d /app app

COPY --chmod=755 container-config/api/dev-start.sh /dev-start.sh

# Move to project dir and drop root privileges.
WORKDIR /var/www
RUN chown -R $UID:$GID .

# Drop privileges.
USER $UID:$GID

CMD [ "/dev-start.sh" ]
