var pos = 0;  // posição dos novos ACLs a serem adicionados
var newCont = 0;  // contagem dos ACLs válidos a serem adicionados, é o mesmo que a quantidade de posições válidas de validArray
var validArray = new Array(); // var que contém quais posições estão válidas, dos novos ACLs a serem adicionados (se a linha foi excluída, então é inválida)

var isEditingACL = false;  // var que informa se o usuário está ou não editando algum ACL
var editpos = 0;  // posição dos ACLs que estão sendo editados