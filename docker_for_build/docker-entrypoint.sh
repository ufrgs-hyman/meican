#!/bin/sh

if [ "$1" = "dev" ]
then
    cp /composer.phar $MEICAN_DIR
    ENV_TEXT="Development environment"
    echo "composer.phar copied to container"
else
    ENV_TEXT="Production environment"
fi

cp $MEICAN_DIR/docker_for_build/db.php $MEICAN_DIR/config/ \
 && sed -i "s/MYSQL_DATABASE/$MYSQL_DATABASE/" $MEICAN_DIR/config/db.php \
 && sed -i "s/MYSQL_USER/$MYSQL_USER/" $MEICAN_DIR/config/db.php \
 && sed -i "s/MYSQL_PASSWORD/$MYSQL_PASSWORD/" $MEICAN_DIR/config/db.php \
 && chown meican:meican $MEICAN_DIR/config/db.php \
 && su meican -c "php composer.phar install" \
 && service apache2 start \
 && echo "\033[0;32m MEICAN started successfully - $ENV_TEXT \033[0m " \
 && echo "\033[0;32m Running on http://localhost:$MEICAN_PORT \033[0m " \
 && /bin/bash
