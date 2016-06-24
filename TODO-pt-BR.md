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

- Mostrar com código de cores a ocupação atual de cada elance. CHECK

- Computar para cada enlace o trafego passante baseado na medição das pontas de cada circuito que tem o elance em seu caminho. CHECK

- Ao se passar o mouse por cima do enlace, mostrar o gráfico de utilização acumulado do enlace e o gráfico de banda reservada acumulado. Bem como a lista dos circuitos que passam nesse enlace, com hyperlink para a página de cada circuito

- [Reavaliar necessidade] Ter um campo de filtro para buscar um campo especifico. Possibilitar lista de sugestões? Feature existente no Weathermap que conflita com a tela de detalhes do circuito.

- Mostrar enlaces que estão DOWN utilizando coloração diferente. No weathermap da RNP é o preto. CHECK

- Computar o status do enlace com AND lógico entre o status da interface física das duas portas que formam o enlace. CHECK

- [Reavaliar necessidade] O status do enlace pode ser uma informação útil para ser apresentada na tela de detalhes do circuito. Uma forma seria informar algum alerta caso algum enlace do circuito esteja DOWN.

Circuits - View

- Aguardar DataPlaneStateChange para entao Commitar alterações de circuitos ativos

