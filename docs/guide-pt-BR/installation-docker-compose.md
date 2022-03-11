## MEICAN Docker

### Requisitos:
* <a href="https://docs.docker.com/install">Docker</a>
* <a href="https://docs.docker.com/compose/install">Docker Compose</a>


### Instruções para executar:


1- Acesse o diretório 'env' e abra o arquivo '.env' para configurar suas credenciais;

   - Você deve configurar os seguintes itens:
   
     * MYSQL_ROOT_PASSWORD
    
     * MYSQL_DATABASE
    
     * MYSQL_USER
    
     * MYSQL_PASSWORD
     
     * MEICAN_PORT
     
     * MEICAN_VERSION
        
        
2- Execute o seguinte comando no diretório raiz:

    docker-compose up


3- Após, o MEICAN estará disponível no localhost na porta configurada no parâmetro `MEICAN_PORT`, com um usuário criado:

```
Usuário: master
Senha: master
```

Se você estiver fazendo uma atualização, volte para o [Guia de atualização](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/upgrade.md). Se este não for o seu caso, o próximo passo é definir os parâmetros e configurar o aplicativo. Consulte o [Guia de Configuração](https://github.com/ufrgs-hyman/meican/blob/master/docs/guide/configuration.md).

