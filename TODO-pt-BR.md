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

- Ao se passar o mouse por cima do enlace, mostrar o gráfico de utilização acumulado do enlace e o gráfico de banda reservada acumulado. Bem como a lista dos circuitos que passam nesse enlace, com hyperlink para a página de cada circuito

- [Reavaliar necessidade] Ter um campo de filtro para buscar um campo especifico. Possibilitar lista de sugestões? Feature existente no Weathermap que conflita com a tela de detalhes do circuito.

- [Reavaliar necessidade] O status do enlace pode ser uma informação útil para ser apresentada na tela de detalhes do circuito. Uma forma seria informar algum alerta caso algum enlace do circuito esteja DOWN.

ISSUES

1) Status na página de detalhes do circuito não está atualizando automaticamente
2) Lista de circuitos esta incompleta.
3) A opção de interromper o circuito está disponível por padrão, isso pode induzir um usuário/operador a interromper o circuito quando não for o desejável.
3.a) Sugiro inverter o padrão e marcar como No por default.
3.b) Sugiro mostrar sempre os 3 campos e utilizar validação de formulário para mostrar mensagens de erro em destaque em vermelho, caso o valor da data de inicio ou banda forem alterados e a opção de permitir interrupção ainda continue como No. Ao precionar o botão de confirmar a reserva, informar via pop-up o problema, estilo erro de confirmaçao de EULA (Ex: Favor confirmar permissão para interrupção do circuito para alteração de startTime e banda) e destacar os erros (Campos de permissão e valores alterados).
3.c) Caso a sugestão 3.b for considerada, é possível que o usuário se arrependa de alter os valores e queira desistir da reserva. Avaliar como isso pode ser feito. De forma simples, apenas cancelar, ou de forma mais complexa com campo para restaurar os valores originais de cada campo.
5) [Topology] Não estão sendo mostrados os links das topologia NSI e Cipo
6) [CircuitDetails] Grafico não apresenta unidade de medida adequada (Ex: 0.0003 Mbps). Considerar utilizar algum tipo de mudança de escala da unidade de medida para evitar numeros com mais de 1 ou 2 casas decimais (Ex 0.3Kbps ou 300 bps).
7) Enlaces sem circuitos devem ter uma mensagem informando isto.
8) Habilitar hover em nodes e links.
9) Inverter direcao trafego no traffic map


