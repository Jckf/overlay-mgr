#!/usr/bin/env bash

set -e

composer install --prefer-dist --no-interaction --optimize-autoloader

php-fpm
