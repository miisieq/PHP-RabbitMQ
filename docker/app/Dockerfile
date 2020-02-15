FROM php:7.4-fpm

ARG TIMEZONE
ARG DOCKER_USER

RUN \
    # Set timezone
    ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    \
    # Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir /.composer && chown -R ${DOCKER_USER}:${DOCKER_USER} /.composer \
    \
    # Update
    && apt-get update && apt-get install -y \
        zip unzip \
    \
    # Clear APT & PEAR cache
    && rm -rf /var/lib/apt/lists/* rm -rf /tmp/pear

USER ${DOCKER_USER}