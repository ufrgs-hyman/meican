var lines = [];
var pathMarkers = [];
var markerCluster = null;
var temporaryMarker = null;
var infowindow = null;
var clusterContent = [];

function markPlaces(){
    //contextMenu.hide();
    for (var i=0; i < domains.length; i++) {        
        for (var j=0; j<domains[i].networks.length; j++) {            
            if (domains[i].networks[j].latitude) {
                for (var k=0; k<i;k++){
                    for (var l=0; l<domains[k].networks.length; l++) {
                        if (domains[k].networks[l].latitude) {
                            if ((domains[i].networks[j].latitude == domains[k].networks[l].latitude) &&
                                (domains[i].networks[j].longitude == domains[k].networks[l].longitude)) {
                                    domains[i].networks[j].longitude -= -0.005;
                            }
                        }
                    }
                }
                var coord = new google.maps.LatLng(domains[i].networks[j].latitude,domains[i].networks[j].longitude);
                addMarker(coord, domains[i].id, domains[i].name, domains[i].networks[j].name, domains[i].networks[j].id);
                bounds.push(coord);
            }
        }
    }
    google.maps.event.addListener(map, 'click', function() {
        //contextMenu.hide();        
    });
    setBounds(bounds);
    //toggleCluster(true, markersArray);
}

function resetZoom() {
    setBounds(bounds);    
}

function setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    map.fitBounds(polylineBounds);
    map.setCenter(polylineBounds.getCenter());
}
//
//function clearLines(){
//    contextMenu.hide();
//    for (var i = 0; i < lines.length; i++) {
//        lines[i].setMap(null);
//    }
//    for (i = 0; i<markersArray.length; i++) {
//        markersArray[i].setClickable(true);
//    }
//    $("#src_domain").empty();
//    $("#dst_domain").empty();
//    $("#src_network").empty();
//    $("#dst_network").empty();
//    $("#position_origin").empty();
//    $("#position_destination").empty();
//    $("#src_device").empty();
//    $("#src_device").slideUp();
//    $("#dst_device").empty();
//    $("#dst_device").slideUp();
//    $("#src_port").empty();
//    $("#src_port").slideUp();
//    $("#dst_port").empty();
//    $("#dst_port").slideUp();
//    map_clearVlanConf('src');
//    map_clearVlanConf('dst');
//    button = "origin";
//    button_origin_pressed = false;
//    button_destination_pressed = false;
//    contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
//    contextMenu.append(
//        '<li><a href="#fromHere">' + from_here_string + '</a></li>'
//    );
//    contextMenu.bind('contextmenu', function() {return false;});
//    $(map.getDiv()).append(contextMenu);    
//    toggleCluster(false,pathMarkers);
//    pathMarkers = [];
//    toggleCluster(true,markersArray);
//    setBounds(bounds);
//}
//
function addMarker(location, domain_id, domain, descricao, id) {
  var color;
  
  if (domain_id == 1) {
      color = "00ff00";
  } else {
      color = "ff0000";
  }

  marker = new StyledMarker({
    id: id,
    domain: domain,
    descricao: descricao,
    position: location,
    styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
        color:color
    }),
    map:map
  });

//  google.maps.event.addListener(marker, "click", function() {
//      markerClick(id, domain_id, domain, descricao, location, color);
//  });
//
//  google.maps.event.addListener(marker, "mouseover", function() {
//
//    selectedMarker = new StyledMarker({
//        id: id,
//        domain: domain,
//        descricao: descricao,
//        position: location,
//        styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
//                    color:color
//                   }),
//        map:map
//    });
//    var devices = "";
//    for (var i=0; i<domains.length; i++){
//        if (domains[i].id == domain_id) {
//            for (var j=0; j<domains[i].networks.length; j++){
//                if (domains[i].networks[j].id == id) {
//                    for (var k=0; k<domains[i].networks[j].devices.length; k++){
//                        devices += domains[i].networks[j].devices[k].name + "<br>";
//                    }
//                }
//            }
//        }
//    }
//    infowindow = new google.maps.InfoWindow({
//       content: "<b>" + domain_string + "</b>: " + domain +
//                "<br><b>" + network_string + "</b>: " + descricao +
//                "<br><b>" + devices_string + "</b>: " + devices,
//       disableAutoPan: true
//    });
//    selectedMarker.setMap(null);
//    infowindow.open(map, selectedMarker);
//  });
//
//  google.maps.event.addListener(marker, "mouseout", function() {
//        infowindow.close(map);
//  });
//
//    // Display and position the menu
//  google.maps.event.addListener(marker, 'rightclick', function() {
//
//        contextMenu.hide();
//        contextMenu.find('a').click( function() {
//            // fade out the menu
//            contextMenu.fadeOut(75);
//
//            // The link's href minus the #
//            var action = $(this).attr('href').substr(1);
//
//            switch ( action )
//            {
//		case 'fromHere':
//                        markerClick(id, domain_id, domain, descricao, location, color);
//			break;
//		case 'toHere':
//                        markerClick(id, domain_id, domain, descricao, location, color);
//			break;
//            }
//            return false;
//        });
//        var projection = overlay.getProjection(),
//        pos = projection.fromLatLngToContainerPixel(location),
//        x = pos.x,
//        y = pos.y;
//        selectedMarker.setMap(null);
//        
//        // save the clicked location
//
//        // adjust if clicked to close to the edge of the map
//        if (x > mapDiv.width() - contextMenu.width())
//            x -= contextMenu.width();
//
//        if (y > mapDiv.height() - contextMenu.height())
//            y -= contextMenu.height();
//
//        // Set the location and fade in the context menu
//        contextMenu.css({
//            top: y,
//            left: x
//        }).fadeIn(100);
//  });

  markersArray.push(marker);
  marker.setMap(map);
}
//
//function markerClick(id, domain_id, domain, descricao, location, color){
//    contextMenu.hide();
//    
//    selectedMarker = new StyledMarker({
//        id: id,
//        domain: domain,
//        descricao: descricao,
//        position: location,
//        clickable: false,
//        styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
//                    color:color
//                   }),
//        map:map
//    });
//
//    for (var i = 0; i < markersArray.length; i++) {
//        if ((markersArray[i].id == id) && (markersArray[i].domain == domain)) {
//            markersArray[i].setClickable(false);
//        }
//    }
//
//    pathMarkers.push(selectedMarker);
//
//    if (button == "origin") {
//        button_origin_pressed = true;
//        button = "destination";
//        contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
//        contextMenu.append(
//            '<li><a href="#toHere">' + to_here_string + '</a></li>'
//        );
//        contextMenu.bind('contextmenu', function() {return false;});
//        $(map.getDiv()).append(contextMenu);
//
//        $("#src_network").html(descricao);
//        $("#src_domain").html(domain);
//        map_changeNetwork('src', id, domain_id);
//        $("#position_origin").html(String(location));
//
//    } else if (button == "destination") {
//        button_destination_pressed = true;
//        button = "origin";
//        contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
//        contextMenu.append(
//            '<li><a href="#fromHere">' + from_here_string + '</a></li>'
//        );
//        contextMenu.bind('contextmenu', function() {return false;});
//        $(map.getDiv()).append(contextMenu);
//
//        $("#dst_domain").html(domain);
//        $("#dst_network").html(descricao);
//        map_changeNetwork('dst', id, domain_id);
//        $("#position_destination").html(String(location));
//    }
//    setBounds(bounds);
//    selectedMarker.setMap(null);
//    if ((button_origin_pressed) && (button_destination_pressed)) {
//        drawPath($("#position_origin").html(), $("#position_destination").html());
//    }
//}
//
//function drawPath(startPoint, endPoint){
//    var lat = [];
//    var lng = [];    
//    if ((startPoint) && (endPoint)) {
//        var str_aux = "";
//        var temp = startPoint.split(",");
//        str_aux = temp[0];
//        lat[0] = str_aux.replace('(', "");
//        str_aux = temp[1];
//        lng[0] = str_aux.replace(')', "");
//
//        temp = endPoint.split(",");
//        str_aux = temp[0];
//        lat[1] = str_aux.replace('(', "");
//        str_aux = temp[1];
//        lng[1] = str_aux.replace(')', "");
//
//        var origin = new google.maps.LatLng(lat[0],lng[0]);
//        var destination = new google.maps.LatLng(lat[1],lng[1]);
//
//        var flightPlanCoordinates = [origin, destination];
//
//        var line = new google.maps.Polyline({
//            path: flightPlanCoordinates,
//            strokeColor: "#0000FF",
//            strokeOpacity: 0.5,
//            strokeWeight: 4
//        });
//        line.setMap(map);
//        lines.push(line);
//        toggleCluster(false,markersArray);
//        toggleCluster(true,pathMarkers);
//        setBounds(flightPlanCoordinates);        
//    } else {
//        setBounds(bounds);
//    }
//}
//
//function invertPath () {
//    contextMenu.hide();
//    var src_domain_id, dst_domain_id;
//    var src_network_id, dst_network_id;
//
//    var temp_src_domain = $("#src_domain").html();
//    var temp_dst_domain = $("#dst_domain").html()
//
//    var temp_src_network = $("#src_network").html();
//    var temp_dst_network = $("#dst_network").html();
//
//    var temp_src_device = $("#src_device").val();
//    var temp_dst_device = $("#dst_device").val();
//
//    var temp_src_port = $("#src_port").val();
//    var temp_dst_port = $("#dst_port").val();
//    
//    var i, j;
//
//    if (temp_dst_network != "") {
//        for (i=0; i<domains.length; i++){
//            if (temp_dst_domain == domains[i].name){
//                src_domain_id = domains[i].id;
//                for (j=0; j<domains[i].networks.length; j++){
//                    if (temp_dst_network == domains[i].networks[j].name) {
//                        src_network_id = domains[i].networks[j].id;
//                    }
//                }
//            }
//        }
//        map_changeNetwork("src", src_network_id, src_domain_id);
//        if (temp_dst_device != -1){
//            $("#src_device").val(temp_dst_device);
//            map_changeDevice("src");
//            if (temp_dst_port != -1) {
//                $("#src_port").val(temp_dst_port);
//            }
//        }
//    }
//    if (temp_src_network != "") {
//        for (i=0; i<domains.length; i++){
//            if (temp_src_domain == domains[i].name){
//                dst_domain_id = domains[i].id;
//                for (j=0; j<domains[i].networks.length; j++){
//                    if (temp_src_network == domains[i].networks[j].name) {
//                        dst_network_id = domains[i].networks[j].id;
//                    }
//                }
//            }
//        }
//        map_changeNetwork("dst", dst_network_id, dst_domain_id);
//        if (temp_src_device != -1){
//            $("#dst_device").val(temp_src_device);
//            map_changeDevice("dst");
//            if (temp_src_port != -1) {
//                $("#dst_port").val(temp_src_port);
//            }
//        }
//    }
//
//    var temp = $("#src_domain").html();
//    $("#src_domain").html($("#dst_domain").html());
//    $("#dst_domain").html(temp);
//
//    temp = $("#src_network").html();
//    $("#src_network").html($("#dst_network").html());
//    $("#dst_network").html(temp);
//
//    temp = $("#position_origin").html();
//    $("#position_origin").html($("#position_destination").html());
//    $("#position_destination").html(temp);
//
//    if (button == "origin") {
//        if (button_destination_pressed) {
//            button_origin_pressed = true;
//            button_destination_pressed = false;
//        }
//        button = "destination";
//        contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
//        contextMenu.append(
//            '<li><a href="#toHere">' + to_here_string + '</a></li>'
//        );
//        contextMenu.bind('contextmenu', function() {return false;});
//        $(map.getDiv()).append(contextMenu);
//    } else if (button == "destination") {
//        if (button_origin_pressed) {
//            button_origin_pressed = true;
//            button_destination_pressed = false;
//        }
//        button = "origin";
//        contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
//        contextMenu.append(
//            '<li><a href="#fromHere">' + from_here_string + '</a></li>'
//        );
//        contextMenu.bind('contextmenu', function() {return false;});
//        $(map.getDiv()).append(contextMenu);
//    }
//}
//
//function enableMapClick(){
//    for (var i=0; i<markersArray.length;i++){
//        marker = new google.maps.Marker;
//        marker = markersArray[i];
//        marker.setClickable(true);
//    }
//    for (i=0; i<pathMarkers.length;i++){
//        marker = new google.maps.Marker;
//        marker = pathMarkers[i];
//        marker.setClickable(true);
//    }
//    if (button == "origin") {
//        button = "destination";
//    } else {
//        button = "origin";
//    }
//}
//
//function disableMapClick(){
//    for (var i=0; i<markersArray.length;i++){
//        marker = new google.maps.Marker;
//        marker = markersArray[i];
//        marker.setClickable(false);
//    }
//    for (i=0; i<pathMarkers.length;i++){
//        marker = new google.maps.Marker;
//        marker = pathMarkers[i];
//        marker.setClickable(false);
//    }
//}
//
//function toggleCluster(toggle, arrayMarkers){
//    if (toggle) {
//        markerCluster = new MarkerClusterer(map,arrayMarkers);
//        google.maps.event.addListener(markerCluster, 'clustermouseover',function(markerCluster) {
//                var stringInfo = "<h4>&nbsp;&nbsp;" + cluster_information_string + "</h4>&nbsp;&nbsp;";
//                stringInfo += " <b>" + networks_string + "</b>: <br>&nbsp;&nbsp;";
//                clusterContent = markerCluster.getMarkers();
//                selectedMarker = new StyledMarker({
//                    id: clusterContent[0].id,
//                    domain: clusterContent[0].domain,
//                    descricao: clusterContent[0].descricao,
//                    position: clusterContent[0].position,
//                    styleIcon: new StyledIcon(StyledIconTypes.MARKER,{
//                        color:clusterContent[0].styleIcon.color
//                    }),
//                    map:map
//                });
//                for (var i=0; i<clusterContent.length;i++){
//                        stringInfo+= " " + clusterContent[i].descricao +"&nbsp;&nbsp;";
//                        stringInfo+= " (" + clusterContent[i].domain +")<br>&nbsp;&nbsp;";
//                }
//
//                selectedMarker.setMap(null);
//                infowindow = new google.maps.InfoWindow({
//                    content: stringInfo,
//                    disableAutoPan: true
//                });
//                infowindow.open(map, selectedMarker);
//        });
//        google.maps.event.addListener(markerCluster, 'clustermouseout',function() {
//                infowindow.close(map);
//        });
//        google.maps.event.addListener(markerCluster, 'clusterclick',function() {
//                if (infowindow) {
//                    infowindow.close(map);
//                }
//        });
//    } else {
//        markerCluster.clearMarkers(arrayMarkers);
//    }
//}
//
//function map_changeNetwork(where, network_id, domain_id) {
//    var network = "#" + where + "_network";
//    var device_id = "#" + where + "_device";
//    var port_id = "#" + where + "_port";
//
//    $(port_id).slideUp();
//    $(device_id).slideUp();
//
//    map_clearVlanConf(where);
//    clearSelectBox(device_id);
//
//    if ($(network).html() != "") {
//        var devices = [];
//        for (var i=0; i<domains.length; i++){
//            for (var j=0; j<domains[i].networks.length; j++){
//                for (var k=0; k<domains[i].networks[j].devices.length; k++){
//                    if ((domains[i].id == domain_id) && (domains[i].networks[j].id == network_id)) {
//                        devices.push(domains[i].networks[j].devices[k]);
//                    }
//                }
//            }
//        }
//        fillSelectBox(device_id, devices);
//        $(device_id).slideDown();
//    }
//}
//
//function map_changeDevice(where) {
//    var domain_id = "#" + where + "_domain";
//    var network_id = "#" + where + "_network";
//    var device_id    = "#" + where + "_device";
//    var port_id = "#" + where + "_port";
//
//    $(port_id).slideUp();
//    map_clearVlanConf(where);
//    clearSelectBox(port_id);
//    
//    if ($(device_id).val() != -1) {
//        var ports = map_getPorts($(domain_id).html() ,$(network_id).html(), $(device_id).val(), where);
//        map_fillPorts(port_id, ports);
//
//        if (ports.length == 1)
//            map_setEndpointConf(where);
//
//        $(port_id).slideDown();
//    }
//}
//
//function map_getPorts(domain_id, network_id, device_id) {
//    var devices = map_getDevices(domain_id, network_id);
//    var ports = null;
//    if (devices) {
//        for (var i=0; i<devices.length; i++) {
//            if (devices[i].id == device_id) {
//                ports = devices[i].ports;
//                break;
//            }
//        }
//    }
//    return ports;
//}
//
//function map_getDevices(domain_id, network_id) {
//    var devices = null;
//    for (var i=0; i<domains.length; i++) {
//        if (domains[i].name == domain_id){
//            for (var j=0; j<domains[i].networks.length; j++) {
//                if (domains[i].networks[j].name == network_id) {
//                    devices = domains[i].networks[j].devices;
//                    break;
//                }
//            }
//        }
//    }
//    return devices;
//}
//
//function map_changePort(where) {
//    var port_id = "#" + where + "_port";
//    map_clearVlanConf(where);
//    if ($(port_id).val() != -1) {
//        map_setEndpointConf(where);
//    }
//}
//
//function map_clearVlanConf(where) {
//    var untagged_htmlId = "#" + where + "_vlanUntagged";
//    var tagged_htmlId = "#" + where + "_vlanTagged";
//    var text_htmlId = "#" + where + "_vlanText";
//    var tip_htmlId = "#" + where + "_vlanTip";
//
//    $(tip_htmlId).html("");
//    $(text_htmlId).val("");
//    $(text_htmlId).attr('disabled','disabled');
//
//    $(untagged_htmlId).removeAttr('checked');
//    $(untagged_htmlId).attr('disabled','disabled');
//
//    $(tagged_htmlId).removeAttr('checked');
//    $(tagged_htmlId).attr('disabled','disabled');
//
//    if (where == "src") {
//        src_urn = null;
//        src_vlan_min = null;
//        src_vlan_max = null;
//        src_vlan_validValues = null;
//    } else if (where == "dst") {
//        dst_urn = null;
//        dst_vlan_min = null;
//        dst_vlan_max = null;
//        dst_vlan_validValues = null;
//    }
//}
//
//function map_fillPorts(htmlId, portsArray, current_port) {
//    clearSelectBox(htmlId);
//    for (var i=0; i < portsArray.length; i++) {
//        if ((portsArray[i].port_number == current_port) || (portsArray.length == 1))
//            $(htmlId).append('<option selected="true" value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
//        else
//            $(htmlId).append('<option value="' + portsArray[i].port_number + '">' + portsArray[i].port_number + '</option>');
//    }
//}
//
//function map_setEndpointConf(where) {
//
//    var untagged_htmlId = "#" + where + "_vlanUntagged";
//    var tagged_htmlId = "#" + where + "_vlanTagged";
//    var text_htmlId = "#" + where + "_vlanText";
//    var tip_htmlId = "#" + where + "_vlanTip";
//
//    var urnData = map_getUrnData(where);
//
//    var temp = new Array();
//    var virgula = urnData.vlan.search(",");
//    var range = urnData.vlan.search("-");
//
//    var allowTag = true;
//    var allowUntag = true;
//
//    var vlan_min = null;
//    var vlan_max = null;
//    var vlan_validValues = null;
//
//    if (virgula != -1) {
//        temp = urnData.vlan.split(",");
//        if (range != -1) {
//            // possui virgula e range. Ex: "0,3000-3500"
//            if (temp[0] != 0)
//                allowUntag = false;
//            temp = temp[1].split("-");
//            vlan_min = temp[0];
//            vlan_max = temp[1];
//        } else {
//            // possui virgula, mas nao possui range. Ex: "3000,3001,3002" ou "0,3000,3001,3002"
//            if (temp[0] != 0) {
//                allowUntag = false;
//                vlan_validValues = urnData.vlan;
//            } else
//                vlan_validValues = urnData.vlan.substr(2);
//        }
//    } else {
//        if (range != -1) {
//            // nao possui virgula, mas possui range. Ex: "3000-3500"
//            temp = urnData.vlan.split("-");
//            vlan_min = temp[0];
//            vlan_max = temp[1];
//            allowUntag = false;
//        } else {
//            // nao possui virgula nem range. Ex: "0" ou "3000"
//            vlan_validValues = urnData.vlan;
//            if (vlan_validValues == 0) {
//                allowTag = false;
//            } else {
//                // um valor só para VLAN
//                $(text_htmlId).val(vlan_validValues);
//                allowUntag = false;
//            }
//        }
//    }
//
//    if (allowTag) {
//        // pode ser tagged
//        $(tagged_htmlId).removeAttr('disabled');
//
//        if (vlan_min && vlan_max)
//            $(tip_htmlId).html('Value: ' + vlan_min + ' - ' + vlan_max);
//        else if (vlan_validValues) {
//            $(tip_htmlId).html('Value: ' + vlan_validValues);
//        }
//
//        if (allowUntag) {
//            // pode ser untagged também
//            $(untagged_htmlId).removeAttr('disabled');
//            $(untagged_htmlId).attr('checked','yes');
//        } else {
//            $(tagged_htmlId).attr('checked','yes');
//            $(text_htmlId).removeAttr('disabled');
//        }
//    } else {
//        // não pode ser tagged, significa que só pode ser untagged
//        $(untagged_htmlId).removeAttr('disabled');
//        $(untagged_htmlId).attr('checked','yes');
//    }
//
//    if (where == "src") {
//        src_urn = urnData.urn_string;
//        src_vlan_min = vlan_min;
//        src_vlan_max = vlan_max;
//        src_vlan_validValues = vlan_validValues;
//    } else if (where == "dst") {
//        dst_urn = urnData.urn_string;
//        dst_vlan_min = vlan_min;
//        dst_vlan_max = vlan_max;
//        dst_vlan_validValues = vlan_validValues;
//    }
//}
//
//function map_getUrnData(where) {
//    var domain_id = "#" + where + "_domain";
//    var network_id = "#" + where + "_network";
//    var device_id = "#" + where + "_device";
//    var port_id = "#" + where + "_port";
//
//    var ports = map_getPorts($(domain_id).html(), $(network_id).html(), $(device_id).val());
//    var urnData = null;
//
//    for (var i=0; ports.length; i++) {
//        if (ports[i].port_number == $(port_id).val()) {
//            urnData = ports[i];
//            break;
//        }
//    }
//    return urnData;
//}
//
//function map_changeVlanType(elem, where) {
//    var text_htmlId = "#" + where + "_vlanText";
//
//    if (elem.value == "FALSE")
//        $(text_htmlId).attr('disabled','disabled');
//    else if (elem.value == "TRUE")
//        $(text_htmlId).removeAttr('disabled');
//}
//
//function map_saveFlow(flow_id) {
//    var flow_Array = new Array();
//
//    var action = "";
//    if (flow_id) {
//        action = "update";
//        flow_Array[0] = flow_id; // id do flow quando está editando
//    } else {
//        action = "add";
//        flow_Array[0] = 0;
//    }
//
//    flow_Array[1] = $('#name').val(); // name
//
//    if (!flow_Array[1]) {
//        setFlash(flash_nameReq, "warning");
//        return;
//    }
//
//    flow_Array[2] = validateBand($('#bandwidth').val()); // bandwidth
//    if (!flow_Array[2]) {
//        setFlash(flash_bandInv, "warning");
//        return;
//    }
//
//    if (src_urn) {
//        var src_domain_id;
//        for (var i=0; i<domains.length; i++){
//            if (domains[i].name == $("#src_domain").html()){
//                src_domain_id = domains[i].id;
//            }
//        }
//
//        flow_Array[3] = src_domain_id;// source domainId
//        flow_Array[4] = src_urn; // source URN
//    } else {
//        setFlash(flash_sourceReq, "warning");
//        return;
//    }
//
//    if ($("#src_vlanUntagged").attr("checked"))
//        flow_Array[5] = 0;
//    else if ($("#src_vlanTagged").attr("checked")) {
//        if (checkVLAN("src"))
//            flow_Array[5] = $('#src_vlanText').val(); // source VLAN
//        else {
//            setFlash(flash_srcVlanInv, "warning");
//            return;
//        }
//    } else {
//        setFlash(flash_srcVlanReq, "warning");
//        return;
//    }
//
//    if (dst_urn) {
//        var dst_domain_id;
//        for (i=0; i<domains.length; i++){
//            if (domains[i].name == $("#dst_domain").html()){
//                dst_domain_id = domains[i].id;
//            }
//        }
//        flow_Array[6] = dst_domain_id; // destination domainId
//        flow_Array[7] = dst_urn; // destination URN
//    } else {
//        setFlash(flash_destReq, "warning");
//        return;
//    }
//
//    if ($("#dst_vlanUntagged").attr("checked"))
//        flow_Array[8] = 0;
//    else if ($("#dst_vlanTagged").attr("checked")) {
//        if (checkVLAN("dst"))
//            flow_Array[8] = $('#dst_vlanText').val(); // destination VLAN
//        else {
//            setFlash(flash_dstVlanInv, "warning");
//            return;
//        }
//    } else {
//        setFlash(flash_dstVlanReq, "warning");
//        return;
//    }
//
//    $.post("main.php?app=circuits&controller=flows&action="+action, {
//        flowData: flow_Array
//    }, function(data) {
//        loadHtml(data);
//    });
//}
//
//function validateBand(band_value) {
//    var band = band_value.replace(/ /g, "");
//    if (band >= band_min && band <= band_max) {
//        if (band % band_div == 0) {
//            return band;
//        } else
//            return false;
//    } else
//        return false;
//}
