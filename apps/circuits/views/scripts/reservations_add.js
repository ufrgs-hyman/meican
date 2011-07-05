var currentTab = "t1";
var previousTab;

var center = new google.maps.LatLng(-23.051931,-60.975511);
var myOptions = {
    zoom: 5,
    center: center,
    streetViewControl: false,
    navigationControlOptions: {
        style: google.maps.NavigationControlStyle.ZOOM_PAN
    },
    backgroundColor: "white",
    mapTypeControl: false,
    mapTypeId: google.maps.MapTypeId.TERRAIN
};
var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
google.maps.event.trigger(map, 'resize');
map.setZoom( map.getZoom() );

function createTabs(){
    $("ul.tabs li").addClass("inactive").hide();        //esconde as abas
    $(".cont_tab").hide();                              //esconde todo conteudo
    $("ul.tabs li:eq(0)").addClass("active").show();    //mostra primeira aba
    $(".cont_tab:eq(0)").show();                        //mostra o conteudo da primeira aba

    $("ul.tabs li").click(function() {
        clearFlash();
        previousTab = currentTab;
        currentTab = $(this).attr("id");
        $("ul.tabs li").removeClass("active");          //remove qualquer classe “active”
        $(this).addClass("active");                     //Adiciona a classe “active” na aba selecionada
        $(".cont_tab").hide();                          //esconde o conteudo de todas as abas
        var activeTab = $(this).find("a").attr("href"); //encontra o atributo href para identificar a aba ativa e seu conteudo
        $(activeTab).fadeIn();                          //Mostra o conteudo da aba ativa gradualmente
        if (currentTab == "t5") {
            changeBand();
        }
        google.maps.event.trigger(map, 'resize');
        map.setZoom( map.getZoom() );
        
        return false;
    });    
}

function createSlider(){
    $('#slider').slider({
        value:(band_max)/2,
        min: band_min,
        max: band_max,
        step: band_div,
        slide: function( event, ui ) {
            if (ui.value >= (band_max*band_warning)) {
                $("#amount").animate({
                    'color': '#FF0000'
                },10);
                $( "#amount" ).val( ui.value + " Mbps. " + warning_string);
            } else {
                $("#amount").animate({
                    'color': '#00000000'
                },10);
                $( "#amount" ).val( ui.value + " Mbps");
            }
        }
    });
    $( "#amount" ).val( $( "#slider" ).slider( "value" ) + " Mbps");    
    $("#slider").bind("slidechange", changeBand());
}

function nextTab(elem){
    var activeTab;
    switch (elem.id) {
        case "bn1": {
            if (validate(currentTab)) {      
                clearFlash();
                $("ul.tabs li").removeClass("active");                      //Desativa aba ativada
                $("ul.tabs li:eq(1)").addClass("active").show();            //Exibe aba selecionada    
                $(".cont_tab").hide();                                      //Esconde o conteúdo de todas as abas
                activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");   //Identifica ABA ativa pelo href
                $(activeTab).fadeIn();                                      //Exibe somente o conteudo da aba ativa
                previousTab = currentTab;
                currentTab = "t2";
                google.maps.event.trigger(map, 'resize');
                map.setZoom( map.getZoom() );
            }
            break;
        }
        case "bn2": {
            if (validate(currentTab)) {
                clearFlash();
                $("ul.tabs li:eq(1)").removeClass("active");    
                $("ul.tabs li:eq(2)").addClass("active").show();            
                $(".cont_tab").hide();
                activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
                $(activeTab).fadeIn();  
                previousTab = currentTab;
                currentTab = "t3";
            }                
            break;
        }
        case "bn3": {
            if (validate(currentTab)) {
                clearFlash();
                $("ul.tabs li:eq(2)").removeClass("active");
                $("ul.tabs li:eq(3)").addClass("active").show();            
                $(".cont_tab").hide();
                activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
                $(activeTab).fadeIn();  
                previousTab = currentTab;
                currentTab = "t4";
            }                
            break;
        }
        case "bn4": {
            if (validate(currentTab)) {
                clearFlash();
                $("ul.tabs li:eq(3)").removeClass("active");
                $("ul.tabs li:eq(4)").addClass("active").show();            
                $(".cont_tab").hide();
                activeTab = $("ul.tabs li:eq(4)").find("a").attr("href");
                $(activeTab).fadeIn();   
                previousTab = currentTab;
                currentTab = "t5";
                changeBand();
            }
            break;            
        }        
        default: {
            break;
        }
    }
}

function prevTab(elem){
    var activeTab;
    switch (elem.id){
        case "bp2": {
            $("ul.tabs li").removeClass("active");
            $("ul.tabs li:eq(0)").addClass("active");            
            $(".cont_tab").hide();
            activeTab = $("ul.tabs li:eq(0)").find("a").attr("href");
            $(activeTab).fadeIn();
            previousTab = currentTab;
            currentTab = "t1";                 
            break;    
        }
        case "bp3": {
            $("ul.tabs li").removeClass("active");
            $("ul.tabs li:eq(1)").addClass("active");            
            $(".cont_tab").hide();
            activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
            $(activeTab).fadeIn();
            previousTab = currentTab;
            currentTab = "t2";        
            google.maps.event.trigger(map, 'resize');
            map.setZoom( map.getZoom() );                
            break;    
        }
        case "bp4": {
            $("ul.tabs li").removeClass("active");
            $("ul.tabs li:eq(2)").addClass("active");            
            $(".cont_tab").hide();
            activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
            $(activeTab).fadeIn();        
            previousTab = currentTab;
            currentTab = "t3";                
            break;    
        }
        case "bp5": {
            $("ul.tabs li").removeClass("active");
            $("ul.tabs li:eq(3)").addClass("active");            
            $(".cont_tab").hide();
            activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
            $(activeTab).fadeIn(); 
            previousTab = currentTab;
            currentTab = "t4";                  
            break;    
        }        
    } 
}

function validate(tab) {
    switch (tab) {
        case "t1": {
            return validateReservationName();            
            break;
        }
        case "t2": {
            return validateReservationEndPoints();
            break;
        }
        case "t3": {
            return true;
            break;
        }
        case "t4": {
            return true;// validateReservationTimer();
            break;            
        }        
        default: {
            return false;
            break;
        }
    } 
}

function validateReservationName() {
    if ($("#res_name").val() == "") {
        return nameError();
    } else
        return true;
}

function nameError(){
    setFlash(flash_nameReq);
    $("ul.tabs li").removeClass("active");
    $("ul.tabs li:eq(0)").addClass("active");            
    $(".cont_tab").hide();
    activeTab = $("ul.tabs li:eq(0)").find("a").attr("href");
    $(activeTab).fadeIn();
    previousTab = currentTab;
    currentTab = "t1"; 
    return false;
}

function validateReservationEndPoints() {   
    return ((sourceError(false)) && (destinationError(false)));    
    //return true
}

function sourceError(error){
    if (error) {
        setFlash(flash_sourceReq);
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(1)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
        $(activeTab).fadeIn();
        previousTab = currentTab;
        currentTab = "t2"; 
        return false;    
    } else
        return true;        
}

function destinationError(error){
    if (error) {
        setFlash(flash_destReq);
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(1)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");
        $(activeTab).fadeIn();
        previousTab = currentTab;
        currentTab = "t2"; 
        return false;    
    } else
        return true;
}

function validateReservationTimer() {
    return timerError(true);
    //return true;    
}

function timerError(error){
    if (error) {
        setFlash(flash_timerReq);
        $("ul.tabs li").removeClass("active");
        $("ul.tabs li:eq(3)").addClass("active");            
        $(".cont_tab").hide();
        activeTab = $("ul.tabs li:eq(3)").find("a").attr("href");
        $(activeTab).fadeIn();
        previousTab = currentTab;
        currentTab = "t4"; 
        return false;    
    } else
        return true;
}

function validateForm() {
    if (validateReservationName()) {
        if (validateReservationEndPoints()) {
            if (validateReservationTimer()) {
                $("#reservation_add").submit();                    
            } 
        }
    }
}

function changeName(elem){
    if (elem.id == "res_confirmation") {
        $("#res_name").val(elem.value);
    } else if (elem.id == "res_name") {
        $("#res_confirmation").val(elem.value);
    }
}

function changeBand(){
    var value = $("#slider").slider("option","value");

    if (value >= (band_max*band_warning)) {
        $("#lb_bandwidth").html("<font color=#FF0000>  " + value + " Mbps.<br>" + warning_string + "</font>");    
    } else {
        $("#lb_bandwidth").html("  " + value + " Mbps.");    
    }
}