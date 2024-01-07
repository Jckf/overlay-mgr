FROM mysql:8.2

COPY config/mysql/setup.sql /docker-entrypoint-initdb.d/setup.sql
