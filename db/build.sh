#!/bin/bash
host="localhost"
password="futurarnp"
user="root"
database="meican";

#agora=time;
#mysqldump -user=$user --password=$password --host=$host -D$database > dump_$agora.sql


case "$1" in
  data)
    cd data
    for arq in *.sql
    do
      echo "Populating table $arq";
      mysql --user=$user --password=$password --host=$host -D$database < $arq;
    done
    cd ..
    ;;
  tables)
    cd structure
    for arq in *.sql
    do
      echo "Creating table $arq";
      mysql --user=$user --password=$password --host=$host -D$database < $arq;
    done
    cd ..
    ;;
  *)
    echo "Deleting current database...";
    mysql --user=$user --password=$password --host=$host -D$database < $database.sql;
    cd structure
    for arq in *.sql
    do
      echo "Creating table $arq";
      mysql --user=$user --password=$password --host=$host -D$database < $arq;
    done
    cd ..
    cd data
    for arq in *.sql
    do
      echo "Populating table $arq";
      mysql --user=$user --password=$password --host=$host -D$database < $arq;
    done
    cd ..
    ;;
esac
exit 0

