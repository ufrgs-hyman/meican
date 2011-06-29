function nextTab(elem){
    var activeTab;              
    if (elem.id == "bn1") {                                         //botão NEXT 1
        $("ul.tabs li").removeClass("active");                      //Desativa aba ativada
        $("ul.tabs li:eq(1)").addClass("active").show();            //Exibe aba selecionada    
        $(".cont_tab").hide();                                      //Esconde o conteúdo de todas as abas
        activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");   //Identifica ABA ativa pelo href
        $(activeTab).fadeIn();                                      //Exibe somente o conteudo da aba ativa
        google.maps.event.trigger(map, 'resize');
        map.setZoom( map.getZoom() );
    } else if (elem.id == "bn2") {                  
        $("ul.tabs li:eq(1)").removeClass("active");    
        $("ul.tabs li:eq(2)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
        $(activeTab).fadeIn();        
    } else if (elem.id == "bn3") {
        $("ul.tabs li:eq(2)").removeClass("active");
        $("ul.tabs li:eq(3)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
        $(activeTab).fadeIn();        
    } else if (elem.id == "bn4") {
        $("ul.tabs li:eq(3)").removeClass("active");
        $("ul.tabs li:eq(4)").addClass("active").show();            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(4)").find("a").attr("href");
        $(activeTab).fadeIn();    
    } 
}

function previousTab(elem){
    var activeTab;
    if (elem.id == "bp2") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(0)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(0)").find("a").attr("href");
        $(activeTab).fadeIn();
    } else if (elem.id == "bp3") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(1)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
        $(activeTab).fadeIn();
        google.maps.event.trigger(map, 'resize');
        map.setZoom( map.getZoom() );
    } else if (elem.id == "bp4") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(2)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
        $(activeTab).fadeIn();        
    } else if (elem.id == "bp5") {
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(3)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
        $(activeTab).fadeIn(); 
    } 
}