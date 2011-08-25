#!/bin/bash
case "$1" in
  data)
    cd data
    for arq in *.sql
    do
      echo "Populating table $arq";
      mysql --user=root --password="futurarnp" --host=localhost -Dmeican < $arq;
    done
    cd ..
    ;;
  tables)
    cd structure
    for arq in *.sql
    do
      echo "Creating table $arq";
      mysql --user=root --password="futurarnp" --host=localhost -Dmeican < $arq;
    done
    cd ..
    ;;
  *)
    echo "Deleting current database...";
    mysql --user=root --password="futurarnp" --host=localhost -Dmeican < meican.sql;
    cd structure
    for arq in *.sql
    do
      echo "Creating table $arq";
      mysql --user=root --password="futurarnp" --host=localhost -Dmeican < $arq;
    done
    cd ..
    cd data
    for arq in *.sql
    do
      echo "Populating table $arq";
      mysql --user=root --password="futurarnp" --host=localhost -Dmeican < $arq;
    done
    cd ..
    ;;
esac
exit 0

