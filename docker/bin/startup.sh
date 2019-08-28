#!/usr/bin/env bash

echo 'APP_ENV="$APP_ENV"' >> /etc/environment

echo $APP_ENV > /crm/current/APP_ENV

envsubst < /etc/php/7.2/fpm/pool.d/www.conf.raw > /etc/php/7.2/fpm/pool.d/www.conf

if [ "${APP_ENV}" = "production" ] || [ "${APP_ENV}" = "testing" ]
then
    cron
    crontab /root/crontab
fi

if [ "${APP_ENV}" = "local" ]
then cat /root/hosts >> /etc/hosts
fi

service php7.2-fpm stop
service php7.2-fpm start
service redis-server start

mkdir -p /beanstalk-wal
beanstalkd -b /beanstalk-wal -z 5242880 &

envsubst < /etc/supervisor/conf.d/laravel-worker.conf.raw > /etc/supervisor/conf.d/laravel-worker.conf

service supervisor start

supervisorctl reread
supervisorctl update
supervisorctl restart all

chmod +x /crm/current/docker/bin/*

nginx -g 'daemon off;'