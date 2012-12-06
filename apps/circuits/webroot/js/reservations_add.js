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

/* */ 
var src_networks = null;
var dst_networks = null;
var src_urn = null;
var dst_urn = null;
var src_partial_urn = null;
var dst_partial_urn = null;

/* VARIAVEIS PARA DESENHO DO MAPA*/
var edit_map; // Nome do mapa na criacao de reservas
var view_map; // Nome do mapa na visualizacao de reservas
var view_center; // Centro do mapa na criacao de reservas
var overlay; // Camada de overlay para sobreposicoes
var mapDiv; 
var contextMenu = null; // menu pop-up ao clicar nas redes do mapa
var contextMenuWaypoint = null; //meun pop-up para waypoints
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
    
    if (src_urn != dst_urn) {
        return true;
    } else {
        setFlash(flash_sameSrcDst, "error");
        return false;
    }
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
        if ($("#src_vlanText").val() == "any") {
            $("#src_vlanText").val("");
        }
        if ( ($("#src_vlanTagged").attr("checked")) && ($("#src_vlanText").attr("placeholder", "any")) ){
            $("#src_vlanText").attr("placeholder","");
        }
        if ($("#dst_vlanText").val() == "any") {
            $("#dst_vlanText").val("");
        }
        if ( ($("#dst_vlanTagged").attr("checked")) && ($("#dst_vlanText").attr("placeholder", "any")) ){
            $("#dst_vlanText").attr("placeholder","");
        }        
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

function checkVLAN(where) {

    var vlan_value = null;
    var vlan_min = null;
    var vlan_max = null;
    var vlan_validValues = null;

    if (where == "src") {
        vlan_value = $("#src_vlanText").val();
        vlan_min = parseInt(src_vlan_min);
        vlan_max = parseInt(src_vlan_max);
        vlan_validValues = src_vlan_validValues;
    } else if (where == "dst") {
        vlan_value = $("#dst_vlanText").val();
        vlan_min = parseInt(dst_vlan_min);
        vlan_max = parseInt(dst_vlan_max);
        vlan_validValues = dst_vlan_validValues;
    } else return false;

    if (!vlan_value)
        return true;

    if (vlan_min && vlan_max) {
        if ((vlan_value >= vlan_min) && (vlan_value <= vlan_max))
            return true;
        else
            return false;
    } else if (vlan_validValues) {
        var temp = new Array();
        var valid = false;
        temp = vlan_validValues.split(",");
        for (var i=0; i < temp.length; i++) {
            if (vlan_value == temp[i])
                valid = true;
        }
        if (valid)
            return true;
        else
            return false;
    } else
        return false;
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
// Funções para buscar endpoints
/*----------------------------------------------------------------------------*/

function fillPoint(point, endpointObj) {
    var checkAcl = false;
    if (point == "src")
        checkAcl = true;
    
    if (endpointObj.domain == -1) {
        setDialogMessage(flash_domainNotFound, "error");
        return;
    }
    
    var dom_found = false;
    var net_found = false;
    
    var network_name = null;
    var coord = null;
    
    for (var i in domains) {
        if (domains[i].id == endpointObj.domain) {
            dom_found = true;
            var domain_name = domains[i].name;
            
            if (endpointObj.network != null) {
                for (var j in domains[i].networks) {
                    network_name = domains[i].networks[j].name;
                    if (domains[i].networks[j].id == endpointObj.network) {
                        if (!checkAcl || domains[i].networks[j].allow_create) {
                            coord = new google.maps.LatLng(domains[i].networks[j].latitude, domains[i].networks[j].longitude);
                            $.fn.mapEdit.markerClick(coord, endpointObj.domain, domain_name, domains[i].topology_id, endpointObj.network, network_name, point);
                            net_found = true;
                            break;
                        } else {
                            setDialogMessage(flash_pointCannotBeSource, "error");
                            return;
                        }
                    }
                }
            } else {
                if (domains[i].networks.length == 1) {
                    if (!checkAcl || domains[i].networks[0].allow_create) {
                        network_name = domains[i].networks[0].name;
                        coord = new google.maps.LatLng(domains[i].networks[0].latitude, domains[i].networks[0].longitude);
                        $.fn.mapEdit.markerClick(coord, endpointObj.domain, domain_name, domains[i].topology_id, domains[i].networks[0].id, network_name, point);
                        net_found = true;
                    } else {
                        setDialogMessage(flash_pointCannotBeSource, "error");
                        return;
                    }
                }
            //                else {
            //                    $("#"+point+"_domain").html(domain_name);
            //                    setDomainPartialURN(point, domains[i].topology_id);
            //                }
            }
        }
        if (dom_found)
            break;
    }
    
    if (dom_found && endpointObj.device == -1) {
        setDialogMessage(flash_deviceNotFound, "error");
        return;
    }
    
    if (net_found) {
        if (endpointObj.device != null) {
            
            if (checkAcl && !deviceAllowCreate(endpointObj.device)) {
                setDialogMessage(flash_deviceCannotBeSource, "error");
                return;
            }
                
            $("#" + point + "_device").val(endpointObj.device);
            map_changeDevice(point);

            if (checkAcl && (endpointObj.port != null) && (endpointObj.port != -1) && !portAllowCreate(endpointObj.port)) {
                setDialogMessage(flash_portCannotBeSource, "error");
                return;
            }
                    
            if (endpointObj.port != null) {
                $("#" + point + "_port").val(endpointObj.port);
                map_changePort(point);
                    
                if (endpointObj.port == -1) {
                    setDialogMessage(flash_portNotFound, "error");
                    return;
                }
            }
            
            if ($("#" + point + "_port").val() != -1)
                $("#edp_dialog_form").dialog("close");
            else
                setDialogMessage(flash_portNotSet, "warning");
        } else
            setDialogMessage(flash_deviceNotSet, "warning");
    } else
        setDialogMessage(flash_pointNotSet, "warning");
}

function deviceAllowCreate(device) {
    var devices = map_getDevices($("#src_domain").html(), $("#src_network").html());
    var allow_create = false;
    for (var d in devices) {
        if (devices[d].id == device) {
            allow_create = devices[d].allow_create;
            break;
        }
    }
    return allow_create;
}

function portAllowCreate(port) {
    var ports = map_getPorts($("#src_domain").html(), $("#src_network").html(), $("#src_device").val());
    var allow_create = false;
    for (var p in ports) {
        if (ports[p].port_number == port) {
            allow_create = ports[p].allow_create;
            break;
        }
    }
    return allow_create;
}

function setDialogMessage(message, type) {
    $("#dialog_msg").removeClass();
    $("#dialog_msg").addClass(type);
    $("#dialog_msg").html(message);
}

function selectThisHost(point) {
    clearFlash();
    $.fn.mapEdit.clearPoint(point);
    
    $.ajax ({
        type: "POST",
        url: baseUrl+'circuits/reservations/selectThisHost',
        dataType: "json",
        point: point,
        success: function(data) {
            if (data) {
                fillPoint(this.point, data);
            } else
                setFlash(flash_couldNotGetHost, "error");
        },
        error: function(jqXHR) {
            if (jqXHR.status == 406)
                location.href = baseUrl+'init/gui';
        }
    });
}

function chooseHost(point) {
    $("#dialog_msg").empty();
    $.fn.mapEdit.clearPoint(point);
    
    $.ajax ({
        type: "POST",
        url: baseUrl+'circuits/reservations/chooseHost',
        dataType: "json",
        data: {
            edp_reference: $("#edp_reference").val()
        },
        point: point,
        success: function(data) {
            if (data) {
                fillPoint(this.point, data);
            } else {
                setDialogMessage(flash_couldNotGetHost, "error");
            }
        },
        error: function(jqXHR) {
            if (jqXHR.status == 406)
                location.href = baseUrl+'init/gui';
        }
    });
}

function copyEndpointLink(point) {
    var urn = null;
    var partial_urn = null;
    if (point == "src") {
        urn = src_urn;
        partial_urn = src_partial_urn;
    } else {
        urn = dst_urn;
        partial_urn = dst_partial_urn;
    }
    
    var searchDisabled = $('#' + point + '_copyedp').attr('class').search("disabled");
    
    if (searchDisabled == -1) {
        if (urn != null)
            $("#edp_link").val(urn);
        else if (partial_urn != null)
            $("#edp_link").val(partial_urn);
        else
            $("#edp_link").val("unknown");
    
        $("#copy_edp_dialog").dialog("open");
        $("#edp_link").trigger('click');
    }
}


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

function setDomainPartialURN(where, topology_id) {
    if (topology_id) {
        if (where == "src") {
            src_partial_urn = "urn:ogf:network:domain=" + topology_id;
        } else if (where == "dst") {
            dst_partial_urn = "urn:ogf:network:domain=" + topology_id;
        }
        $("#" + where + "_copyedp").disabled(false);
    } else
        $("#" + where + "_copyedp").disabled();
}

function appendNode(where, node_id) {
    var urn = null;
    
    if (where == "src")
        urn = src_partial_urn;
    else if (where == "dst")
        urn = dst_partial_urn;
    
    if (urn != null) {
        var searchNode = urn.search(":node");
        if (searchNode != -1)
            urn = urn.substring(0, searchNode);
        
        if (node_id)
            urn += ":node=" + node_id;
        
        $("#" + where + "_copyedp").disabled(false);
        
        if (where == "src")
            src_partial_urn = urn;
        else
            dst_partial_urn = urn;
    } else
        $("#" + where + "_copyedp").disabled();
}

function setDevicePartialURN(where, devices, device_id) {
    if (devices) {
        for (var i in devices) {
            if (devices[i].id == device_id) {
                appendNode(where, devices[i].topology_node_id);
                break;
            }
        }
    }
}

function map_changeNetwork(where, network_id, domain_id) {
    var network = "#" + where + "_network";
    var device_id = "#" + where + "_device";
    var port_id = "#" + where + "_port";

    $(device_id).disabled(false);
    //$("#" + where + "_vlanTagged").disabled(false);

    map_clearVlanConf(where);
    $(device_id).clearSelectBox();

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
            $(device_id).fillSelectBox(devices, -1, true); // -1 indicates that this function uses a fourth parameter | True indicates that is necessary to check for permissions before filling the box
        }
        else {                                           //if desired checkpoint is a destination endpoint, then there's no need for permission'
            $(device_id).fillSelectBox(devices);
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
    $(port_id).clearSelectBox();
    
    if ($(device_id).val() != -1) {
        var ports = map_getPorts($(domain_id).html(), $(network_id).html(), $(device_id).val());
        if (where == 'src')
            map_fillPorts(port_id, ports, -1, true);
        else
            map_fillPorts(port_id, ports);

        var devices = map_getDevices($(domain_id).html(), $(network_id).html());
        setDevicePartialURN(where, devices, $(device_id).val());

        if (ports.length == 1) {
            map_setEndpointConf(where);
        }
        
        $(port_id).slideDown();
    } else
        appendNode(where, null);
}

function map_changePort(where) {
    var port_id = "#" + where + "_port";
    map_clearVlanConf(where);
    if ($(port_id).val() != -1) {
        map_setEndpointConf(where);
    }
}

function map_getPorts(domain_id, network_id, device_id) {
    var devices = map_getDevices(domain_id, network_id);
    var ports = null;
    if (devices) {
        for (var i=0; i<devices.length; i++) {
            if (devices[i].id == device_id) {
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
    $(text_htmlId).attr('title', '').val("").disabled().attr('placeholder',"").next().html("").disabled();

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
    $(htmlId).clearSelectBox();
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
            $(text_htmlId).attr('placeholder', any_string);
        } else {
            // possui virgula, mas nao possui range. Ex: "3000,3001,3002" ou "0,3000,3001,3002"
            if (temp[0] != 0) {
                allowUntag = false;
                vlan_validValues = urnData.vlan;
            } else
                vlan_validValues = urnData.vlan.substr(2);
            $(text_htmlId).attr('placeholder', any_string);
        }
    } else {
        if (range != -1) {
            // nao possui virgula, mas possui range. Ex: "3000-3500"
            temp = urnData.vlan.split("-");
            vlan_min = temp[0];
            vlan_max = temp[1];
            allowUntag = false;
            $(text_htmlId).attr('placeholder',any_string);
        }else {
            // nao possui virgula nem range. Ex: "0" ou "3000"
            vlan_validValues = urnData.vlan;
            if (vlan_validValues == 0) {
                allowTag = false;
            } else {
                // um valor só para VLAN
                $(text_htmlId).attr('placeholder', any_string);
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
        src_vlan_min = parseInt(vlan_min);
        src_vlan_max = parseInt(vlan_max);
        src_vlan_validValues = vlan_validValues;
        src_max_cap = urnData.max_capacity;
        src_min_cap = urnData.min_capacity;
        src_div_cap = urnData.granularity;
        $("#src_urn").val(src_urn);
    } else if (where == "dst") {
        dst_urn = urnData.urn_string;
        dst_vlan_min = parseInt(vlan_min);
        dst_vlan_max = parseInt(vlan_max);
        dst_vlan_validValues = vlan_validValues;
        dst_max_cap = urnData.max_capacity;
        dst_min_cap = urnData.min_capacity;
        dst_div_cap = urnData.granularity;
        $("#dst_urn").val(dst_urn);
    }
    
    $("#" + where + "_copyedp").disabled(false);
    
    enableBandwidthSpinner();
}

function enableBandwidthSpinner() {
    if (src_urn && dst_urn) {
        var bmin_tmp = (src_min_cap >= dst_min_cap) ? src_min_cap : dst_min_cap;
        var bmax_tmp = (src_max_cap <= dst_max_cap) ? src_max_cap : dst_max_cap;
        var bdiv_tmp = (src_div_cap == dst_div_cap) ? src_div_cap : band_div;
        
        $('#bandwidth').spinner({
            min: bmin_tmp, 
            max: bmax_tmp, 
            step: bdiv_tmp
        }).spinner("enable").disabled(false).trigger('click');
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

function maySpecifyPath() {
    if ($("#chk_maySpecifyPath").attr('checked')) {
        $("#waypointsConfiguration").slideDown(1);
    //        for (var i in $.fn.mapEdit.waypoints) {
    //            $.fn.mapEdit.waypoints[i].unselectedMarker.setMap(null);
    //            $.fn.mapEdit.waypoints[i].setMap(edit_map);
    //        }        
    } else {
        $("#waypointsConfiguration").slideUp(1);
    //        for (var i in $.fn.mapEdit.waypoints) {
    //            $.fn.mapEdit.waypoints[i].setMap(null);
    //            $.fn.mapEdit.waypoints[i].unselectedMarker.setMap(edit_map);            
    //        }
    }
}

function loadDevices(elem) {
    var devices = new Array();
    var str = "";
    var dev = "";
    
    for (var i in domains) {
        for (var j in domains[i].networks) {
            for (var k in domains[i].networks[j].devices) {
                if ((domains[i].id == $(elem).children("#domain_id").html()) && 
                    (domains[i].networks[j].id == $(elem).children("#network_id").html())) {
                    $("#waypointDomain").html(domains[i].name);
                    $("#waypointNetwork").html(domains[i].networks[j].name);                    
                    for (w in $.fn.mapEdit.waypoints) {
                        if (($.fn.mapEdit.waypoints[w].domain_id == domains[i].id) &&
                            ($.fn.mapEdit.waypoints[w].id == domains[i].networks[j].id)){
                            str = $.fn.mapEdit.waypoints[w].urn.split(":node");
                            if (str.length > 1) {
                                dev = domains[i].networks[j].devices[k].id;
                                break;
                            }
                        }
                    }
                    devices.push(domains[i].networks[j].devices[k]);
                }
            }
        }
    }

    $("#waypointDevice").fillSelectBox(devices);
    if (dev != "") {
        $("#waypointDevice").val(dev);
    }
}

function setPathUrn() {    
    var strPath = "";
    for (var i in $.fn.mapEdit.waypoints) {
        strPath = strPath + $.fn.mapEdit.waypoints[i].urn + ";";
    }
    $("#path_urn").val(strPath);
}

function completeURN() {
    if ($("#waypointDevice").val() != -1) {
        for (var w in $.fn.mapEdit.waypoints) {
            for (var i in domains) {
                for (var j in domains[i].networks) {
                    for (var k in domains[i].networks[j].devices) {
                        if ((domains[i].networks[j].devices[k].id == $("#waypointDevice").val()) &&
                            (domains[i].id == $.fn.mapEdit.waypoints[w].domain_id) &&
                            (domains[i].networks[j].id) == $.fn.mapEdit.waypoints[w].id) {
                            $.fn.mapEdit.waypoints[w].urn = "urn:ogf:network:domain=" + domains[i].topology_id + ":node=" + domains[i].networks[j].devices[k].topology_node_id;
                            break;
                        }
                    }
                }
            }
        }
    } else {
        for (var w in $.fn.mapEdit.waypoints) {
            if (($("#waypointDomain").html() == $.fn.mapEdit.waypoints[w].domain_name) &&
                ($("#waypointNetwork").html() == $.fn.mapEdit.waypoints[w].label)) {
                $.fn.mapEdit.waypoints[w].urn = "urn:ogf:network:domain=" + $.fn.mapEdit.waypoints[w].topology_id;
                break;
            }
        }
    }
    setPathUrn();        
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
        waypointCount: 0, // numero de pontos intermediarios selecionados
        hops : new Array(), //array contendo posicao dos pontos intermediarios
        waypoints: new Array(),
        
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
            $.fn.mapEdit.prepareWaypointContextMenu();
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
                        this.addMapMarker(coord, domains[i].id, domains[i].name, domains[i].topology_id, domains[i].networks[j].id, domains[i].networks[j].name, domains[i].networks[j].allow_create, color);
                        edit_bounds.push(coord);
                    }
                }
            }
            //toggleCluster(true, edit_markersArray);

            google.maps.event.addListener(edit_map, 'click', function() {
                contextMenu.hide(); 
                contextMenuWaypoint.hide(); 
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
                if (specify_path) {
                    contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
                    contextMenu.append('<li><a href="#fromHere" id="contextItem">' + from_here_string + '</a></li>');
                    contextMenu.append('<li><a href="#setWaypoint" id="contextItem">' + waypoint_string + '</a></li>');
                    contextMenu.append('<li><a href="#toHere">' + to_here_string + '</a></li>');
                    contextMenu.bind('contextmenu', function() {
                        return false;
                    });
                    $(edit_map.getDiv()).append(contextMenu);
                } else {
                    contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
                    contextMenu.append('<li><a href="#fromHere" id="contextItem">' + from_here_string + '</a></li>');
                    contextMenu.append('<li><a href="#toHere">' + to_here_string + '</a></li>');
                    contextMenu.bind('contextmenu', function() {
                        return false;
                    });
                    $(edit_map.getDiv()).append(contextMenu);                    
                }
            }
        },

        prepareWaypointContextMenu: function(){
            if (contextMenuWaypoint == null) {
                contextMenuWaypoint = $(document.createElement('ul')).attr('id', 'contextMenu');
                contextMenuWaypoint.append('<li><a href="#removeWaypoint" id="contextItem">' + remove_waypoint_string + '</a></li>');
                contextMenuWaypoint.bind('contextMenuWaypoint', function() {
                    return false;
                });
                $(edit_map.getDiv()).append(contextMenuWaypoint);
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
        addMapMarker: function (coord, domain_id, domain_name, dom_topo_id, network_id, network_name, allow_create, color) {
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
                allow_create: allow_create,
                position: coord,
                styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
                    color:color
                }),
                map:edit_map
            });

            var clickFn = function() {
        
                if (this.allow_create) {
                    $("#contextItem").removeClass("ui-state-disabled");
                }
                else {
                    $("#contextItem").addClass("ui-state-disabled");
                }
                
                contextMenu.find('a').unbind('click');
                contextMenu.find('a[class=ui-state-disabled]').click( function() {
                    //setFlash("Ponto desejado não pode ser origem");
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
                            $.fn.mapEdit.markerClick(coord, domain_id, domain_name, dom_topo_id, network_id, network_name, "src");
                            break;
                        case 'setWaypoint':
                            $.fn.mapEdit.markerClick(coord, domain_id, domain_name, dom_topo_id, network_id, network_name, "way");
                            break;
                        case 'toHere':
                            $.fn.mapEdit.markerClick(coord, domain_id, domain_name, dom_topo_id, network_id, network_name, "dst");
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
        markerClick: function (coord, domain_id, domain_name, dom_topo_id, network_id, network_name, where){
            var color
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
            } else if (where == "way") {                
                
                n = $.fn.mapEdit.waypointCount;
                
                $.fn.mapEdit.waypointCount++;
                
                $.fn.mapEdit.hops.push(coord);
                
                color = "00FF00";                               
                
                var clickFnWay = function() {
                    var order = this.order;
                    contextMenuWaypoint.find('a').unbind('click');
                    
                    contextMenuWaypoint.find('a').click( function() {
                        // fade out the menu
                        contextMenuWaypoint.fadeOut(75);
                        clearFlash();
                    
                
                        // The link's href minus the #
                        switch ( $(this).attr('href').substr(1) )
                        {
                            case 'removeWaypoint':
                                $.fn.mapEdit.clearPoint("way", order);
                                break;
                        }
                        contextMenuWaypoint.hide();
                        contextMenuWaypoint.find('a').unbind('click');
                        return false;
                    });
    
                    var pos = overlay.getProjection().fromLatLngToContainerPixel(coord),
                    x = pos.x,
                    y = pos.y;// save the clicked location

                    // adjust if clicked to close to the edge of the map
                    if (x > mapDiv.width() - contextMenuWaypoint.width())
                        x -= contextMenuWaypoint.width();
        
                    if (y > mapDiv.height() - contextMenuWaypoint.height())
                        y -= contextMenuWaypoint.height();

                    // Set the location and fade in the context menu
                    contextMenuWaypoint.css({
                        top: y,
                        left: x
                    }).fadeIn(100);         
                };
                
        
                $.fn.mapEdit.waypoints[n] = new StyledMarker({
                    domain_id: domain_id,
                    domain_name: domain_name,
                    topology_id: dom_topo_id,
                    id: network_id,
                    label: network_name,
                    position: coord,
                    urn : "urn:ogf:network:domain=" + dom_topo_id,
                    order: ($.fn.mapEdit.waypointCount - 1),
                    styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
                        color:color
                    }),
                    unselectedMarker: null,
                    map: edit_map,
                    map_:edit_map
                });
               
                for (var i in edit_markersArray){//retira marcador não selecionado
                    if ((edit_markersArray[i].id == network_id) && 
                        (edit_markersArray[i].domain_id == domain_id)) {
                        edit_markersArray[i].setMap(null);
                        $.fn.mapEdit.waypoints[n].unselectedMarker = edit_markersArray[i];
                        edit_markersArray.splice(i,1);
                        break;
                    }
                }                
                
                $("#waypoints_order").append("<li class='ui-state-default opener' id='order_"+$.fn.mapEdit.waypoints[n].order+"'>"  + network_name + "<label id='domain_id' hidden>" + domain_id + "</label><label id='network_id' hidden>" + network_id + "</label> </li>");

                $(".opener").click(function() {
                    var content = this;
                    $("#dialog-modal").dialog( {
                        open: function(event, ui) {
                            loadDevices(content);
                        } 
                    });
                    $("#dialog-modal").dialog("open");
                });
                
                $("#chk_maySpecifyPath").removeAttr("disabled");
                $("#advConfLabel").removeAttr("disabled");
                maySpecifyPath();
                
                google.maps.event.addListener($.fn.mapEdit.waypoints[n], "click", clickFnWay);
                google.maps.event.addListener($.fn.mapEdit.waypoints[n], 'rightclick', clickFnWay);    
                
            }   
            
            if (where != "way") {            
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
            }
            
            for (var i in edit_markersArray){//retira marcador não selecionado
                if ((edit_markersArray[i].id == network_id) && 
                    (edit_markersArray[i].domain_id == domain_id)) {
                    edit_markersArray[i].setMap(null);
                    path[n].unselectedMarker = edit_markersArray[i];
                    edit_markersArray.splice(i,1);
                    break;
                }
            }
            
            $("#"+where+"_domain").html(domain_name);
            setDomainPartialURN(where, dom_topo_id);
            $("#"+where+"_network").html(network_name); 
            map_changeNetwork(where, network_id, domain_id); 
            
            
            $.fn.mapEdit.preparePath(path[0], path[1], $.fn.mapEdit.hops);    
            
            setPathUrn();
        },
        
        /** Desenha linha entre dois pontos e prepara seleção de banda
         **/
        preparePath: function(from, to, waypoints) {
            var pathToDraw = new Array();
            
            if (from != null)  {
                pathToDraw.push(from.position);
            }
            
            if (waypoints != null) {
                for (var i in waypoints) {
                    pathToDraw.push(waypoints[i]);
                }
            }
            
            if (to != null) {
                pathToDraw.push(to.position);
            }
            
            if (pathToDraw.length > 1) {
                $.fn.mapEdit.clearMapElements(edit_lines);
                this.drawPath(pathToDraw);
            } else
                return;
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
            ((srcSet) && (dstSet) && (path[0] != null) && (path[1] != null));
        //            (path.length == 2) &&
        //            (path[0] != null) && 
        //            (path[1] != null);
        },
    
        clearPoint: function (point, order) {
            var n = 0;
            if (point == "src") {
                src_partial_urn = null;
                if (!srcSet)
                    return ;
                srcSet = false;
                n=0;
            } else if (point == "dst") {
                dst_partial_urn = null;
                if (!dstSet)
                    return ;
                dstSet = false;
                n=1;
            //assert(path[n] == null && !dstSet)
            } else if (point == "way") {
                if ($.fn.mapEdit.waypointCount == 0)
                    return;                
                n = order; 
                $.fn.mapEdit.waypointCount--;
            }
            
            if (point=="dst" || point == "src") {
                if (path[n].unselectedMarker != null){
                    edit_markersArray.push(path[n].unselectedMarker); //coloca marcador de volta
                    path[n].unselectedMarker.setMap(edit_map);
                }
            
                path[n].setMap(null); //remove ponto do mapa
            
                path[n] = null;
            
                if (!this.hasPath()){
                    $("#bandwidth").spinner('disable');
                    $('#bandwidth_un').disabled();
                } 
                
                $("#"+point+"_domain,#"+point+"_network").empty();
                $("#"+point+"_device,#"+point+"_port").empty().disabled();//,#src_vlanTagged
                $("#" + point + "_copyedp").disabled();
                map_clearVlanConf(point);
                
            } else if (point =="way") {  //caso ponto a ser removido seja intermediario
                
                if ($.fn.mapEdit.waypoints[n].unselectedMarker != null) {
                    edit_markersArray.push($.fn.mapEdit.waypoints[n].unselectedMarker);   //devolve ponto original ao array de marcadores
                    $.fn.mapEdit.waypoints[n].unselectedMarker.setMap(edit_map);          //devolve o marcador original ao mapa
                } 
                
                $.fn.mapEdit.waypoints[n].setMap(null);   // exclui o marcador selecionado do mapa
                $.fn.mapEdit.waypoints.splice(order, 1);  // retira marcador selecionado do array de pontos intermediarios                   
                $.fn.mapEdit.hops.splice(n,1);            // retira posicao do ponto intermediario do array de 'hops' para desenho
                
                $("#waypoints_order").empty();
                
                if ($.fn.mapEdit.waypoints.length == 0) {
                    $("#waypointsConfiguration").slideUp(1);
                    $("#chk_maySpecifyPath").attr("disabled", "disabled");
                    $("#advConfLabel").attr("disabled", "disabled");
                } else {
                    for (var i=0; i< $.fn.mapEdit.waypoints.length; i++) {                    
                        if ($.fn.mapEdit.waypoints[i].order > order)                    
                            $.fn.mapEdit.waypoints[i].order --;
                        $("#waypoints_order").append("<li class='ui-state-default opener' id='order_"+$.fn.mapEdit.waypoints[i].order+"'>" + $.fn.mapEdit.waypoints[i].label +"<label id='domain_id' hidden>" + $.fn.mapEdit.waypoints[i].domain_id + "</label><label id='network_id' hidden>" + $.fn.mapEdit.waypoints[i].id + "</li>");    
                    }
                    $(".opener").click(function() {
                        var content = this;
                        $("#dialog-modal").dialog( {
                            open: function(event, ui) {
                                loadDevices(content);
                            } 
                        });
                        $("#dialog-modal").dialog("open");
                    });
                
                }
                
            }
            
            edit_lines = this.clearMapElements(edit_lines);                   //remove as linhas do trajeto antigo
            $.fn.mapEdit.preparePath(path[0], path[1], $.fn.mapEdit.hops);    //desenha novamente o caminho, com o novo trajeto            
            edit_setBounds();   //centraliza o mapa
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
            //$.fn.mapEdit.clearMapElements(waypointsMarkers);
            edit_setBounds(edit_bounds);    
    
            
            $.fn.mapEdit.prepareContextMenu();
            $.fn.mapEdit.prepareWaypointContextMenu();
            
            view_clearAll();    
    
            edit_initializeMap();
        }
    
    };
    
    /* resize da janela muda tamanho do mapa */
    var resizefn = function() {
        if ($('#edit_map_canvas'))
            $('#edit_map_canvas').css('width', $('#subtab-points').offset().left-12-$('#tabs-2').offset().left );
    //google.maps.event.trigger(view_map, 'resize');
    };
    
    $.fn.dlg = function(options) {
        return this.each(function() {
            $(this).dialog(options);
            $(this).keyup(function(e) {
                if (e.keyCode == 13) {
                    $('.ui-dialog').find('button:first').trigger('click');
                }
            });
        });
    }
    
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
    

        $("#bandwidth").spinner('disable');
        $('#bandwidth_un').disabled();
        $("#src_domain,#src_network,#dst_domain,#dst_network").empty();
        $("#src_device,#src_port,#dst_device,#dst_port").empty().disabled();

        /*
         * Initialization (bind) of endpoint icons 
         */
        var points = ["src","dst"];
        for (var i in points) {
            var point = points[i];
            $('#' + point + '_clearpath').attr('prefix', point).click(function() {
                $.fn.mapEdit.clearPoint($(this).attr('prefix'));
            });
	
            $('#' + point + '_thishost').attr('prefix', point).click(function() {
                selectThisHost($(this).attr('prefix'));
            });
	
            $('#' + point + '_choosehost').attr('prefix', point).click(function() {
                $("#edp_dialog").val($(this).attr('prefix'));
                clearFlash();
                $("#edp_dialog_form").dialog("open");
            });
	
            $('#' + point + '_copyedp').attr('prefix', point).click(function() {
                copyEndpointLink($(this).attr('prefix'));
            });
        }

        
        $("#edp_dialog_form").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            width: "auto",
            beforeClose: function() {
                $("#edp_reference").val("");
                $("#dialog_msg").empty();
            },
            buttons: [
            {
                text: ok_string,
                click: function() {
                    chooseHost($('#edp_dialog').val());
                }
            },
            ]
        });
        
        $("#edp_reference").autocomplete({
            source: hosts
        });
        
        //        $("#edp_reference").keyup(function(event) {
        //            if (event.which == 13) {
        //                chooseHost($('#edp_dialog').val());
        //                $("#edp_dialog_form").dialog("close");
        //            }
        //        });

        $("#copy_edp_dialog").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            width: "auto",
            height: "110",
            beforeClose: function() {
                $("#edp_link").val("");
            }
        });
        
        $("#edp_link").bind('click', function() {
            this.select();
        });
        
        map_clearVlanConf();
        
        $('#repeat_chkbox').click(showRecurrenceBox);
        $(".recurrence_table input[type=checkbox]").click(function(){
            checkWeekDay(this.id);
        });
        $('#recur_radio, #date_radio, #nr_occurr').change(setUntilType);
        $(window).resize(resizefn);
        var finishfn = function(){
            $(window).unbind('resize');
            $('#main').unbind('pjax:start', finishfn);
        };
        $('#main').bind('pjax:start', finishfn);
        
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
        
        $("#waypoints_order").sortable({
            update: function(event, ui) {
                var auxOrder = $(this).sortable('toArray');
                var newOrder = new Array();

                $.fn.mapEdit.hops = [];

                for (var i=0; i < auxOrder.length; i++) {
                    var aux = auxOrder[i].split("order_");
                    newOrder.push($.fn.mapEdit.waypoints[aux[1]]);                   
                    newOrder[newOrder.length-1].order = newOrder.length-1;
                    $.fn.mapEdit.hops.push(newOrder[newOrder.length-1].position);
                }
                
                $.fn.mapEdit.waypoints = newOrder;
                
                
                $("#waypoints_order").empty();
                
                for (i=0; i< $.fn.mapEdit.waypoints.length; i++) {                    
                    $("#waypoints_order").append("<li class='ui-state-default opener' id='order_"+$.fn.mapEdit.waypoints[i].order+"'>" + $.fn.mapEdit.waypoints[i].label + "<label id='domain_id' hidden>" + $.fn.mapEdit.waypoints[i].domain_id + "</label><label id='network_id' hidden>" + $.fn.mapEdit.waypoints[i].id + "</li>");    
                }

                if ($.fn.mapEdit.hasPath()) {
                    edit_lines = $.fn.mapEdit.clearMapElements(edit_lines);
                }
                
                $.fn.mapEdit.preparePath(path[0], path[1], $.fn.mapEdit.hops);
                
                $(".opener").click(function() {
                    var content = this;
                    $("#dialog-modal").dialog( {
                        open: function(event, ui) {
                            loadDevices(content);
                        } 
                    });
                    $("#dialog-modal").dialog("open");
                });                
                
                setPathUrn();
            }      
            
        }).css("display","block");
        
        $("#dialog-modal").dialog({
            autoOpen: false,
            resizable: false,
            draggable: false,
            modal: true,
            show: "fade",
            hide: "fade",
            buttons: [
            {
                text:save_string, 
                click: function() {
                    completeURN();
                    $(this).dialog( "close" ); 
                }
            },
            {   
                text:cancel_string, 
                click: function() {
                    $(this).dialog( "close" );
                }
            }
            ]
        });



        resizefn();
        
        google.maps.event.trigger(edit_map, 'resize');
    //edit_map.setZoom( edit_map.getZoom() );
    });
    
    $(window).load(resizefn);
	
})(jQuery);

