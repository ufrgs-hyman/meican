$(".cont_tab").hide(); //esconde todo conteudo
$("ul.tabs li:first").addClass("active").show(); //ativa a primeira aba
$(".cont_tab:first").show(); //mostra o conteudo da primeira aba
  
$("ul.tabs li").click(function() {
 
    $("ul.tabs li").removeClass("active"); //remove qualquer classe “active”
    $(this).addClass("active"); //Adiciona a classe “active” na aba selecionada
    $(".cont_tab").hide(); //esconde o conteudo de todas as abas
    var activeTab = $(this).find("a").attr("href"); //encontra o atributo href para identificar a aba ativa e seu conteudo
    $(activeTab).fadeIn(); //Mostra o conteudo da aba ativa gradualmente
    return false;
});

$('#slider').slider({
    value:500,
    min: 100,
    max: 1000,
    step: 100,
    slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.value );
    }
});
               
