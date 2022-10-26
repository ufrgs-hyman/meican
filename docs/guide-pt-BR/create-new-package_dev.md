## Instruções para criar um novo _package_ do MEICAN :package:

### 1- Escolha as configurações para a imagem que será gerada: :mag:

  * A versão do MEICAN que você deseja construir.

  * Um nome para a imagem que será criada.

<div>&nbsp&nbsp :warning: O suporte para essa funcionalidade existe a partir da versão 3.4.0.</div>
<div>&nbsp&nbsp :warning: Nos comandos a seguir, <b><em>3.4.0</em></b> será usado como a versão escolhida e <b><em>ghcr.io/ufrgs-hyman/meican/meican-app:3.4.0</em></b> será usado como o nome da imagem escolhida.
Quando você for criar uma imagem, não se esqueça de alterar esses valores para os de sua preferência.</div>

<br>

### 2- Construir a imagem MEICAN: :gear:
  * Vá para o diretório 'docker_for_build'.
  * Execute: ```docker build --build-arg MEICAN_VERSION=3.4.0 -t ghcr.io/ufrgs-hyman/meican/meican-app:3.4.0 .```

<div>&nbsp&nbsp :warning: Observe que há um ponto no final do comando.</div>
<div>&nbsp&nbsp :triangular_flag_on_post: Após a execução, a imagem será criada em sua máquina local.</div>

<br>

### 3- Autentique-se no Container Registry: :key:

  * Para autenticar-se, siga as instruções no link a seguir: https://docs.github.com/pt/packages/working-with-a-github-packages-registry/working-with-the-container-registry#

<br>

### 4- Envie a imagem para o GitHub Container Registry: :arrow_heading_up:

  * Execute: ```docker push ghcr.io/ufrgs-hyman/meican/meican-app:3.4.0```
   
<div>&nbsp&nbsp :warning: Observe que <b><em>ghcr.io/ufrgs-hyman/meican/meican-app:3.4.0</em></b> é o nome que você definiu para a imagem.</div>

<br>
<br>

:heavy_check_mark: Após essas etapas, sua nova imagem estará publicada no GitHub Container Registry! :confetti_ball: :tada:
