var meicanMap = new MeicanLMap('canvas');
var connIsApproved = true;
var path;

$(document).ready(function() {
    meicanMap.show("rnp", 'dev');
    $("#canvas").css("height", 400);

    drawCircuit($("#circuit-id").attr('value'));

    refresher = setInterval(function() {
        updateCircuitStatus();
    }, 1000);
});

function updateCircuitStatus() {
    switch($("#status").attr("data-value")) {
        case 'reservating'   : 
            break;
        case 'scheduled'     : 
            if(moment().isAfter($("#circuit-info").find('.start-time').attr("value"))) {
                activatingCircuit();
            } else {
                $("#status").find(".tts").text(moment().to($("#circuit-info").find('.start-time').attr("value")));
            }
            break;
        case 'activating'    : activeCircuit();
            break;
        case 'active'        : finishCircuit();
            break;
        case 'finished'      : 
            break;
    }
}

function scheduleCircuit() {
    $("#status").find(".info-box-text").text("Time to start");
    $("#status").find(".info-box-number").html('<span class="tts">loading...</span><br><small>10/02/2016 at 20:00</small>');
    $("#status").attr("data-value", 'scheduled');
}

function activatingCircuit() {
    $("#status").find(".ion-clock").removeClass().addClass("ion ion-gear-a");
    $("#status").find(".info-box-text").text("Status");
    $("#status").find(".info-box-number").text("Activating");
    $("#status").attr("data-value", 'activating');
}

function activeCircuit() {
    $("#status").find(".ion-clock").removeClass().addClass("ion ion-arrow-up-a");
    $("#status").find(".info-box-text").text("Status");
    $("#status").find(".info-box-number").text("Active");
    $("#status").attr("data-value", 'active');
}

function inactiveCircuit() {
    $("#status").find(".ion-clock").removeClass().addClass("ion ion-close-circled");
    $("#status").find(".info-box-text").text("Status");
    $("#status").find(".info-box-number").text("Inactive");
}

function finishCircuit() {
    $("#status").find(".ion-clock").removeClass().addClass("ion ion-checkmark-circled");
    $("#status").find(".info-box-text").text("Status");
    $("#status").find(".info-box-number").text("Finished");
}

function drawCircuit(connId, animate) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-ordered-paths',
        dataType: 'json',
        method: "GET",
        data: {
            id: connId,
        },
        success: function(response) {
            if (connIsApproved) {
                var size = response.length;
                path = response;

                //a ordem dos marcadores aqui eh importante,
                //pois eh a ordem do circuito
                //console.log(requiredMarkers);

                addSource(path[0]);
                addDestin(path[size-1]);
                
                for (var i = 1; i < size-1; i++) {
                    addWayPoint(path[i]);
                }
                
                //setMapBoundsMarkersWhenReady(requiredMarkers);
                
                drawCircuitWhenReady(path, animate);
                
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
                
                //setMapBoundsMarkersWhenReady(requiredMarkers);
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
            console.log('sda');
            var path = [];
            for (var i = 0; i < meicanMap.getMarkers().length; i++) {
                path.push(meicanMap.getMarkers()[i].options.id);
            }
            meicanMap.addLink(path);
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

function addWayPoint(pathItem) {
    //marker = meicanMap.getMarker('dev'+ devId);
    //if (marker) return;

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: pathItem.device_id,
        },
        success: function(response) {
            addMarker(response, "#00FF00");
        }
    });
}

function addSource(pathItem) {
    //marker = meicanMap.getMarker('dev'+ devId);
    //if (marker) return meicanMap.changeDeviceMarkerColor(marker, "0000EE");

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: pathItem.device_id,
        },
        success: function(response) {
            addMarker(response, "#0000EE");
        }
    });
}

function addDestin(pathItem) {
    //marker = meicanMap.getMarker('dev'+ devId);
    //if (marker) return meicanMap.changeDeviceMarkerColor(marker, "FF0000");

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: pathItem.device_id,
        },
        success: function(response) {
            addMarker(response, "#FF0000");
        }
    });
}

function addMarker(dev, color) {
    marker = meicanMap.getMarker('dev'+dev.id);
    if (marker) return marker;

    meicanMap.addMarker(
        'dev'+dev.id,
        dev.name,
        'dev',
        dev.dom,
        dev.lat,
        dev.lng,
        color);
    //markerCluster.addMarker(marker);
    
    //addMarkerListeners(marker);
    
    //marker.setMap(meicanMap.getMap());
}

function areMarkersReady(ids) {
    for (var i = 0; i < ids.length; i++) {
        var marker = meicanMap.getMarker('dev'+ids[i].device_id);
        if (marker === null) {
            return false;
        }
    }
    
    return true;
}