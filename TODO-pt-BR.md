ISSUES

15) Test77 to MXPA ========>  Test77 : MXSP to MXPA 
26) path nao disponivel na aba Info se circuito falha
10) retirar limitacao de reservas com banda 0
5) definir comportamento do formulario do modal ao clicar em um agrupamento de pontos, 
devem ser mostrados apenas os LIDs agrupados na lista, todos ou selecionar um aleatorio?
1) nodes devem referenciar point(s) para facil acesso e busca.
2) ao definir um point no modo avancado deve ser procurado o nodo associado e desenhado o path no mapa se possivel.
3) ao editar um point, o modal atualmente nao mostra os selects preenchidos, talvez poderiamos usar a URN e criar uma funcao que a partir da URN marque os selects.
4) usar o contextmenu para expandir os pointgroups se possivel
5) VALIDAR RESERVA VIA MODO AVANCADO
6) VALIDAR RESERVA VIA MAPA
7) VALIDAR QUE PONTOS SE EXPANDEM CASO SOLICITADO
8) VALIDAR TELA DE STATUS DE CIRCUITO COM MUDANCAS

Futuro

1) Pontos no mapa deveriam comecar agrupados, o usuario deve interagir com os nodos e os 
desagrupar se quiser. Na proxima vez que for visualizado, o mapa deve mostrar os nodos de acordo
com a ultima configuracao do usuario. 
16) Bloquear dias do passado no schedule da reserve
24) Federation login
18) Ediçao de circuitos finalizados nao deve ser permitido. PassedEndTime deve ser verificado, uma vez detectado deve-se finalizar o circuito.
27) nao deveria ser permitido editar ou refresh de reservas finalizadas
20) ACKs nao tao sendo enviados pelo MEICAN
11) Colocar VLAN na popup de circuit view (FALAR COM MARCOS => Cada device representa dois pontos e eles tem duas vlans.)
21) Meican deveria ser um provedor na lista de providers, mas nao poderia ser removido.
Ele nao possuiria servicos e seria o unico uRA da lista.
22) usar snakeanimate para animar os paths
8) Habilitar hover em nodes e links. 
9) colocar uma flecha deixando mais claro o sentido do trafego.
7) Enlaces sem circuitos devem ter uma mensagem informando isto.
17) Discovery nao ta criando notificacoes.
25) Monitoring [Reavaliar necessidade] O status do enlace pode ser uma informação útil para ser apresentada na tela de detalhes do circuito. Uma forma seria informar algum alerta caso algum enlace do circuito esteja DOWN.


remover entidade device
DEFINIDO (SIM)

sim

- parser seria unificado a partir de dominios, redes e portas.
- parser nao se preocupa em criar devices, quem deve criar esta entidade eh o mapa ao mostrar as portas no mapa, caso estejam no mesmo local ou nao tenham localizacao geografica.
- aumento de processamento para gerar os devices on the fly.
- processamento para obter links entre descricoes de topologias diferentes. Nao eh um problema efetivo dado que o mapa de monitoramento eh baseado apenas em NMWG, pois que acessamos diretamente o OSCARS para obter os circutos atuais.

nao

- parser deveria definir um device e haveriam problemas com topologias nao oscars, dado que os devices nao existem nessa visao.
- topologia NSI nao respeita regras NMWG, o parser segue friamente as regras do NMWG primariamente.
 
======

topologia poderia ser mantida em XML
DEFINIDO (SERAH FEITO TESTE DA TOPOLOGIA EM XML COM PROTOTIPO)

xml

- parser deve ser usado no momento de mostrar o mapa (aqui caberia um cache)
- politicas e permissoes nao sao afetadas, pois elas existem indepentendes da topologia. Os dominios podem ser descobertos a partir do XML, mas para serem removidos deve-se excluir diretamente na web.
- o discovery continuara existindo para descobrir dominios e mostrar inconsistencias da topologia
- no futuro pode ser decidido manter a topologia no banco tambem, o que eh perfeitamente possivel.
- nao seria possivel (inicialmente) adicionar ou editar a topologia no meican. Isso eh necessario realmente? Apenas ouvi relatos de uso para edicao de coordenadas geograficas. Esse nao eh o objetivo das interfaces, temos um caso de subutilizacao, provando que existem paginas desnecessarias no sistema. 

banco

- remocao da entidade device implica em diversas mudancas, o que demanda muita implementacao e teste,
muito mais que apenas alterar o parser.
- o parser sera alterado em qualquer cenario, mas manter a topologia no banco demanda muitas alteracoes para o funcionamento basico. Mantendo em XML, as reservas estariam funcionais antes mesmo do discovery.
- politicas e permissoes nao seriam afetadas.
- seria possivel editar e adicionar elementos na topologia do meican


topo = [
	&dom1
	dom1 = [
		net1 = [
			p1
		]
	]
]

dom1 = {
	'color': #000,
	'networks': [&net1]
}

net1 = {
	'urn':'asdas',
	'points':[&p1],
	'parent': &dom1
}

p1 = {
	'urn':'asdasd',
	'parent': &net1
}

node1 = {
	'points':[&p1]
}

