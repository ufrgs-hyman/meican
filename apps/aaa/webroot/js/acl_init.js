//$("#aro_hint1").position({
//    my: "left top",
//    at: "right top",
//    of: $("#aro_box1")
//});

var pos = 0;  // posição das novas URNs a serem adicionadas
var newCont = 0;  // contagem das URNs válidas a serem adicionadas, é o mesmo que a quantidade de posições válidas de validArray
var validArray = new Array(); // var que contém quais posições estão válidas, das novas URNs a serem adicionadas (se a linha foi excluída, então é inválida)

var isEditingACL = false;  // var que informa se o usuário está ou não editando alguma URN
var editpos = 0;  // posição das URNs que estão sendo editadas

//$('.titleHintBox').inputHintBox({
//    div: $('#shiny_box'),
//    div_sub: '.shiny_box_body',
//    source: 'attr',
//    attr: 'title',
//    incrementLeft: 5
//});