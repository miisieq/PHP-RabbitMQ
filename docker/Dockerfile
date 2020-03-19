FROM php:7.4-fpm

ARG TIMEZONE
ARG DOCKER_USER

RUN \
    # Set timezone.
    ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    \
    # Install Composer.
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir /.composer && chown -R ${DOCKER_USER}:${DOCKER_USER} /.composer \
    \
    # Update and install necessary executables.
    && apt-get update && apt-get install -y \
        zip unzip \
        supervisor \
        wkhtmltopdf \
    \
    # Install PHP sockets extension.
    && docker-php-ext-install sockets \
    \
    # Clear APT & PEAR cache/
    && rm -rf /var/lib/apt/lists/* rm -rf /tmp/pear

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
