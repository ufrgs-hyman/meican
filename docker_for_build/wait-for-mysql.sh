#!/bin/sh
# wait-for-mysql.sh

set -e
  
MYSQL_HOST="$1"
shift
  
until php -r "mysqli_connect('$MYSQL_HOST', '$MYSQL_USER', '$MYSQL_PASSWORD') or exit(1);" 2>&1 >/dev/null; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 1
done
  
>&2 echo "MySQL is up - executing command"
exec "$@"
