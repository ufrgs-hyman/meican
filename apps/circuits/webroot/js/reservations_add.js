/* VARIAVEL PARA VALIDACAO DO TIMER */
var timerValid = true; // se for verdadeiro, a validacao do timer esta correta

/* VARIAVEIS PARA CONTROLE DO MAPA*/
var srcSet = false;  // origem selecionada
var dstSet = false;  // destino selecionado
var edit_markersArray = new Array(); // array dos marcadores no mapa de criacao da reserva
var view_markersArray = new Array(); // array dos marcadores no mapa de visualizacao da reserva
var edit_bounds = new Array(); // limites geograficos do mapa na criacao da reserva
var edit_lines = new Array(); // linhas interligando os pontos do circuito na criacao da reserva
var view_bounds = new Array(); // limites geograficos do mapa na visualizacao da reserva
var view_lines = new Array();  // linhas interligando os pontos do circuito na visualizacao da reserva
var path = new Array();  // rota completa do circuito -> origem, ponto intermediario 1, ponto intermediario 2, ... , destino. Se tiver tamanho 2, so ha origem e destino.

/* VARIAVEIS PARA FUTURA INCLUSAO DE PONTOS INTERMEDIARIOS*/
var waypoints = new Array(); // pontos intermediarios do circuito
var waypointsMarkers = new Array(); // marcadores dos pontos intermediarios do circuito
var counter = 0; // contador de numero de pontos intermediarios

/* */ 
var src_networks = null;
var dst_networks = null;
var src_urn = null;
var dst_urn = null;

/* VARIAVEIS PARA DESENHO DO MAPA*/
var edit_map; // Nome do mapa na criacao de reservas
var view_map; // Nome do mapa na visualizacao de reservas
var view_center; // Centro do mapa na criacao de reservas
var overlay; // Camada de overlay para sobreposicoes
var mapDiv; 
var contextMenu = null; // menu pop-up ao clicar nas redes do mapa
var editMapHandler;

var useView = false;

function validateEndpoints() {
    // verifica origem
    if (src_urn) {
        if ($("#src_vlanTagged").attr("checked")) {
            $("#src_vlanType").val("tagged");
            if (!checkVLAN("src")) {
                setFlash(flash_srcVlanInv, "warning");
                return false;
            }
        } else
            $("#src_vlanType").val("untagged");
    } else {
        setFlash(flash_sourceReq, "warning");
        return false;
    }
    
    // verifica destino
    if (dst_urn) {
        if ($("#dst_vlanTagged").attr("checked")) {
            $("#dst_vlanType").val("tagged");
            if (!checkVLAN("dst")) {
                setFlash(flash_dstVlanInv, "warning");
                return false;
            }
        } else
            $("#dst_vlanType").val("untagged");
    } else {
        setFlash(flash_destReq, "warning");
        return false;
    }
    
    return true;
}

function validateTimer() {
    writeSummaryToInput();
    calcDuration();
    return timerValid;
}

function validateReservationForm() {
    if (!$('#res_name').val().length) {
        setFlash(flash_nameReq);
        js_submit_form = false;
        $('#res_name').focus();
        return false;
    }
    
    if (validateEndpoints() && validateBand($('#bandwidth').val()) && validateTimer()) {
        return true;
    } else {
        js_submit_form = false;
        $('#res_name').focus();
        return false;
    }
        
    /*var hops = "";
    $.each($("#hops_line select"), function() {
        if (this.value != -1)
            hops += this.value + ";";
    });
    if (hops) {
        var path = src_urn + ";";
        path += hops;
        path += dst_urn;
        $("#path").val(path);
    } else {
        $("#path").val("");
    }*/
}

function changeVlanValue(where) {
    var text_vlanId = "#" + where + "_vlanText";
    if ($(text_vlanId).val().length == 0) {
        $(text_vlanId).val(any_string);
    }
}


function cancelRecurrence(){
    if ($("#repeat_chkbox").attr("checked") && ($("#recurrence-edit").is(":visible"))) {
        $("#recurrence").hide(); 
    } else {
        $("#recurrence").hide(); 
        $("#repeat_chkbox").removeAttr("checked");
        $('#recurrence_summary').empty();
        $('#summary_input').val("");
        refreshSummary();
    }
}

/*
$.fn.extend({
    slideRight: function() {
        return this.each(function() {
            $(this).animate({
                width: 'show'
            });
        });
    },
    slideLeft: function() {
        return this.each(function() {
            $(this).animate({
                width: 'hide'
            });
        });
    },
    slideToggleWidth: function() {
        return this.each(function() {
            var el = $(this);
            if (el.css('display') == 'none') {
                el.slideRight();                
            } else {
                el.slideLeft();
            }
        });
    }
});

function showVlanConf() {
    if ($("#showVlan_checkbox").attr("checked")) {
        $("#div_vlan").slideDown();
    }else {
        $("#div_vlan").slideUp();
    }
}*/

/*this function generates random colors to differentiate domains on the map*/
function genHex(domainId) {
    var firstColor = "3a5879";
    if (domainId == 0) {
        return firstColor;
    } else {
        var color = parseInt(firstColor,16);
        color += (domainId * parseInt("d19510", 16));
        if ((color == "eee") && (color == "eeeeee")) {
            color = "dddddd";
            color = color.toString(16);
        } else if (color > 0xFFFFFF) {
            color = color.toString(16);
            color = color.substring(1, color.length);
        } else {
            color = color.toString(16);
        }
        return color;            
    }
}

/* This function was used to add waypoints to the circuit. Currently is not being used, but will be used in the future */
/*
function moreFields() {
    counter++;
    var newFields = document.getElementById('addHops').cloneNode(true);
    newFields.id = '';
    newFields.style.display = 'block';
    var newField = newFields.childNodes;
    for (var i=0;i<newField.length;i++) {
        var theId = newField[i].id;
        if (theId) {
            newField[i].id = theId + counter;
        }
    }
    var insertHere = document.getElementById('writeHops');
    insertHere.parentNode.insertBefore(newFields,insertHere);
    var selectId = "#selectHops" + counter;
    //fillUrnBox(selectId, urn_string);
}

/* Analogue to the function moreFields(), the function lessFields() was used to remove waypoints from the circuit. */
/*
function lessFields(elem) {
    elem.parentNode.parentNode.removeChild(elem.parentNode);
    edit_mapPlaceDevice();
}*/


/*----------------------------------------------------------------------------*/
// INICIO DAS FUNÇÕES DO MAPA                                                                                           //
/*----------------------------------------------------------------------------*/

// seta os limites do mapa para enquadrar os marcadores ou as rotas traçadas
function edit_setBounds(flightPlanCoordinates){
    if (flightPlanCoordinates == null)
        flightPlanCoordinates = edit_bounds;
    polylineBounds = new google.maps.LatLngBounds();
    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    edit_map.fitBounds(polylineBounds);
    edit_map.setCenter(polylineBounds.getCenter());
}

//
//function decodeUrn(urn) {
//    var string_aux = "domain=";
//    var domainTopology = urn.substring((urn.indexOf("domain=") + string_aux.length), urn.indexOf(":node="));    
//    string_aux = ":node=";
//    var deviceTopology = urn.substring((urn.indexOf(":node=") + string_aux.length), urn.indexOf(":port="));
//
//
//    for (var i in domains) {
//        if (domains[i].topology_id == domainTopology) {
//            for (var j in domains[i].networks) {
//                for (var k in domains[i].networks[j].devices) {                    
//                    if (domains[i].networks[j].devices[k].topology_node_id == deviceTopology) {
//                        var waypoint = ({
//                            location: new google.maps.LatLng(domains[i].networks[j].latitude, domains[i].networks[j].longitude),
//                            domain_id: domains[i].id,
//                            domain_name: domains[i].name,
//                            network_id: domains[i].networks[j].id, 
//                            network_name: domains[i].networks[j].name, 
//                            device_id: domains[i].networks[j].devices[k].id,
//                            device_name: domains[i].networks[j].devices[k].name + " " + domains[i].networks[j].devices[k].model
//                        });
//                    }
//                }
//            }
//        }
//    }
//    return waypoint;
//}

//function edit_addTopologyMarker(waypoint) {
//    
//    marker = new google.maps.Marker({
//        id : waypoint.device_id,
//        position: waypoint.location,
//        
//        map:edit_map
//    });
//
//    google.maps.event.addListener(marker, "mouseover", function(marker) {
//
//        infowindow = new google.maps.InfoWindow({
//            content: "<b>" + domain_string + "</b>: " + waypoint.domain_name + "<br/>" +
//            "<b>" + network_string + "</b>: " + waypoint.network_name + "<br/>" +
//            "<b>" + device_string + "</b>: " + waypoint.device_name,
//            disableAutoPan: true
//        });
//        infowindow.open(edit_map, marker);
//    });
//  
//    google.maps.event.addListener(marker, "mouseout", function() {
//        infowindow.close(edit_map);
//    });
//  
//    // Display and position the menu    
//    waypointsMarkers.push(marker);
//    marker.setMap(edit_map);
//}

//function edit_redrawPath() {
//
//    for (var i=1; i<=counter; i++) {
//        var selectId = "#selectHops" + counter;
//        if ($(selectId).val()) {
//            if ($(selectId).val() != -1) {
//                var waypoint = decodeUrn($(selectId).val());
//                waypoints.push(waypoint.location);
//            }
//        }
//            
//    }
//    
//    var flightPlanCoordinates = new Array();
//    
//    flightPlanCoordinates[0] = path[0].position;
//    
//    for(i=0; i<waypoints.length; i++) {
//        flightPlanCoordinates[i+1] = waypoints[i];
//    }
//    
//    var length = flightPlanCoordinates.length;
//    
//    flightPlanCoordinates[length] = path[1].position;
//
//    var line = new google.maps.Polyline({
//        path: flightPlanCoordinates,
//        strokeColor: "#0000FF",
//        strokeOpacity: 0.5,
//        strokeWeight: 4
//    });
//    line.setMap(edit_map);
//    edit_lines.push(line);
//    edit_setBounds(flightPlanCoordinates);  
//    view_Circuit();    
//}

//function toggleCluster(toggle, arrayMarkers){
//
//if (toggle) {
//        markerCluster = new MarkerClusterer(edit_map, arrayMarkers);      
//        google.maps.event.addListener(markerCluster, 'clustermouseover',function(markerCluster) {
//                var stringInfo = "<h4>&nbsp;&nbsp;" + cluster_information_string + "</h4>&nbsp;&nbsp;";
//                stringInfo += " <b>" + networks_string + "</b>: <br>&nbsp;&nbsp;";
//                clusterContent = markerCluster.getMarkers();
//                selectedMarker = new StyledMarker({
//                    domain_id: clusterContent[0].domain_id,
//                    domain_name: clusterContent[0].domain_name,
//                    id: clusterContent[0].network_id,
//                    label: clusterContent[0].label,
//                    position: clusterContent[0].position,
//                    styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
//                        color:clusterContent[0].styleIcon.color
//                    }),
//                    map:edit_map
//                });
//                for (var i=0; i<clusterContent.length;i++){
//                        stringInfo+= " " + clusterContent[i].label +"&nbsp;&nbsp;";
//                        stringInfo+= " (" + clusterContent[i].domain_name +")<br>&nbsp;&nbsp;";
//                }
//
//                selectedMarker.setMap(null);
//                infowindow = new google.maps.InfoWindow({
//                    content: stringInfo,
//                    disableAutoPan: true
//                });
//                infowindow.open(edit_map, selectedMarker);
//
//        });
//        google.maps.event.addListener(markerCluster, 'clustermouseout',function() {
//                infowindow.close(edit_map);
//        });
//        google.maps.event.addListener(markerCluster, 'clusterclick',function() {
//                if (infowindow) {
//                    infowindow.close(edit_map);
//                }
//        });
//    } else {
//        markerCluster.clearMarkers(arrayMarkers);
//    }
//}

// VIEW FUNCTIONS

// alterna entre a visão simples e a visão avançada no mapa
//function view_toggleTopology(){
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
//}

// inicializa o mapa para visualizacao do circuito

//function view_Circuit(){
//    var coord_src = path[0].position;
//    view_addMarker(coord_src, "src");
//    view_bounds.push(coord_src);
//    var coord_dst = path[1].position;
//    view_addMarker(coord_dst, "dst");
//    view_bounds.push(coord_dst);
//    view_setBounds(view_bounds);
//    var sourceDest = new Array();
//    sourceDest.push(path[0].position);
//    sourceDest.push(path[1].position);
//    view_drawPath(sourceDest);
//}

// adiciona marcadores no mapa para visualizacao do circuito
//function view_addMarker(location, where) {
//    var color;
//    
//    if (where == "src") {
//        color = "0000EE";
//    } else if (where == "dst") {
//        color = "FF0000";
//    }
//    
//    marker = new StyledMarker({
//        position: location,
//        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
//            color:color
//        }),
//        map:view_map
//    });    
//
//    view_markersArray.push(marker);
//    marker.setMap(view_map);
//}

// desenha linha entre endpoints para visualizacao do circuito
//function view_drawPath(flightPlanCoordinates) {
//    var line = new google.maps.Polyline({
//        path: flightPlanCoordinates,
//        strokeColor: "#0000FF",
//        strokeOpacity: 0.5,
//        strokeWeight: 4
//    });
//    line.setMap(view_map);
//    view_lines.push(line);
//    view_setBounds(flightPlanCoordinates);        
//}

// Draw the circuit including waypoints
//function view_drawTopology(coordinatesArray){
//    var flightPlanCoordinates = [];
//    for (var i=0; i<coordinatesArray.length; i++){
//        flightPlanCoordinates.push(coordinatesArray[i]);
//    }
//    var line = new google.maps.Polyline({
//        path: flightPlanCoordinates,
//        strokeColor: "#0000FF",
//        strokeOpacity: 0.5,
//        strokeWeight: 4
//    });
//
//    line.setMap(view_map);
//    view_lines.push(line);
//}

// limpa as linhas do mapa da visualizacao
//function view_clearLines(){
//    for (var i = 0; i < view_lines.length; i++) {
//        view_lines[i].setMap(null);
//    }    
//}

// limpa os marcadores do mapa de visualizacao
//function view_clearMarkers(){
//    var j = view_markersArray.length;
//    
//    for (var i=0; i < j; i++){
//        view_markersArray[i].setMap(null);        
//    }
//    for (i=j; i>0; i--) {
//        view_markersArray.pop();
//    }
//    
//}

//reseta os limites originais do mapa
//function view_clearBounds(){
//    var j = view_bounds.length;
//    for (var i=j; i>0; i--){
//        view_bounds.pop();        
//    }
//    view_setBounds(view_bounds);    
//}

// reseta o mapa ao estado original
//function view_clearAll(){
//    view_clearMarkers();
//    view_clearLines();
//    view_clearBounds();       
//}

//function view_setBounds(flightPlanCoordinates){
//    polylineBounds = new google.maps.LatLngBounds();
//
//    for (i=0; i<flightPlanCoordinates.length; i++) {
//        polylineBounds.extend(flightPlanCoordinates[i]);
//    }
//    view_map.fitBounds(polylineBounds);
//    view_map.setCenter(polylineBounds.getCenter());
//}



/*----------------------------------------------------------*/
// INICIO DAS FUNÇÕES AVANÇADAS DE FLUXO                                      //
/*---------------------------------------------------------*/



function map_changeNetwork(where, network_id, domain_id) {
    var network = "#" + where + "_network";
    var device_id = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    $(device_id).disabled(false);
    //$("#" + where + "_vlanTagged").disabled(false);

    map_clearVlanConf(where);
    clearSelectBox(device_id);

    if ($(network).html() != "") {
        var devices = [];
        for (var i=0; i<domains.length; i++){
            for (var j=0; j<domains[i].networks.length; j++){
                for (var k=0; k<domains[i].networks[j].devices.length; k++){
                    if ((domains[i].id == domain_id) && (domains[i].networks[j].id == network_id)) {
                        devices.push(domains[i].networks[j].devices[k]);
                    }
                }
            }
        }
        if (where == 'src') {                            // if desired endpoint is a source endpoint, it's necessary to check for permissions'
            fillSelectBox(device_id, devices, -1, true); // -1 indicates that this function uses a fourth parameter | True indicates that is necessary to check for permissions before filling the box
        }
        else {                                           //if desired checkpoint is a destination endpoint, then there's no need for permission'
            fillSelectBox(device_id, devices);
        }    
        $(device_id).slideDown(); //TODO: pq slideDown aqui?
    }
}

function map_changeDevice(where) {
    var domain_id = "#" + where + "_domain";
    var network_id = "#" + where + "_network";
    var device_id    = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    $(port_id).disabled(false);
    //$(port_id).slideUp();
    map_clearVlanConf(where);
    clearSelectBox(port_id);
    
    if ($(device_id).val() != -1) {
        var ports = map_getPorts($(domain_id).html() ,$(network_id).html(), $(device_id).val(), where);
        if (where == 'src')
            map_fillPorts(port_id, ports, -1, true);
        else
            map_fillPorts(port_id, ports);

        if (ports.length == 1) {
            map_setEndpointConf(where);
        }        
        $(port_id).slideDown();
    } 
}

function map_changePort(where) {
    var port_id = "#" + where + "_port";
    map_clearVlanConf(where);
    if ($(port_id).val() != -1) {
        map_setEndpointConf(where);
    }
}

function map_getPorts(domain_id, network_id, device_id, where) {
    var devices = map_getDevices(domain_id, network_id);
    var ports = null;
    if (devices) {
        for (var i=0; i<devices.length; i++) {
            if (devices[i].id == device_id) {
                //var confirmation_device = "#confirmation_" + where + "_device";                
                //$(confirmation_device).html(devices[i].name);
                ports = devices[i].ports;
                break;
            }
        }
    }
    return ports;
}

function map_getDevices(domain_id, network_id) {
    var devices = null;
    for (var i=0; i<domains.length; i++) {
        if (domains[i].name == domain_id){
            for (var j=0; j<domains[i].networks.length; j++) {
                if (domains[i].networks[j].name == network_id) {
                    devices = domains[i].networks[j].devices;
                    break;
                }
            }
        }
    }
    return devices;
}

function map_clearVlanConf(where) {
    // var untagged_htmlId = "#" + where + "_vlanUntagged";
    var tagged_htmlId = "#" + where + "_vlanTagged";
    var text_htmlId = "#" + where + "_vlanText";
    var tip_htmlId = "#" + where + "_vlanTip";

    $(tip_htmlId).html("");
    $(text_htmlId).attr('title', '').val("").disabled().next().html("").disabled();

    /*$(untagged_htmlId).removeAttr('checked');
    $(untagged_htmlId).attr('disabled','disabled');*/

    $(tagged_htmlId).removeAttr('checked');
    $(tagged_htmlId).disabled();
    
    $("#bandwidth").spinner('disable');
    $('#bandwidth_un').disabled();

    if (where == "src") {
        src_urn = null;
        src_vlan_min = null;
        src_vlan_max = null;
        src_vlan_validValues = null;
        src_max_cap = null;
        src_min_cap = null;
        src_div_cap = null;
    } else if (where == "dst") {
        dst_urn = null;
        dst_vlan_min = null;
        dst_vlan_max = null;
        dst_vlan_validValues = null;
        dst_max_cap = null;
        dst_min_cap = null;
        dst_div_cap = null;
    }
}

function map_fillPorts(htmlId, portsArray, current_port, check_allow) {
    clearSelectBox(htmlId);
    for (var i=0; i < portsArray.length; i++) {
        if ((check_allow) && (portsArray[i].allow_create==false)) {
            continue;
        }
        else {
            if ((portsArray[i].port_number == current_port) || (portsArray.length == 1))
                $(htmlId).append('<option selected="true" value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
            else
                $(htmlId).append('<option value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
        }
    }
}

function map_setEndpointConf(where) {

    /*var untagged_htmlId = "#" + where + "_vlanUntagged";*/
    var tagged_htmlId = "#" + where + "_vlanTagged";
    var text_htmlId = "#" + where + "_vlanText";
    var tip_htmlId = "#" + where + "_vlanTip";

    var urnData = map_getUrnData(where);

    var temp = new Array();
    var virgula = urnData.vlan.search(",");
    var range = urnData.vlan.search("-");

    var allowTag = true;
    var allowUntag = true;

    var vlan_min = null;
    var vlan_max = null;
    var vlan_validValues = null;

    if (virgula != -1) {
        temp = urnData.vlan.split(",");
        if (range != -1) {
            // possui virgula e range. Ex: "0,3000-3500"
            if (temp[0] != 0)
                allowUntag = false;
            temp = temp[1].split("-");
            vlan_min = temp[0];
            vlan_max = temp[1];
            $(text_htmlId).val(vlan_min);
        } else {
            // possui virgula, mas nao possui range. Ex: "3000,3001,3002" ou "0,3000,3001,3002"
            if (temp[0] != 0) {
                allowUntag = false;
                vlan_validValues = urnData.vlan;
            } else
                vlan_validValues = urnData.vlan.substr(2);
        }
    } else {
        if (range != -1) {
            // nao possui virgula, mas possui range. Ex: "3000-3500"
            temp = urnData.vlan.split("-");
            vlan_min = temp[0];
            vlan_max = temp[1];
            allowUntag = false;
            $(text_htmlId).val(vlan_min);
        }else {
            // nao possui virgula nem range. Ex: "0" ou "3000"
            vlan_validValues = urnData.vlan;
            if (vlan_validValues == 0) {
                allowTag = false;
            } else {
                // um valor só para VLAN
                $(text_htmlId).val(vlan_validValues);
                allowUntag = false;
            }
        }
    }

    if (allowTag) {
        // pode ser tagged
        $(tagged_htmlId).disabled(false);

        if (vlan_min && vlan_max){
            $(text_htmlId).attr('title', vlan_min + ' - ' + vlan_max).next().html("("+vlan_min + ' - ' + vlan_max+")");
        //$(tip_htmlId).html(value_string + ': ' + vlan_min + ' - ' + vlan_max);
        } else if (vlan_validValues) {
            $(text_htmlId).attr('title', vlan_validValues).next().html("("+vlan_validValues+")");
        //$(tip_htmlId).html(value_string + ': ' + vlan_validValues);
        }

        if (allowUntag) { //TODO: verificar isso
        // pode ser untagged também
        /*$(untagged_htmlId).removeAttr('disabled');
            $(untagged_htmlId).attr('checked','yes');*/
        } else {
            $(tagged_htmlId).disabled();
            $(tagged_htmlId).attr('checked','yes');
            $(text_htmlId).disabled(false);
        }
    } else {
    // não pode ser tagged, significa que só pode ser untagged
    /*$(untagged_htmlId).removeAttr('disabled');
        $(untagged_htmlId).attr('checked','yes');*/
    }

    if (where == "src") {
        src_urn = urnData.urn_string;
        src_vlan_min = vlan_min;
        src_vlan_max = vlan_max;
        src_vlan_validValues = vlan_validValues;
        src_max_cap = urnData.max_capacity;
        src_min_cap = urnData.min_capacity;
        src_div_cap = urnData.granularity;
        $("#src_urn").val(src_urn);
    } else if (where == "dst") {
        dst_urn = urnData.urn_string;
        dst_vlan_min = vlan_min;
        dst_vlan_max = vlan_max;
        dst_vlan_validValues = vlan_validValues;
        dst_max_cap = urnData.max_capacity;
        dst_min_cap = urnData.min_capacity;
        dst_div_cap = urnData.granularity;
        $("#dst_urn").val(dst_urn);
    }
    
    enableBandwidthSpinner();
}

function enableBandwidthSpinner() {
    if (src_urn && dst_urn) {
        var bmin_tmp = (src_min_cap >= dst_min_cap) ? src_min_cap : dst_min_cap;
        var bmax_tmp = (src_max_cap <= dst_max_cap) ? src_max_cap : dst_max_cap;
        var bdiv_tmp = (src_div_cap == dst_div_cap) ? src_div_cap : band_div;
        
        $('#bandwidth').spinner({min: bmin_tmp, max: bmax_tmp, step: bdiv_tmp}).spinner("enable").disabled(false).trigger('click');
        $('#bandwidth_un').disabled(false);
        $("#bandwidth").val(bmin_tmp);
        $("#bandwidth").trigger("change");
    }
}

function map_getUrnData(where) {
    var domain_id = "#" + where + "_domain";
    var network_id = "#" + where + "_network";
    var device_id = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    var ports = map_getPorts($(domain_id).html(), $(network_id).html(), $(device_id).val());
    var urnData = null;

    for (var i=0; ports.length; i++) {
        if (ports[i].port_number == $(port_id).val()) {
            urnData = ports[i];
            break;
        }
    }
    return urnData;
}

function map_changeVlanType(elem, where) {
    var text_htmlId = "#" + where + "_vlanText";
    if ($(elem).attr('checked')){
        $(text_htmlId).disabled(false);
    } else {
        $(text_htmlId).disabled();        
    }
}

function validateBand(band_value) {
    var band = band_value.replace(/ /g, "");
    if (band >= band_min && band <= band_max) {
        if (band % band_div == 0) {
            return band;
        } else
            return false;
    } else
        return false;
}


(function($) {
    
    $.fn.mapView = {
        inicialize: function(){
            view_center = new google.maps.LatLng(-23.051931,-60.975511);
            var view_myOptions = {
                zoom: 5,
                zoomControl: false,
                center: view_center,
                streetViewControl: false,
                mapTypeControl: false,
                draggable: false,
                disableDoubleClickZoom: true,
                keyboardShortcuts: false,
                scrollwheel: false,
                backgroundColor: "white",
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };
            view_map = new google.maps.Map(document.getElementById("view_map_canvas"), view_myOptions);


            google.maps.event.trigger(view_map, 'resize');
            view_map.setZoom( view_map.getZoom() );
        }
    }
    
    /*função para criar mapa de edição */
    $.fn.mapEdit = {
        
        inicialize: function(){ /*inicializa mapa */
        
            var edit_myOptions = {
                zoom: 5,
                center: new google.maps.LatLng(-23.051931,-60.975511),
                streetViewControl: false,
                navigationControlOptions: {
                    style: google.maps.NavigationControlStyle.ZOOM_PAN
                },
                backgroundColor: "white",
                //    mapTypeControl: false,
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };
            edit_map = new google.maps.Map(document.getElementById("edit_map_canvas"), edit_myOptions);
            var RedefineZoomControl = function (map, div, home) {
                var controlDiv = div;

                controlDiv.style.padding = '5px';

                // Set CSS for the control border
                var goHomeUI = document.createElement('DIV');
                goHomeUI.title = 'Click to reset zoom';
                controlDiv.appendChild(goHomeUI);
  
                // Set CSS for the control interior
                var goHomeText = document.createElement('DIV');
                goHomeText.innerHTML = reset_zoom;
                goHomeUI.appendChild(goHomeText);
                $(goHomeText).addClass("zoom ui-button ui-widget ui-state-default ui-corner-all ui-widget-content").attr('style', "direction: ltr;overflow: hidden;text-align: center;position: relative;font-family: Arial, sans-serif;-webkit-user-select: none;font-size: 12px;line-height: 160%;padding: 0px 6px;border-radius: ;-webkit-box-shadow: rgba(0, 0, 0, 0.347656) 2px 2px 3px;box-shadow: rgba(0, 0, 0, 0.347656) 2px 2px 3px;min-width: 44px;color: black;border: 1px solid #A9BBDF;border-image: initial;padding-left: 6px;font-weight: normal;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FEFEFE), to(#F3F3F3));background-osition: initial initial;background-repeat: initial initial;");
  
                google.maps.event.addDomListener(goHomeUI, 'click', $.fn.mapEdit.resetZoom);
            }
            var homeControlDiv = document.createElement('DIV');
            var homeControl = new RedefineZoomControl(edit_map, homeControlDiv);
            homeControlDiv.index = 1;
            edit_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
	
            google.maps.event.trigger(edit_map, 'resize');
            edit_map.setZoom( edit_map.getZoom() );
            infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(edit_map, 'zoom_changed', function() {
                if (infowindow) {
                    infowindow.close(edit_map);
                }
            });
	
            $.fn.mapEdit.prepareContextMenu();
            MyOverlay.prototype = new google.maps.OverlayView();
            MyOverlay.prototype.onAdd = function() { }
            MyOverlay.prototype.onRemove = function() { }
            MyOverlay.prototype.draw = function() { }
            //	MyOverlay.prototype.draw.setMap(edit_map);
            function MyOverlay(edit_map) {
                this.setMap(edit_map);
            }
            overlay = new MyOverlay(edit_map);
            mapDiv = $(edit_map.getDiv());
            
            //inicializa mapa com redes marcadas para a definicao dos endpoints
    
            for (var i in domains) {
                color = genHex(i);
                for (var j in domains[i].networks) {            
                    if (domains[i].networks[j].latitude) {
                        for (var k=0; k<i; k++){
                            for (var l in domains[k].networks) {
                                if (domains[k].networks[l].latitude) {
                                    if ((domains[i].networks[j].latitude == domains[k].networks[l].latitude) &&
                                        (domains[i].networks[j].longitude == domains[k].networks[l].longitude)) {
                                        domains[i].networks[j].longitude -= -0.015;
                                    }
                                }
                            }
                        }
                        var coord = new google.maps.LatLng(domains[i].networks[j].latitude, domains[i].networks[j].longitude);                
                        this.addMapMarker(coord, domains[i].id, domains[i].name, domains[i].networks[j].id, domains[i].networks[j].name, domains[i].networks[j].allow_create, color);
                        edit_bounds.push(coord);
                    }
                }
            }
            //toggleCluster(true, edit_markersArray);

            google.maps.event.addListener(edit_map, 'click', function() {
                contextMenu.hide();        
            }); 

            if ( !(dstSet) && !(srcSet)) {       
                edit_setBounds(edit_bounds);
            }

            $.fn.mapEdit.setMarkersMap();
        },
    
        clearMapElements: function (elements) {
            for (i in elements)
                elements[i].setMap(null);
            return [];
        },
    
        prepareContextMenu: function(){
            if (contextMenu == null) {
                contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
                contextMenu.append('<li><a href="#fromHere" id="contextItem">' + from_here_string + '</a></li>');
                contextMenu.append('<li><a href="#toHere">' + to_here_string + '</a></li>');
                contextMenu.bind('contextmenu', function() {
                    return false;
                });
                $(edit_map.getDiv()).append(contextMenu);
            }
        },
        
        setMarkersMap: function(){
            for (i in this.notVisibleMarkers){
                this.notVisibleMarkers[i].setMap(null);
            }
            for (i in edit_markersArray){
                edit_markersArray[i].setMap(edit_map);
            }
            for (i in path){
                path[i].selectedMarker.setMap(edit_map);
            }
        },
        
        //adiciona marcadores de endpoints no mapa 
        addMapMarker: function (coord, domain_id, domain_name, network_id, network_name, allow_create, color) {
            for (i in edit_markersArray){
                var mark = edit_markersArray[i];
                if (
                    (mark.domain_id == domain_id) &&
                    (mark.id == network_id))
                    return ;
            } //não re adiciona marcador
            var marker = new StyledMarker({
                domain_id: domain_id,
                domain_name: domain_name,
                id: network_id,
                label: network_name,
                position: coord,
                styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
                    color:color
                }),
                map:edit_map
            });

            var clickFn = function() {
        
                if (allow_create) {
                    $("#contextItem").removeClass("ui-state-disabled");
                }
                else {
                    $("#contextItem").addClass("ui-state-disabled");
                }
                
                contextMenu.find('a').unbind('click');
                contextMenu.find('a[class=ui-state-disabled]').click( function() {
                    setFlash("Ponto desejado não pode ser origem");
                    return false;
                });
                
                contextMenu.find('a[class!=ui-state-disabled]').click( function() {
                    // fade out the menu
                    contextMenu.fadeOut(75);
                    clearFlash();
                    
                
                    // The link's href minus the #
                    switch ( $(this).attr('href').substr(1) )
                    {
                        case 'fromHere':
                            $.fn.mapEdit.markerClick(coord, domain_id, domain_name, network_id, network_name, "src");   
                            break;
                        case 'toHere':
                            $.fn.mapEdit.markerClick(coord, domain_id, domain_name, network_id, network_name, "dst");
                            break;
                    }
                    contextMenu.hide();
                    contextMenu.find('a').unbind('click');
                    return false;
                });
    
                var pos = overlay.getProjection().fromLatLngToContainerPixel(coord),
                x = pos.x,
                y = pos.y;// save the clicked location

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
            };
        
            google.maps.event.addListener(marker, "click", clickFn);
            google.maps.event.addListener(marker, 'rightclick', clickFn);    
    
            var infowindow = new google.maps.InfoWindow({
                content:    "<b>" + domain_string + "</b>: " + domain_name + "<br/>" +
                "<b>" + network_string + "</b>: " + network_name,
                disableAutoPan: true
            });

            google.maps.event.addListener(marker, "mouseover", function() {
                infowindow.open(edit_map, marker);
            });
  
            google.maps.event.addListener(marker, "mouseout", function() {
                infowindow.close(edit_map);
            });
    
            edit_markersArray.push(marker);
            marker.setMap(edit_map);
        },
        
        //funcao que gerencia os "clicks" nos marcadores
        markerClick: function (coord, domain_id, domain_name, network_id, network_name, where){
            var color;
            if (where == "src") {
                if (srcSet){
                    this.clearPoint("src");
                }
                srcSet = true;
                n = 0;
                color = "0000EE";
            } else if (where == "dst") {
                if (dstSet){
                    this.clearPoint("dst");
                }
                dstSet = true;
                n = 1;
                color = "FF0000";
            }   
            path[n] = new StyledMarker({
                domain_id: domain_id,
                domain_name: domain_name,
                id: network_id,
                label: network_name,
                position: coord,
                clickable: false,
                styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
                    color:color
                }),
                unselectedMarker: null,
                map: edit_map,
                map_:edit_map
            });
            
            for (i in edit_markersArray){//retira marcador não selecionado
                if ((edit_markersArray[i].id == network_id) && 
                    (edit_markersArray[i].domain_id == domain_id)) {
                    edit_markersArray[i].setMap(null);
                    path[n].unselectedMarker = edit_markersArray[i];
                    edit_markersArray.splice(i,1);
                    break;
                }
            }
            
            $("#"+where+"_domain").html(domain_name);
            $("#"+where+"_network").html(network_name); 
            map_changeNetwork(where, network_id, domain_id); 
    
            $.fn.mapEdit.preparePath(path[0], path[1]);    
        },
        
        /** Desenha linha entre dois pontos e prepara seleção de banda
         **/
        preparePath: function(from, to) {
            if ((from == null) || (to == null))
                return ;
            
            //$("#showVlan_checkbox").removeAttr("disabled");
            
            $.fn.mapEdit.clearMapElements(edit_lines);
            this.drawPath(new Array(from.position, to.position));
        },
        
        // desenha uma linha entre dois endpoints selecionados
        drawPath: function (flightPlanCoordinates) {
    
            /*$.fn.mapEdit.clearMapElements(edit_selectedMarkers);
    callback_markers();*/

            var line = new google.maps.Polyline({
                path: flightPlanCoordinates,
                strokeColor: "#0000FF",
                strokeOpacity: 0.5,
                strokeWeight: 4
            });
            line.setMap(edit_map);
            edit_lines.push(line);
            //toggleCluster(true,edit_markersArray);
            //toggleCluster(false, edit_selectedMarkers);
            if ( flightPlanCoordinates[0] != flightPlanCoordinates[1] ) {
                edit_setBounds(flightPlanCoordinates);  
            }
    
            //if (useView) {
            //    view_clearAll();
            //    view_Circuit();
            //}
        },
        
        resetZoom: function(){
            edit_setBounds(edit_bounds);
        },
        
        hasPath: function(){
            return 
            (path.length == 2) &&
            (path[0] != null) && 
            (path[1] != null);
        },
    
        clearPoint: function (point) {
            var n = 0;
            if (point == "src") {
                if (!srcSet)
                    return ;
                srcSet = false;
                n=0;
            } else if (point == "dst") {
                if (!dstSet)
                    return ;
                dstSet = false;
                n=1;
            }
            edit_markersArray.push(path[n].unselectedMarker); //coloca marcador de volta
            path[n].unselectedMarker.setMap(edit_map);
            path[n].setMap(null);
            path[n] = null;
            edit_lines = this.clearMapElements(edit_lines);
            waypointsMarkers = this.clearMapElements(waypointsMarkers); //TODO: trocar para remover apenas um ponto
            
            if (!this.hasPath()){
                $("#bandwidth").spinner('disable');
                $('#bandwidth_un').disabled();
            }
            $("#"+point+"_domain,#"+point+"_network").empty();
            $("#"+point+"_device,#"+point+"_port").empty().disabled();//,#src_vlanTagged
            map_clearVlanConf(point);
            
            edit_setBounds();
        //edit_initializeMap();
        },
    
        clearSrc: function () {//limpa ponto de origem
            $.fn.mapEdit.clearPoint('src');
        },
    
        clearDst: function () {//limpa ponto de destino
            $.fn.mapEdit.clearPoint('dst');
        },
        
        clearAll: function (){
            if (path.length != 0) {
                console.debug('clear all path');
                for (var i=counter; i>0; i--) {
                    alert(i);
                    var removeHop = "#removeHop" + counter;
                    if ($(removeHop)) {
                        lessFields($(removeHop));
                    }
                }
                counter = 0;
            }
    
            path = [];
            $.fn.mapEdit.clearMapElements(edit_lines);
            //$.fn.mapEdit.clearMapElements(edit_selectedMarkers);
            $.fn.mapEdit.clearMapElements(edit_markersArray);
            $.fn.mapEdit.clearMapElements(waypointsMarkers);
            edit_setBounds(edit_bounds);    
    
            
            $.fn.mapEdit.prepareContextMenu();
    
            view_clearAll();    
    
            edit_initializeMap();
        }
    
    };
    
    /* resize da janela muda tamanho do mapa */
    var resizefn = function() {
        if ($('#edit_map_canvas'))
            $('#edit_map_canvas').css('width', $('#subtab-points').offset().left-12-$('#tabs-2').offset().left );
    };
    /* **************** DOCUMENT READY !!!! ******************** */
    
    $(function(){
        
        var f = function(){
            var v = ($("#bandwidth").val()/$("#bandwidth").attr('aria-valuemax'))*100;
            if (v>100 || v < 0)
                return ;
            var k = 2*(50-v);
		    
            $('#bandwidth_bar_inside').animate({
                width: v+'%'/*, 
                'background-color': 'rgb('+(Math.round(255*(100-(k<0?0:k))/100))+','+(Math.round(255*(100-(-k<0?0:-k))/100))+',0)'*/
            }, 100);       
        };
        
        $('#bandwidth').attr("min", band_min).attr("max", band_max).attr("step", band_div).numeric().spinner({
            spin: f,
            stop: f
        }).spinner("disable").bind('spin', f).change(f).keyup(f).click(f).scroll(f);
        
        $('#bandwidth_un').disabled();
        /*  if (false){ //configura tabs?
            $('#tabs-res ul').show();
            $('#tabs-3').show();
            $('#tabs-res').tabs({
                select: function(event, ui){
                    clearFlash();
                    // antes de mostrar a aba, copia conteudo dos campos
                    google.maps.event.trigger(view_map, 'resize');
                    view_setBounds(view_bounds);
                }
            });
        } else {*/
        $('#tabs-res ul').hide();
        $('#tabs-3').hide();
        /*$('#repeat_chkbox').button();*/
        /*$('#weekdays input[type=checkbox]').button();*/
    
        $('#src_clearpath').click($.fn.mapEdit.clearSrc);
        $('#dst_clearpath').click($.fn.mapEdit.clearDst);
        $("#bandwidth").spinner('disable');
        $('#bandwidth_un').disabled();
        $("#src_domain,#src_network,#dst_domain,#dst_network").empty();
        $("#src_device,#src_port,#dst_device,#dst_port").empty().disabled();
        
        map_clearVlanConf();
        
        $('#repeat_chkbox').click(showRecurrenceBox);
        $(".recurrence_table input[type=checkbox]").click(function(){
            checkWeekDay(this.id);
        });
        $('#recur_radio, #date_radio, #nr_occurr').change(setUntilType);
        $(window).resize(resizefn);
        var finishfn = function(){
            $(window).unbind('resize');
            $('#main').unbind('start.pjax', finishfn);
        };
        $('#main').bind('start.pjax', finishfn);
        
        $('form#reservation_add').submit(validateReservationForm);
        
        /* quando digita nome, tira overlay */
        $('#res_name').bind('keyup change', function(){
            if ($(this).val())
                $('.tab-overlay').fadeOut();
            else
                $('.tab-overlay').fadeIn();
        }).focus().keyup();
        
        $.fn.mapEdit.inicialize();
        
        initializeTimer();
        
        resizefn();
        
        google.maps.event.trigger(edit_map, 'resize');
        //edit_map.setZoom( edit_map.getZoom() );
    });
    
    $(window).load(resizefn);
	
})(jQuery);

