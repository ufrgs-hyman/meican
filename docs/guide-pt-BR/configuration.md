##GUIA DE CONFIGURAÇÃO

Esta etapa envolve definir alguns parâmetros necessários para configurar a aplicação.

###PARÂMETROS

Localizados em: config/params.php

O certificado da aplicação deve ser mantido na pasta 'certificates' na raiz do projeto. O nome do arquivo contendo o certificado e a senha do certificado devem ser definidas como segue:

```
'certificate.filename' => 'meican.pem',
'certificate.pass' => '#CERTIFICATE-PASSWORD#',    
```

Por padrão, o provedor de testes está definido como ativado. Este provedor de circuitos aceita todas as requisições feitas por quaisquer usuários. Tais requisições ainda podem ser rejeitadas pelo sistema de workflows, veja mais detalhes desse sistema no módulo BPM. Para desativar o provedor de testes, defina o seguinte parâmetro como falso:

```
'provider.force.dummy' => false,
```

O formulário de recuperação de senha de usuários usa a [Google reCAPTCHA API](https://www.google.com/recaptcha). É necessário preencher as chaves em:

```
'google.recaptcha.secret.key' => '',
'google.recaptcha.site.key' => '',
```

O sistema de feedback da aplicação requer um email válido:

```
'mailer.destination' => 'meican@example.com',
```

Um servidor SMTP deve ser configurado em um arquivo separado, para uso por parte da aplicação. São necessários um host, um usuário e uma senha.

Localizado em: config/mailer.php

```
'host' => '',
'username' => '',
'password' => '',
```

###NSA ID

É necessário definir um NSA ID para identificar a aplicação junto ao provedor NSI configurado. Acesse a interface, no menu Reservas entre na opção Configuração. Nesta interface defina o MEICAN NSA ID com uma string válida, seguindo o padrão URN:

```
urn:ogf:network:#DOMAIN#:#YEAR#:nsa:meican
```

###PROVEDOR PADRÃO

Se o provedor de testes estiver desativado, nós precisamos definir um provedor para receber as requisições feitas pelo MEICAN. Ainda na mesma seção anterior (Reservas > Configuração) você pode definir o NSA ID do provedor, assim como a URL do seu Connection Service.
