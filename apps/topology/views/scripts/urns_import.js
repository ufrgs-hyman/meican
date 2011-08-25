var isEditingURN = false;  // var que informa se o usuário está ou não editando alguma URN
var editpos = 0;  // posição das URNs que estão sendo editadas
var pos = 0;  // posição das novas URNs a serem adicionadas
var newCont = 0;  // contagem das URNs válidas a serem adicionadas, é o mesmo que a quantidade de posições válidas de validArray
var validArray = new Array(); // var que contém quais posições estão válidas, das novas URNs a serem adicionadas (se a linha foi excluída, então é inválida)

var isImporting = true; // var que informa se a topologia está sendo importada do zero (quando não há nenhuma URN no BD)
var isManual = false;

pos = urns_to_import.length;
for (var i=0; i < pos; i++) {
    validArray[i] = true;
    newCont++;
}