FROM nginx:latest

COPY container-config/nginx/nginx.conf /etc/nginx/nginx.conf
COPY container-config/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY static /var/www/static
