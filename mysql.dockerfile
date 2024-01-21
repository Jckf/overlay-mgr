FROM mysql:8.2

COPY container-config/mysql/setup.sql /docker-entrypoint-initdb.d/
