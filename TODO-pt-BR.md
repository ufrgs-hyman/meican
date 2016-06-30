TO DO:

Apresentacao

Pauta prevista:
1) Discutir sobre os recursos disponíveis para apresentação e divulgação. Ex: Slides e possivelmente um live demo no playgroud; e para divulgação acesso ao playground, documento em PDF sobre overview do ambiente, videos... Acredito que temos versões de todos esses materiais, precisamos então discutir quais se aplicam e se há atualizações pendentes nos conteúdos.
2) Verificar possibilidade de apresentar features do escopo 2016.1 (Tela de detalhes de circuitos e monitoramento)
3) Definir apresentador e roteiro. Ainda não nos foi passado o tempo de duração para a apresentação.
4) Discutir possíveis sugestões para um slide de trabalhos futuros. Ex: Expandir o monitoramento para outros domínios (SouthernLight, AmLight, GEANT, ESnet...), novas features...

Live demo (Roteiro)
- Intro
a) Reserva de circuitos (Criação*, alteração,  remoção, logs)
b) Autorização e workflows (estático)
c) Monitoramente de circuitos (Visualização e arquitetura)
d) Visualizador e Sincronizador de Topologia (intra e inter dominio, proxy)
e) Outros (Testes automatizados...)

Próximo checkpoint:
- 14h 05/07/2016
- Proposta atual) Live demo no ambiente novo
- [Todos] Revisar viabilidade da proposta
- [MEICAN] Preparar apresentação baseada no roteiro proposto e formato da apresentação do SCI 2015
- [GRE] Testes da interface e procedimentos propostos
- [MEICAN] Estabilização do ambiente

//////////

Login

- Atualizar tela de login via cafe

AAA - Users

- Criacao
- Edicao

Monitoring

- [Reavaliar necessidade] O status do enlace pode ser uma informação útil para ser apresentada na tela de detalhes do circuito. Uma forma seria informar algum alerta caso algum enlace do circuito esteja DOWN.

ISSUES

5) [Topology] Não estão sendo mostrados os links das topologia NSI e Cipo
6) [CircuitDetails] Grafico não apresenta unidade de medida adequada (Ex: 0.0003 Mbps). Considerar utilizar algum tipo de mudança de escala da unidade de medida para evitar numeros com mais de 1 ou 2 casas decimais (Ex 0.3Kbps ou 300 bps).
7) Enlaces sem circuitos devem ter uma mensagem informando isto.
8) Habilitar hover em nodes e links. 
9) Discovery nao le topologias redirecionadas ou le?
10) IN e OUT estao invertidos no traffic
11) Colocar VLAN na popup de circuit view
12) Colocar Device to Device na legenda do circuit view
13) Oscars service nao esta atualizando status dos circuitos
14) Garantid Capacity nos enlaces
15) Test77 to MXPA ========>  Test77 : MXSP to MXPA
16) Bloquear dias do passado no schedule da reserve

