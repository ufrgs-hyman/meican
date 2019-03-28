ISSUES

15) Test77 to MXPA ========>  Test77 : MXSP to MXPA 
26) path nao disponivel na aba Info se circuito falha
10) retirar limitacao de reservas com banda 0
5) definir comportamento do formulario do modal ao clicar em um agrupamento de pontos, 
devem ser mostrados apenas os LIDs agrupados na lista, todos ou selecionar um aleatorio?
2) ao definir um point no modo avancado deve ser procurado o nodo associado e desenhado o path no mapa se possivel.
3) ao editar um point, o modal atualmente nao mostra os selects preenchidos, talvez poderiamos usar a URN e criar uma funcao que a partir da URN marque os selects.
8) VALIDAR TELA DE STATUS DE CIRCUITO COM MUDANCAS

File "/usr/local/lib/python2.7/site-packages/opennsa/protocols/nsi2/helper.py", line 143, in parseRequest
	    for av in attr.AttributeValue:
	exceptions.TypeError: 'NoneType' object is not iterable
	
2018-01-31 02:38:22Z [HTTPChannel,20,201.21.202.89] SOAP Payload that caused error:
	<?xml version="1.0" encoding="UTF-8"?>
	<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://schemas.ogf.org/nsi/2013/12/connection/types" xmlns:ns2="http://schemas.ogf.org/nsi/2013/12/framework/headers" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:gns="http://nordu.net/namespaces/2013/12/gnsbod" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"><SOAP-ENV:Header><ns2:nsiHeader><protocolVersion>application/vnd.ogf.nsi.cs.v2.provider+soap</protocolVersion><correlationId>urn:uuid:fae8a536-d367-e012-5033-307df530629f</correlationId><requesterNSA>urn:ogf:network:cipo.rnp.br:2017:nsa:meican</requesterNSA><providerNSA>urn:ogf:network:TestNetwork:nsa</providerNSA><replyTo>http://localhost/meican/circuits/nsi/requester</replyTo><gns:ConnectionTrace><Connection index="0">urn:ogf:network:cipo.rnp.br:2017:nsa:meican</Connection></gns:ConnectionTrace><sessionSecurityAttr><saml:Attribute Name="user">asdas</saml:Attribute><saml:Attribute Name="token">asdsad</saml:Attribute></sessionSecurityAttr></ns2:nsiHeader></SOAP-ENV:Header><SOAP-ENV:Body><ns1:reserve><description>asdasda</description><criteria version="1"><schedule><startTime>2018-01-04T02:00:00.000-00:00</startTime><endTime>2018-01-05T02:00:00.000-00:00</endTime></schedule><serviceType>http://services.ogf.org/nsi/2013/12/descriptions/EVTS.A-GOLE</serviceType><p2p:p2ps xmlns:p2p="http://schemas.ogf.org/nsi/2013/12/services/point2point"><capacity>10</capacity><directionality>Bidirectional</directionality><symmetricPath>true</symmetricPath><sourceSTP>urn:ogf:network:cipo.rnp.br:2013::mxac:ge-2_3_4:+?vlan=200-299</sourceSTP><destSTP>urn:ogf:network:cipo.rnp.br:2013::mxpi:ge-2_3_4:+?vlan=200-299</destSTP><parameter type="protection">PROTECTED</parameter></p2p:p2ps></criteria></ns1:reserve></SOAP-ENV:Body></SOAP-ENV:Envelope>


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

