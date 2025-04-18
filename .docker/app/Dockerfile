FROM php:8.2.16-fpm-alpine as fpm_server

ARG APP_ENV="production"

ARG APP_PHP_EXTENSIONS

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions ${APP_PHP_EXTENSIONS} @composer-2.4.4

COPY --chown=www-data . /var/www/html

COPY .docker/app/environment/${APP_ENV}/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY .docker/app/environment/${APP_ENV}/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/html

EXPOSE 9000



FROM fpm_server as scheduler

RUN echo "* * * * * /var/www/html/bin/console log-analytics:import-logs --filePath=/var/www/html/import/logs.log" | crontab -u www-data -

ENTRYPOINT []

CMD ["crond", "-l", "2", "-f"]


FROM fpm_server as supervisor

RUN apk update \
    && apk add --no-cache supervisor

COPY ./.docker/app/supervisord.conf /etc/supervisord.conf
COPY ./.docker/app/supervisor.conf /etc/supervisor.conf

ENTRYPOINT []

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
