var meicanMap = new MeicanLMap('canvas');

$(document).ready(function() {
    meicanMap.show("rnp", 'dev');
    $("#canvas").css("height", 400);

    drawReservation($("#connection-id").text());
});

function drawReservation(connId, animate) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-ordered-paths',
        dataType: 'json',
        method: "GET",
        data: {
            id: connId,
        },
        success: function(response) {
            if (selectedConnIsApproved) {
                var size = response.length;
                var requiredMarkers = [];

                //a ordem dos marcadores aqui eh importante,
                //pois eh a ordem do circuito
                for (var i = 0; i < size; i++) {
                    if (response[i].device_id != null) {
                        requiredMarkers.push(response[i].device_id);
                    }
                }

                showMarkers(requiredMarkers);

                //console.log(requiredMarkers);

                addSourceMarker(response[0].device_id);
                addDestinMarker(response[size-1].device_id);
                
                for (var i = 1; i < size-1; i++) {
                    if (response[i].device_id != null) {
                        addWayPointMarker(response[i].device_id);
                    }
                }
                
                setMapBoundsMarkersWhenReady(requiredMarkers);
                
                drawCircuitWhenReady(requiredMarkers, animate);
                
            } else {
                var size = response.length;
            
                var requiredMarkers = [];

                //aqui nao importa a ordem dos marcadores, pois nao ha circuito criado
                addSourceMarker(response[0].device_id);
                requiredMarkers.push(response[0].device_id);
                addDestinMarker(response[size-1].device_id);
                requiredMarkers.push(response[size-1].device_id);
                
                for (var i = 1; i < size-1; i++) {
                    if (response[i].device_id != null) {
                        addWayPointMarker(response[i].device_id);
                        requiredMarkers.push(response[i].device_id);
                    }
                }
                
                setMapBoundsMarkersWhenReady(requiredMarkers);
            }
        }
    });
}

function drawCircuitWhenReady(requiredMarkers, animate) {
    if (areMarkersReady(requiredMarkers)) {
        //console.log("drew");
        if (animate) {
            drawCircuitAnimated(requiredMarkers);
        } else {
            drawCircuit(requiredMarkers);
        }
    } else {
        setTimeout(function() {
            drawCircuitWhenReady(requiredMarkers, animate);
        } ,50);
    }
}

function setMapBoundsMarkersWhenReady(requiredMarkers) {
    if (areMarkersReady(requiredMarkers)) {
        //console.log("setbounds");
        var path = [];
        var size = requiredMarkers.length;
        for(var i = 0; i < size; i++){
            path.push(meicanMap.getMarker('dev',requiredMarkers[i]).position);
        }
        setMapBounds(path);
    } else {
        setTimeout(function() {
            setMapBoundsMarkersWhenReady(requiredMarkers);
        } ,50);
    }
}

function addWayPointMarker(devId) {
    marker = meicanMap.getMarker('dev', devId);
    if (marker) return;

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: devId,
        },
        success: function(response) {
            addMarker(response, "#00FF00");
        }
    });
}

function addSourceMarker(devId) {
    marker = meicanMap.getMarker('dev', devId);
    if (marker) return meicanMap.changeDeviceMarkerColor(marker, "0000EE");

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: devId,
        },
        success: function(response) {
            addMarker(response, "#0000EE");
        }
    });
}

function addDestinMarker(devId) {
    marker = meicanMap.getMarker('dev', devId);
    if (marker) return meicanMap.changeDeviceMarkerColor(marker, "FF0000");

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: devId,
        },
        success: function(response) {
            addMarker(response, "#FF0000");
        }
    });
}