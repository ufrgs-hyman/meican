function toggleTopology(){
    res_clearAll();
    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
        var aux = parseFloat(dst_lng_network);
        aux += 0.0005;
        dst_lng_network = aux.toString();
    }
    var coordinatesArray=[];

    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    res_addMarker(coord_src, "src");
    res_bounds.push(coord_src);

    var waypoint = new google.maps.LatLng(-18,-54);    
    res_bounds.push(waypoint, "way");

    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    res_addMarker(coord_dst, "dst");
    res_bounds.push(coord_dst);

    coordinatesArray.push(coord_src);
    coordinatesArray.push(waypoint);
    coordinatesArray.push(coord_dst);

    if (topology) {
        topology = false;
        res_addMarker(waypoint);
        res_drawTopology(coordinatesArray);
    } else {
        topology = true;
        res_drawPath(coordinatesArray);
    }
    res_setBounds(res_bounds);
}

function res_showCircuit(){
//    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
//        var aux = parseFloat(dst_lng_network);
//        aux += 0.0005;
//        dst_lng_network = aux.toString();
//    }
    var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
    res_addMarker(coord_src, "src");
    res_bounds.push(coord_src);

    var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
    res_addMarker(coord_dst, "dst");
    
    res_bounds.push(coord_dst);
    res_setBounds(res_bounds);
    
    res_drawPath(coord_src, coord_dst);
}

function res_addMarker(location, where) {

    var color;

    if (where == "src") {
        color = "0000EE";
    } else if (where == "dst") {
        color = "FF0000";
    } else if (where == "way") {
        color = "00FF00";
    }
    
    var res_marker = new StyledMarker({
        position: location,
        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
            color:color
        }),
        map:res_map
    });

    res_markersArray.push(res_marker);
    res_marker.setMap(res_map);
}

function res_drawPath(origin, destination){
        //var origin = coordinatesArray[0];
        //var destination = coordinatesArray[(coordinatesArray.length -1)];
        var flightPlanCoordinates = [origin, destination];
        var line = new google.maps.Polyline({
            path: flightPlanCoordinates,
            strokeColor: "#0000FF",
            strokeOpacity: 0.5,
            strokeWeight: 4
        });

        line.setMap(res_map);
        res_lines.push(line);
}

function res_drawTopology(coordinatesArray){
    
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

        line.setMap(res_map);
        res_lines.push(line);
}

function res_clearAll(){
    for (var i = 0; i < res_lines.length; i++) {
        res_lines[i].setMap(null);
    }    
    res_setBounds(res_bounds);
}

function res_clearMarkers(){
    for (var i=0; i<res_markersArray.length; i++){
        res_markersArray[i].setMap(null);
    }
}

function res_clearBounds(){
    for (var i=0; i<res_bounds.length; i++){
        res_bounds.pop();
    }
}

function res_clearAll(){
    res_clearMarkers();
    res_clearAll();
    res_clearBounds();
}

function res_setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    res_map.fitBounds(polylineBounds);
    res_map.setCenter(polylineBounds.getCenter());
}