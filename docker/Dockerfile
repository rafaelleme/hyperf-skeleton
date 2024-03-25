FROM hyperf/hyperf:8.1-alpine-v3.16-swoole as base

# Envs
ARG timezone

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"}

ENV PHPIZE_DEPS="autoconf dpkg-dev dpkg file g++ gcc libc-dev make php81-dev php81-pear postgresql-dev pkgconf re2c pcre-dev pcre2-dev zlib-dev libtool automake"

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS php81-dev php81-pear  \
    && php -m \
    && ln -s /usr/bin/phpize81 /usr/local/bin/phpize \
    && ln -s /usr/bin/php-config81 /usr/local/bin/php-config \
    && cd /tmp \
    && wget --no-check-certificate https://pecl.php.net/get/mongodb-1.14.1.tgz \
    && mkdir -p ext-mongo \
    && tar -xf mongodb-1.14.1.tgz -C ext-mongo \
    && ( \
        cd ext-mongo/mongodb-1.14.1 \
        && ls \
        && phpize \
        && ./configure \
        && make -s -j$(nproc) && make install \
    ) \
    #  ---------- some config ----------
    && cd /etc/php81 \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    && { \
        echo "extension=mongodb.so"; \
        echo "mongo.auto_reconnect = true"; \
        echo "mongo.allow_persistent = On"; \
        echo "mongo.max_persistent = -1"; \
        echo "mongo.max_connections = -1"; \
        echo "mongo.chunk_size = 1024"; \
    } | tee /etc/php81/conf.d/mongodb.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

# Install dependencies with composer
WORKDIR /opt/www
COPY ./composer.* /opt/www/
RUN composer install --no-dev -o --ignore-platform-reqs

# Dev image
FROM base as dev
ENV APP_ENV=dev SCAN_CACHEABLE=(false)
ENTRYPOINT ["php", "/opt/www/bin/hyperf.php"]
CMD [ "server:watch" ]