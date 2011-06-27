function toggleTopology(){
    clearAll();
    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
        var aux = parseFloat(dst_lng_network);
        aux += 0.0005;
        dst_lng_network = aux.toString();
    }
    var coordinatesArray=[];

    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    addMarker(coord_src);
    bounds.push(coord_src);

    var waypoint = new google.maps.LatLng(-18,-54);    
    bounds.push(waypoint);

    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    addMarker(coord_dst);
    bounds.push(coord_dst);

    coordinatesArray.push(coord_src);
    coordinatesArray.push(waypoint);
    coordinatesArray.push(coord_dst);

    if (topology) {
        topology = false;
        addMarker(waypoint);
        drawTopology(coordinatesArray);
    } else {
        topology = true;
        drawPath(coordinatesArray);
    }
    setBounds(bounds);
}

function showCircuit(){
    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
        var aux = parseFloat(dst_lng_network);
        aux += 0.0005;
        dst_lng_network = aux.toString();
    }
    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    addMarker(coord_src);
    bounds.push(coord_src);
    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    addMarker(coord_dst);
    bounds.push(coord_dst);
    setBounds(bounds);
    drawPath(coord_src, coord_dst);
}

function addMarker(location) {
  marker = new google.maps.Marker({
    position: location,
    map:map
  });

  markersArray.push(marker);
  marker.setMap(map);
}

function drawPath(coordinatesArray){
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

function clearLines(){
    for (var i = 0; i < lines.length; i++) {
        lines[i].setMap(null);
    }    
    setBounds(bounds);
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
    clearLines();
    clearBounds();
}