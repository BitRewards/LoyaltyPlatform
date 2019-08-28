FROM ubuntu:16.04

RUN apt-get update && \
		apt-get install -y --allow-unauthenticated curl python-software-properties \
		software-properties-common && \
		LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php && \
		LC_ALL=C.UTF-8 add-apt-repository "deb http://apt.postgresql.org/pub/repos/apt/ xenial-pgdg main" && \
		curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - && \
		apt-get update && \
		apt-get install -y --allow-unauthenticated  nginx \
		php7.2-fpm php7.2-cli php-curl mc git tig net-tools php7.2-pgsql \
		php7.2-json  php7.2-xml iputils-ping \
		redis-server gettext phpunit \
		php7.2-mbstring  vim  supervisor beanstalkd \
		php7.2-zip libfcgi0ldbl locales postgresql-client \
		php7.2-curl imagemagick php7.2-gd php7.2-imagick php7.2-xdebug \
		php7.2-bcmath  php7.2-redis  php7.2-amqp \
    && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer global require hirak/prestissimo && \
    ln -s /crm/current/docker/crontab /root/crontab


RUN locale-gen en_US.UTF-8 && \
		locale-gen ru_RU.UTF-8 && \
		locale-gen zh_CN.UTF-8 && \
		locale-gen tr_TR.UTF-8 && \
		update-locale

COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY php/fpm/conf.d/xdebug.ini /etc/php/7.2/fpm/conf.d/20-xdebug.ini
COPY php/fpm/pool.d/www.conf /etc/php/7.2/fpm/pool.d/www.conf.raw
COPY supervisord/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf.raw



