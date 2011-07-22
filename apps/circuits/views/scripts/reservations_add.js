var currentTab = "t1";
var previousTab;
var tab1_valid = false;
var tab2_valid = false;

var src_networks = null;
var dst_networks = null;
var src_urn = null;
var dst_urn = null;

function createTabs(){
    $(".cont_tab").hide();                              //esconde todo conteudo
    
    $("ul.tabs li").click(function() {
        if ($("#t1").attr("class") != "ui-state-disabled") {
            clearFlash();
            if (($(this).attr("id") == "t3") && ($("#t3").attr("class") == "ui-state-disabled")){
                return false;
            }
            previousTab = currentTab;
            currentTab = $(this).attr("id");
            $("ul.tabs li").removeClass("active");          //remove qualquer classe “active”
            $(this).addClass("active");                     //Adiciona a classe “active” na aba selecionada
            $(".cont_tab").hide();                          //esconde o conteudo de todas as abas
            var activeTab = $(this).find("a").attr("href"); //encontra o atributo href para identificar a aba ativa e seu conteudo
            $(activeTab).fadeIn();                          //Mostra o conteudo da aba ativa gradualmente
            if (currentTab == "t3") {
                changeBand();
            }
            google.maps.event.trigger(edit_map, 'resize');
            edit_map.setZoom( edit_map.getZoom() );
            edit_setBounds(edit_bounds);

            google.maps.event.trigger(view_map, 'resize');
            view_map.setZoom( view_map.getZoom() );
            view_setBounds(view_bounds);
        }
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
    $('#slider').removeClass("ui-widget");
    $('#slider').addClass("ui-widget-slider");
    $( "#amount" ).val( $( "#slider" ).slider( "value" ) + " Mbps");    
    $("#slider").bind("slidechange", changeBand());
    $("#slider").slider( "option", "disabled", true );
    $("#amount_label").hide();        
    $("#amount").hide();    
}

function nextTab(elem){
    var activeTab;
    switch (elem.id) {
        case "bn1": {
            clearFlash();
            $("ul.tabs li").removeClass("active");                      //Desativa aba ativada
            $("ul.tabs li:eq(1)").addClass("active").show();            //Exibe aba selecionada    
            $(".cont_tab").hide();                                      //Esconde o conteúdo de todas as abas
            activeTab = $("ul.tabs li:eq(1)").find("a").attr("href");   //Identifica ABA ativa pelo href
            $(activeTab).fadeIn();                                      //Exibe somente o conteudo da aba ativa
            previousTab = currentTab;
            currentTab = "t2";
            break;
        }
        case "bn2": {
            if ((tab1_valid) && (tab2_valid)) {
                clearFlash();
                $("ul.tabs li").removeClass("active");    
                $("ul.tabs li:eq(2)").addClass("active").show();            
                $(".cont_tab").hide();
                activeTab = $("ul.tabs li:eq(2)").find("a").attr("href");
                $(activeTab).fadeIn();  
                previousTab = currentTab;
                currentTab = "t3";
                google.maps.event.trigger(view_map, 'resize');
                view_setBounds(view_bounds);
                view_map.setZoom( view_map.getZoom() );                 
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
            google.maps.event.trigger(edit_map, 'resize');
            edit_setBounds(edit_bounds);
            edit_map.setZoom( edit_map.getZoom() );                            
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
            break;    
        }     
    } 
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
    if (elem.value != "") {
        $("#t1").removeClass("ui-state-disabled");
        $("#t2").removeClass("ui-state-disabled");
        if ((tab1_valid) && (tab2_valid)) {
            $("#t3").removeClass("ui-state-disabled");
        }
        switch (currentTab) {
            case "t1": {
                $(".cont_tab:eq(0)").show(); 
                $("#t1").addClass("active");                          
                break;
            }
            case "t2": {
                $(".cont_tab:eq(1)").show();                        
                $("#t2").addClass("active");
                break;
            }
            case "t3": {
                $(".cont_tab:eq(2)").show();                       
                $("#t3").addClass("active");
                break;
            }
            default: {
                break;
            }
        }        
        
        $("#div-tabs").removeClass("inactive");
        $("#ul-tabs").removeClass("inactive");        
        
        google.maps.event.trigger(edit_map, 'resize');
        edit_map.setZoom( edit_map.getZoom() );                  
        edit_setBounds(edit_bounds);        
        
        google.maps.event.trigger(view_map, 'resize');
        view_map.setZoom( view_map.getZoom() );  
        view_setBounds(view_bounds);
        
    } else if ((elem.value == "")) {
        $("#t1").addClass("ui-state-disabled");
        $("#t2").addClass("ui-state-disabled");
        if ((tab1_valid) && (tab2_valid)) {
            $("#t3").addClass("ui-state-disabled");        
        }
        switch (currentTab) {
            case "t1": {
                $(".cont_tab:eq(0)").hide();
                $("#t1").removeClass("active");
                break;
            }
            case "t2": {
                $(".cont_tab:eq(1)").hide();
                $("#t2").removeClass("active");
                break;
            }
            case "t3": {
                $(".cont_tab:eq(2)").hide();
                $("#t3").removeClass("active");
                break;
            }
            default: {
                break;
            }
        }    
        $("#div-tabs").addClass("inactive");
        $("#ul-tabs").addClass("inactive");        
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

function testTimer(){
    if (tab2_valid) {
        tab2_valid = false;
        if (tab1_valid) {
            $("#t3").addClass("ui-state-disabled");
        }                
    } else {
        tab2_valid = true;
        if (tab1_valid) {
            $("#t3").removeClass("ui-state-disabled");
        }        
    }
}

function cancelRecurrence(){
    $("#auxDiv").hide();
    $("#recurrence").hide(); 
    $("#repeat_chkbox").removeAttr("checked") = false;
    $('#short_summary').html("");
}

function saveRecurrence(){

    $("#recurrence-flash").hide();
    $("#recurrence-warning").html("");
    
    var freq = $("#freq").val();

    if ($("#date_radio").attr("checked")) {
        var until = $("#untilDate").val();
    } else if ($("#recur_radio").attr("checked"))
        var count = $("#nr_occurr").val();
    else {
        $("#recurrence-flash").show();        
        $("#recurrence-warning").html(end_rule_string);        
        return;
    }

    var interval = $("#interval").val();

    var week_str = "";
    if (freq == "WEEKLY") {
        var byday = getCheckedDays();
        if (byday.length == 0) {
            $("#recurrence-flash").show();
            $("#recurrence-warning").html(select_day_string);                    
            return;
        }
        byday = byday.toString();

        var weekdays = ["#Sunday_desc","#Monday_desc","#Tuesday_desc","#Wednesday_desc","#Thursday_desc","#Friday_desc","#Saturday_desc"];

        for (var i in weekdays) {
            if ($(weekdays[i]).html())
                week_str += $(weekdays[i]).html() + " ";
        }
    }

    var sum_desc = $("#short_desc").html() + " ";
    sum_desc += week_str;
    sum_desc += $("#until_desc").html();
    $("#short_summary").html(sum_desc);

    $("#auxDiv").hide();
    $("#recurrence").hide();
    $("#recurrence-edit").show();
}

/*----------------------------------------------------------------------------*/
// INICIO DAS FUNÇÕES DO MAPA                                                 //
//                                                                            //
//  PREFIXO "edit_" INDICA USO DO SCRIPT NA TAB "Endpoints & Bandwidth"       //
//  PREFIXO "view_" INDICA USO DO SCRIPT NA TAB "Confirmation"                //
/*----------------------------------------------------------------------------*/

// EDIT FUNCTIONS

//inicializa mapa com redes marcadas para a definicao dos endpoints
function edit_initializeMap() {
    contextMenu.hide();
    var latitudes = [];
    var longitudes = [];
    
    latitudes[0] = -23.051931;
    longitudes[0] = -60.975511;
    
    latitudes[1] = -19.051931;
    longitudes[1] = -58.975511;    

    latitudes[2] = -20.051931;
    longitudes[2] = -64.975511;    
    
    var rede1 = new google.maps.LatLng(latitudes[0], longitudes[0]);
    var rede2 = new google.maps.LatLng(latitudes[1], longitudes[1]);
    var rede3 = new google.maps.LatLng(latitudes[2], longitudes[2]);    

    edit_addMarker(rede1, "Rede 1");
    edit_bounds.push(rede1);
    edit_addMarker(rede2, "Rede 2");
    edit_bounds.push(rede2);
    edit_addMarker(rede3, "Rede 3");
    edit_bounds.push(rede3); 
    
    google.maps.event.addListener(edit_map, 'click', function() {
        contextMenu.hide();        
    }); 
    
    edit_setBounds(edit_bounds);
}

//adiciona marcadores de endpoints no mapa 
function edit_addMarker(location, name) {

    marker = new StyledMarker({
        id: name,
        position: location,
        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
            color:"3A5879"
        }),
        map:edit_map
    });

    google.maps.event.addListener(marker, "click", function(marker) {
        edit_markerClick(location, name);
    });

    google.maps.event.addListener(marker, "mouseover", function() {

        selectedMarker = new StyledMarker({
            id: name,
            position: location,
            styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
                color:"3A5879"
            }),
            map:edit_map
        });

        infowindow = new google.maps.InfoWindow({
            content: //"<b>" + domain_string + "</b>: " + domain +
            "<b>" + network_string + "</b>: " + name +
            "<br><b>" + coordinates_string + "</b>: " + location,
            disableAutoPan: true
        });
        selectedMarker.setMap(null);
        infowindow.open(edit_map, selectedMarker);
    });
  
    google.maps.event.addListener(marker, "mouseout", function() {
        infowindow.close(edit_map);
    });
  
    // Display and position the menu
    google.maps.event.addListener(marker, 'rightclick', function() {
        if (path.length != 2) {
            contextMenu.hide();
            contextMenu.find('a').click( function() {
                // fade out the menu
                contextMenu.fadeOut(75);

                // The link's href minus the #
                var action = $(this).attr('href').substr(1);

                switch ( action )
                {
                    case 'fromHere':
                        edit_markerClick(location, name);
                        break;
                    case 'toHere':
                        edit_markerClick(location, name);
                        break;
                }
                return false;
            });
            var projection = overlay.getProjection(),
            pos = projection.fromLatLngToContainerPixel(location),
            x = pos.x,
            y = pos.y;
            selectedMarker.setMap(null);
            
            // save the clicked location

            // adjust if clicked to close to the edge of the map
            if (x > mapDiv.width() - contextMenu.width())
                x -= contextMenu.width();
        
            if (y > mapDiv.height() - contextMenu.height())
                y -= contextMenu.height();

            // Set the location and fade in the context menu
            contextMenu.css({
                top: y,
                left: x
            }).fadeIn(100);
        }
    });  

    edit_markersArray.push(marker);
    marker.setMap(edit_map);
}

//funcao que gerencia os "clicks" nos marcadores
function edit_markerClick(location, name){
    contextMenu.hide();
    selectedMarker = new StyledMarker({
        id: name,
        position: location,
        clickable: false,
        styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
            color:"eee"
        }),
        map:edit_map
    });   
    
    for (var i = 0; i < edit_markersArray.length; i++) {
        if ((edit_markersArray[i].id == name) && (edit_markersArray[i].position == location)) {
            edit_markersArray[i].setClickable(false);
        }
    }    
    
    path.push(location);
    
    if (path.length == 1) {
        contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
        contextMenu.append('<li><a href="#toHere">' + to_here_string + '</a></li>');
        contextMenu.bind('contextmenu', function() {
            return false;
        });
        $(edit_map.getDiv()).append(contextMenu);

    } else if (path.length == 2) {
        contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
        contextMenu.append('<li><a href="#fromHere">' + from_here_string + '</a></li>');
        contextMenu.bind('contextmenu', function() {
            return false;
        });
        $(edit_map.getDiv()).append(contextMenu);
    }
    
    selectedMarker.setMap(null);
    
    if (path.length == 2) {
        for (i = 0; i < edit_markersArray.length; i++) {
            edit_markersArray[i].setClickable(false);
        }         
        edit_drawPath(path);
        $("#slider").slider( "option", "disabled", false );
        $("#amount_label").show();        
        $("#amount").show();
        tab1_valid = true;
        if (tab2_valid) {
            $("#t3").removeClass("ui-state-disabled");
        }        
    }
}

// desenha uma linha entre dois endpoints selecionados
function edit_drawPath(flightPlanCoordinates) {
    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });
    line.setMap(edit_map);
    edit_lines.push(line);
    edit_setBounds(flightPlanCoordinates);  
    view_Circuit();
}

// reseta o mapa ao estado original e desabilita o slider
function edit_clearAll(){
    $("#slider").slider( "option", "disabled", true );
    $("#amount_label").hide();
    $("#amount").hide();

    edit_clearLines();
    edit_clearMarkers();    
    edit_setBounds(edit_bounds);
    view_clearAll();
     
    
    tab1_valid = false;
    if (tab2_valid) {
        $("#t3").addClass("ui-state-disabled");
    }
}

//limpa as linhas do mapa de edicao
function edit_clearLines() {
    for (var i = 0; i < edit_lines.length; i++) {
        edit_lines[i].setMap(null);
    }  
    path = [];
}

//limpa os marcadores do mapa de edicao
function edit_clearMarkers() {
    for (i = 0; i<edit_markersArray.length; i++) {
        edit_markersArray[i].setClickable(true);
    }    
}

//reseta o menu de pop-up
function edit_resetContextMenu() {
    contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
    contextMenu.append('<li><a href="#fromHere">' + from_here_string + '</a></li>');
    contextMenu.bind('contextmenu', function() {
        return false;
    });
    $(edit_map.getDiv()).append(contextMenu);     
}

// seta os limites do mapa para enquadrar os marcadores ou as rotas traçadas
function edit_setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    edit_map.fitBounds(polylineBounds);
    edit_map.setCenter(polylineBounds.getCenter());
}

// VIEW FUNCTIONS

// alterna entre a visão simples e a visão avançada no mapa
function view_toggleTopology(){
//    clearAll();
//    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
//        var aux = parseFloat(dst_lng_network);
//        aux += 0.0005;
//        dst_lng_network = aux.toString();
//    }
//    var coordinatesArray=[];
//
//    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
//    edit_addMarker(coord_src);
//    bounds.push(coord_src);
//
//    var waypoint = new google.maps.LatLng(-18,-54);    
//    bounds.push(waypoint);
//
//    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
//    edit_addMarker(coord_dst);
//    bounds.push(coord_dst);
//
//    coordinatesArray.push(coord_src);
//    coordinatesArray.push(waypoint);
//    coordinatesArray.push(coord_dst);
//
//    if (topology) {
//        topology = false;
//        edit_addMarker(waypoint);
//        drawTopology(coordinatesArray);
//    } else {
//        topology = true;
//        edit_drawPath(coordinatesArray);
//    }
//    edit_setBounds(bounds);
}

// inicializa o mapa para visualizacao do circuito
function view_Circuit(){
    var coord_src = path[0];
    view_addMarker(coord_src);
    view_bounds.push(coord_src);
    var coord_dst = path[1];
    view_addMarker(coord_dst);
    view_bounds.push(coord_dst);
    view_setBounds(view_bounds);
    view_drawPath(path);
}

// adiciona marcadores no mapa para visualizacao do circuito
function view_addMarker(location) {
    marker = new google.maps.Marker({
        position: location,
        map:view_map
    });

    view_markersArray.push(marker);
    marker.setMap(view_map);
}

// desenha linha entre endpoints para visualizacao do circuito
function view_drawPath(flightPlanCoordinates) {
    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });
    line.setMap(view_map);
    view_lines.push(line);
    view_setBounds(flightPlanCoordinates);        
}

// deseha topologia para a visao avancada
function view_drawTopology(coordinatesArray){
    var flightPlanCoordinates = [];
    for (var i=0; i<coordinatesArray.length; i++){
        flightPlanCoordinates.push(coordinatesArray[i]);
    }
    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });

    line.setMap(view_map);
    view_lines.push(line);
}

// limpa as linhas do mapa da visualizacao
function view_clearLines(){
    for (var i = 0; i < view_lines.length; i++) {
        view_lines[i].setMap(null);
    }    
}

// limpa os marcadores do mapa de visualizacao
function view_clearMarkers(){
    var j = view_markersArray.length;
    
    for (var i=0; i < j; i++){
        view_markersArray[i].setMap(null);        
    }
    for (i=j; i>0; i--) {
        view_markersArray.pop();
    }
    
}

//reseta os limites originais do mapa
function view_clearBounds(){
    var j = view_bounds.length;
    for (var i=j; i>0; i--){
        view_bounds.pop();        
    }
    view_setBounds(view_bounds);    
}

// reseta o mapa ao estado original
function view_clearAll(){
    view_clearMarkers();
    view_clearLines();
    view_clearBounds();
}

function view_setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    view_map.fitBounds(polylineBounds);
    view_map.setCenter(polylineBounds.getCenter());
}