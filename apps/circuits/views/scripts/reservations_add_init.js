$("ul.tabs li").addClass("inactive").hide();        //esconde as abas
$(".cont_tab").hide();                              //esconde todo conteudo
$("ul.tabs li:eq(0)").addClass("active").show();    //mostra primeira aba
$(".cont_tab:eq(0)").show();                        //mostra o conteudo da primeira aba
  
$("ul.tabs li").click(function() {
 
    $("ul.tabs li").removeClass("active");          //remove qualquer classe “active”
    $(this).addClass("active");                     //Adiciona a classe “active” na aba selecionada
    $(".cont_tab").hide();                          //esconde o conteudo de todas as abas
    var activeTab = $(this).find("a").attr("href"); //encontra o atributo href para identificar a aba ativa e seu conteudo
    $(activeTab).fadeIn();                          //Mostra o conteudo da aba ativa gradualmente
    return false;
});

$('#slider').slider({
    value:(band_max)/2,
    min: band_min,
    max: band_max,
    step: band_div,
    slide: function( event, ui ) {
        if (ui.value >= (band_max*band_warning)) {
            $("#amount").animate({'color': '#FF0000'},10);
            $( "#amount" ).val( ui.value + " Mbps. " + warning_string);
        } else {
            $("#amount").animate({'color': '#00000000'},10);
            $( "#amount" ).val( ui.value + " Mbps");
        }
    }    
});
$( "#amount" ).val( $( "#slider" ).slider( "value" ) + " Mbps");
               
 var center = new google.maps.LatLng(-23.051931,-43.975511);
 var myOptions = {
   zoom: 3,
   center: center,
   streetViewControl: false,
   navigationControlOptions: {style: google.maps.NavigationControlStyle.ZOOM_PAN},
   mapTypeControl: false,
   mapTypeId: google.maps.MapTypeId.TERRAIN
 };
 var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);