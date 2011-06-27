cd ..
#varre as pastas de idiomas em i18n
for LANG in $(ls -l i18n | grep ^d | awk '{print $8}')
do
#varre as pastas de apps
for DIR in $(ls apps)
do
        cd apps/$DIR
        # xgettext precisa de um arquivo vazio temporário para colocar todas as strings encontradas
        echo ''> messages.po 
        find . -type f -iname "*.php" | xgettext --keyword=__ --keyword=_e -j -f -
        if [ -f ../../i18n/$LANG/LC_MESSAGES/$DIR.po ]
        then {
                #faz merge entre as encontradas e as atuais no aquivo .po da respectiva aplicação
                msgmerge -N ../../i18n/$LANG/LC_MESSAGES/$DIR.po messages.po> new.po
                mv new.po ../../i18n/$LANG/LC_MESSAGES/$DIR.po
        }
        else {
                #verifica se o arquivo temporário criado é maior que 1, logo foi criado e precisa ser copiado
        	if [ `ls -l messages.po | awk '{print $5}'` -gt 1 ]
        	
        	then {
        		cp messages.po ../../i18n/$LANG/LC_MESSAGES/$DIR.po
        	}
        	fi
        }
        fi
        rm messages.po
        cd ../..
done
done

