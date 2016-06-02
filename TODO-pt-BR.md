TO DO:

Login

- Melhorar informacoes da tela de login
- Atualizar tela de login via cafe

AAA - Users

- Criacao
- Edicao

Topology Discovery 

- Edicao de regras de descoberta
- Remover suporte ao DDS, pois nao parece estavel, manter HTTP como unico protocolo
- Habilitar novo Scheduler para agendar descobertas

Monitoring

- Mostrar com código de cores a ocupação atual de cada elance. Exemplos:
https://www.rnp.br//servicos/conectividade/trafego
https://wiki.rnp.br/pages/viewpage.action?pageId=86100729

- Computar para cada enlace o trafego passante baseado na medição das pontas de cada circuito que tem o enlace em seu caminho

- Ao se passar o mouse por cima do enlace, mostrar o gráfico de utilização acumulado do enlace e o gráfico de banda reservada acumulado. Bem como a lista dos circuitos que passam nesse enlace, com hyperlink para a página de cada circuito

- Mostrar enlaces que estão DOWN utilizando coloração diferente. No weathermap da RNP é o preto.
-- Computar o status do enlace com AND lógico entre o status da interface física das duas portas que formam o enlace.

Circuits - Reservar

- adicionar pontos via grafo (modulo do viewer)

Circuits - View

- Aguardar DataPlaneStateChange para entao Provisionar alterações de circuitos ativos


