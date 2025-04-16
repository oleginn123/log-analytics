FROM nginx:1.20-alpine as web_server

WORKDIR /var/www/html

ARG NGINX_APP_UPSTREAM_CONTAINER=app
ARG NGINX_APP_UPSTREAM_PORT=9000

COPY ./.docker/nginx/nginx.conf /etc/nginx/

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

# Set upstream conf and remove the default conf
RUN echo "upstream app-upstream { server ${NGINX_APP_UPSTREAM_CONTAINER}:${NGINX_APP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf

COPY ./.docker/nginx/sites-enabled /etc/nginx/sites-enabled

COPY --chown=www-data ./public /var/www/html/public
