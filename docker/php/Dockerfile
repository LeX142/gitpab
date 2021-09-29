FROM composer:2 as composer

FROM php:7.4-fpm

WORKDIR /var/www/html

# Install packages
RUN apt-get update -y \
    && apt-get install -y \
        curl git zip bzip2 wget libmcrypt-dev cron mc \
        libpq-dev libxml2-dev libexif-dev pkg-config \
        libzip-dev libz-dev libbz2-dev libmemcached-dev libtidy-dev \
    && pecl install -o -f mcrypt \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install pdo_pgsql zip pcntl intl\
    && rm -rf /tmp/pear

RUN touch /var/log/cron.log

# composer install: https://hub.docker.com/_/composer?tab=description#php-extensions
COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV GITLAB_PRIVATE_TOKEN=${GITLAB_PRIVATE_TOKEN}
ENV GITLAB_RESTRICTIONS_PROJECT_IDS=${GITLAB_RESTRICTIONS_PROJECT_IDS}
ENV GITLAB_HOST=${GITLAB_HOST}

COPY . .

COPY ./docker/php/entrypoint.sh /root/entrypoint.sh
COPY ./docker/wait-for-it.sh /root/wait-for-it.sh
COPY ./docker/cron/crontab /etc/cron.d/app

RUN crontab /etc/cron.d/app \
    && service cron start \
    && chmod 755 /root/wait-for-it.sh \
    && chmod 755 /root/entrypoint.sh

#RUN /root/wait-for-it.sh db:5432 --timeout=180 -- echo "PostgreSQL started" \
#RUN composer install --no-dev

EXPOSE 9000

ENTRYPOINT ["/root/entrypoint.sh"]
