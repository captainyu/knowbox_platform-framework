#!/bin/bash
project="xxx"
tag="xx"
env=${1}
if [ ${env} ]; then
    phpenv=${env}
    cp /data/wwwroot/${project}/dockerconf/ngconf/nginx.${env}.conf /data/vhost/${tag}.conf
    docker exec ${tag} php /data/wwwroot/${project}/init --overwrite=all --env=${phpenv}
    docker exec ${tag} crontab /data/wwwroot/${project}/dockerconf/crontab.cron
    docker exec ${tag} supervisorctl reload
    docker exec tengine nginx -s reload
else
    echo "需要输入环境参数"
    exit
fi

if [ "${env}" = "dev" ]; then
    docker run  -v /data/wwwroot/${project}/api:/input -v /data/wwwroot/${project}/wiki:/output --rm apidoc -i /input -o /output
fi