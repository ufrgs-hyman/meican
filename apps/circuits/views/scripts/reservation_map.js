function toggleTopology(){
    clearAll();
    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
        var aux = parseFloat(dst_lng_network);
        aux += 0.0005;
        dst_lng_network = aux.toString();
    }
    var coordinatesArray=[];

    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    edit_addMarker(coord_src);
    bounds.push(coord_src);

    var waypoint = new google.maps.LatLng(-18,-54);    
    bounds.push(waypoint);

    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    edit_addMarker(coord_dst);
    bounds.push(coord_dst);

    coordinatesArray.push(coord_src);
    coordinatesArray.push(waypoint);
    coordinatesArray.push(coord_dst);

    if (topology) {
        topology = false;
        edit_addMarker(waypoint);
        drawTopology(coordinatesArray);
    } else {
        topology = true;
        edit_drawPath(coordinatesArray);
    }
    edit_setBounds(bounds);
}

function showCircuit(){
    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
        var aux = parseFloat(dst_lng_network);
        aux += 0.0005;
        dst_lng_network = aux.toString();
    }
    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    edit_addMarker(coord_src);
    bounds.push(coord_src);
    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    edit_addMarker(coord_dst);
    bounds.push(coord_dst);
    edit_setBounds(bounds);
    edit_drawPath(coord_src, coord_dst);
}

function edit_addMarker(location) {
  marker = new google.maps.Marker({
    position: location,
    map:map
  });

  markersArray.push(marker);
  marker.setMap(map);
}

function edit_drawPath(coordinatesArray){
        var origin = coordinatesArray[0];
        var destination = coordinatesArray[(coordinatesArray.length -1)];
        var flightPlanCoordinates = [origin, destination];
        var line = new google.maps.Polyline({
            path: flightPlanCoordinates,
            strokeColor: "#0000FF",
            strokeOpacity: 0.5,
            strokeWeight: 4
        });

        line.setMap(map);
        lines.push(line);
}

function drawTopology(coordinatesArray){
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

        line.setMap(map);
        lines.push(line);
}

function edit_clearAll(){
    for (var i = 0; i < lines.length; i++) {
        lines[i].setMap(null);
    }    
    edit_setBounds(bounds);
}

function clearMarkers(){
    for (var i=0; i<markersArray.length; i++){
        markersArray[i].setMap(null);
    }
}

function clearBounds(){
    for (var i=0; i<bounds.length; i++){
        bounds.pop();
    }
}

function clearAll(){
    clearMarkers();
    edit_clearAll();
    clearBounds();
}