FROM nginx:latest

COPY config/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY static /var/www/static
