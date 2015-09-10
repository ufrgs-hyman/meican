$(document).ready(function() {
	$('#map-canvas').show();
	
	prepareRefreshButton();
	prepareCancelDialog();
	
	selectConn($("#connections-grid tbody").children().attr("data-key"));
    loadEndPointDetails(selectedConn);
	selectedConnIsApproved = isAuthorizationReceived();
});

$(document).on('ready pjax:success', function() {
	selectConn(selectedConn);
	
	$('#connections-grid').on("click", '.cancel-button', function() {
		
        if ($(this).attr("disabled") != 'disabled') {
            disableAutoRefresh();
            $("#cancel-dialog").data('connId', $(this).parent().parent().parent().attr('data-key')).dialog("open");
        }
            
        return false;
	});
	
	$('#connections-grid tbody tr').on("click", function() {
        if(selectedConn != $(this).attr("data-key")) {
            selectConn($(this).attr("data-key"));
            selectedConnIsApproved = isAuthorizationReceived();
            showCircuit(selectedConn);
        }
	});
	
	if (!selectedConnIsApproved && isAuthorizationReceived()) {
		selectedConnIsApproved = true;
		console.log("connection approved");
		drawReservation(selectedConn, true);
	}

    for (var i = 0; i < circuits.length; i++) {
        if (circuits[i].connId == selectedConn) {
            circuits[i].setOptions(
                {
                    strokeColor: getSelectedConnCircuitColor()
                });
            break;
        } 
    }
});

var selectedConnIsApproved;
var selectedConn;
var meicanMap;
var refresher;
var circuits = [];

function getSelectedConnCircuitColor() {
    if (isActiveSelectedConn()) {
        return "#1B8B1D"; 
    } else {
        return "#483D8B"; 
    }
} 

function showCircuit(connId) {
	var found = false;
	for (var i = 0; i < circuits.length; i++) {
		console.log(circuits[i].connId, connId)
		if (circuits[i].connId != connId) {
			circuits[i].setVisible(false);
		} else {
			found = true;
			circuits[i].setVisible(true);
			showMarkers(circuits[i].requiredMarkers);
		}
	}

	if (!found) {
		drawReservation(connId);
	}
}

function isAuthorizationReceived() {
	return $("#connections-grid tbody").
			children("tr[data-key=" + selectedConn + "]").find("td.authorized").length > 0;
}

function isActiveSelectedConn() {
    return $("#connections-grid tbody").
            children("tr[data-key=" + selectedConn + "]").find("td.active").length > 0;
}

function selectConn(id) {
	$("#connections-grid tbody").children("tr[data-key=" + selectedConn + "]").removeClass("checked-line");
	selectedConn = id;
	$("#connections-grid tbody").children("tr[data-key=" + selectedConn + "]").addClass("checked-line");
}

function loadEndPointDetails(connId) {
	$.ajax({
		url: baseUrl+'/circuits/connection/get-end-points',
		dataType: 'json',
		method: "GET",
		data: {
			id: connId,
		},
		success: function(response) {
			fillEndPointDetails("src", response["src"]);
			fillEndPointDetails("dst", response["dst"]);
		}
	});
}

function fillEndPointDetails(endPointType, path) {
	if (path.dom.length < 15) {
		$("#" + endPointType + "-dom").text(path.dom);
	} else {
		$("#" + endPointType + "-dom").text(path.dom.substr(0, 13) + "...");
		$("#" + endPointType + "-dom").prop("title", path.dom);
	}
    if (path.net.length < 15) {
		$("#" + endPointType + "-net").text(path.net);
	} else {
		$("#" + endPointType + "-net").text(path.net.substr(0, 13) + "...");
		$("#" + endPointType + "-net").prop("title", path.net);
	}
	if (path.dev.length < 15) {
		$("#" + endPointType + "-dev").text(path.dev);
	} else {
		$("#" + endPointType + "-dev").text(path.dev.substr(0, 13) + "...");
		$("#" + endPointType + "-dev").prop("title", path.dev);
	}
	if (path.port.length < 15) {
		$("#" + endPointType + "-port").text(path.port);
	} else {
		$("#" + endPointType + "-port").text(path.port.substr(0, 13) + "...");
		$("#" + endPointType + "-port").prop("title", path.port);
	}
	$("#" + endPointType + "-urn").text(path.urn);
	if (path.vlan.length < 15) {
		$("#" + endPointType + "-vlan").text(path.vlan);
	} else $("#" + endPointType + "-vlan").text(path.vlan.substr(0, 13) + "...");
}

function prepareRefreshButton() {
	refresher = setInterval(updateGridView, 30000);
	
	$("#refresh-button").click(function(){
		if ($("#refresh-button").val() == "true") {
			disableAutoRefresh();
		} else {
			enableAutoRefresh();
		}
	});
}

function prepareCancelDialog() {
	$("#cancel-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: "200",
        buttons: [{
        	id:"yes-button",
            text: I18N.t("Yes"),
            click: function() {
                var connId = $("#cancel-dialog").data('connId');
            	$("#cancel-dialog").dialog( "close" );
            	
            	$.ajax({
            		url: baseUrl+'/circuits/connection/cancel',
            		dataType: 'json',
            		data: {
            			id: connId,
            		},
            		success: function() {
            		},
            		error: function() {
            			$("#dialog").dialog("open");
						$("#message").html(I18N.t("You are not allowed for cancel connections in this domains."));
						$("#dialog").dialog({
							buttons: [
								{
									text: "Ok",
								    click: function() {
								  	  $(this).dialog( "close" );
								    }
								},
							]
						});
            		}
            	});
            	
            	enableAutoRefresh();
        	}
        },{
            text: I18N.t("No") + " (ESC)",
            click: function() {
                console.log(I18N.t("Yes"))
            	$("#cancel-dialog").dialog( "close" );
            }
        }],
        close: function() {
        	$("#yes-button").attr("disabled", false);
        }
    });
}

function disableAutoRefresh() {
	$("#refresh-button").val('false');
	clearInterval(refresher);
	$("#refresh-button").text(I18N.t("Enable auto refresh"));
}

function enableAutoRefresh() {
	updateGridView();
	$("#refresh-button").val('true');
	refresher = setInterval(updateGridView, 30000);
	$("#refresh-button").text(I18N.t("Disable auto refresh"));
}

function updateGridView() {
	$.pjax.defaults.timeout = false;
	$.pjax.reload({
		container:'#connections-pjax'
	});
}

/////////////// botoes superiores da tabela origem destino /////////

function initEndPointButtons(endPointType) {
    $('#' + endPointType + '-copy-urn').click(function() {
    	openCopyUrnDialog(endPointType);
    });
    
    $("#copy-urn-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: "auto",
        height: "auto",
        buttons: [{
            text: I18N.t("Close"),
            click: function() {
            	$("#copy-urn-dialog").dialog( "close" );
            }
        }],
    });
}

function openCopyUrnDialog(endPointType) {
	$("#copy-urn-field").val($("#"+ endPointType+ "-urn").text());
	$("#copy-urn-dialog").dialog("open");
}

///////////// DESENHAR CIRCUITO NO MAPA ///////////////

function drawCircuit(requiredMarkers) {
    strokeColor = getSelectedConnCircuitColor();
    
	strokeOpacity = 0.775;
	
	var path = [];

	for (var k = 0; k < requiredMarkers.length; k++) {
		path.push(meicanMap.getMarker('dev',requiredMarkers[k]).position);
	}
	
	if (path.length > 1) {
		var circuit = new google.maps.Polyline({
			connId: selectedConn,
			requiredMarkers: requiredMarkers,
	        path: path,
	        strokeColor: strokeColor,
	        strokeOpacity: strokeOpacity,
	        strokeWeight: 5,
	        geodesic: false,
	    });
		circuit.setMap(meicanMap.getMap());

		circuits.push(circuit);
	}
}

function drawCircuitAnimated(requiredMarkers) {
	if (requiredMarkers.length > 2) {
		for (var i = 0; i < requiredMarkers.length - 1; i++) {
			drawPath(requiredMarkers, getPathData(
                meicanMap.getMarker('dev',requiredMarkers[i]), meicanMap.getMarker('dev',requiredMarkers[i + 1])), 1); 
		}
		
	} else {
		drawPath(requiredMarkers, getPathData(
            meicanMap.getMarker('dev',requiredMarkers[0]), meicanMap.getMarker('dev',requiredMarkers[1])), 1); 
	}
}

function getPathData(source,destin){  
    var path = [];

    path[0] = new google.maps.LatLng(source.position.lat(),source.position.lng()); 
    
    for(var i=1;i<=90;i++){  
    	
    	//sem geodesic
    	var projection = meicanMap.getMap().getProjection();
        var pointFrom = projection.fromLatLngToPoint( source.position );
        var pointTo = projection.fromLatLngToPoint( destin.position );
        
        // se cruzar o 180 meridiano
        if( Math.abs( pointTo.x - pointFrom.x ) > 128 ) {
            if( pointTo.x > pointFrom.x )
                pointTo.x -= 256;
            else
                pointTo.x += 256;
        }
        
        var x = pointFrom.x + ( pointTo.x - pointFrom.x ) * i/90;
        var y = pointFrom.y + ( pointTo.y - pointFrom.y ) * i/90;
        var pointBetween = new google.maps.Point( x, y );

        path[i] = projection.fromPointToLatLng( pointBetween );
        
        //com geodesic - precisa da lib geometry
    	//path[i] = google.maps.geometry.spherical.interpolate(source.position, destin.position, i/90);
    } 
    
    return path;
}  

function drawPath(requiredMarkers, path, index, polyline){  
	if (polyline) {
		polyline.setPath([path[0],path[index]]);
	} else {
		polyline = new google.maps.Polyline({  
			 connId: selectedConn,
			 requiredMarkers: requiredMarkers,
             path: [path[0],path[index]],  
             strokeColor: "#483D8B",
 	         strokeOpacity: 0.775,
 	         strokeWeight: 5,
 	         geodesic: false,
        });  
		polyline.setMap(meicanMap.getMap());  
		circuits.push(polyline);
	}
   
	index++;  
    if(index <= 90){  
      setTimeout(function() {
    	  drawPath(requiredMarkers, path, index, polyline);
      },100);  
    }                            
}  

//////////// INICIALIZA MAPA /////////////////

function initialize() {
	meicanMap = new MeicanMap;
    meicanMap.buildMap("map-canvas");
	
	drawReservation(selectedConn);
	
	initEndPointButtons("src");
    initEndPointButtons('dst');
}

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
			addMarker(response, "00FF00");
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
			addMarker(response, "0000EE");
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
			addMarker(response, "FF0000");
		}
	});
}

function showMarkers(connIds) {
    var size = meicanMap.getMarkers().length;
	for (var i = 0; i < size; i++) {
		var found = false;
		for (var k = 0; k < connIds.length; k++) {
			console.log(meicanMap.getMarkers()[i].id, connIds[k]);
			if (meicanMap.getMarkers()[i].id == connIds[k]) {
				meicanMap.getMarkers()[i].setVisible(true);
				found = true;
				break;
			} 
		}

		if (!found) 
			meicanMap.getMarkers()[i].setVisible(false);
	}
}

//////////// ADICIONA MARCADORES NO MAPA /////////////////

function addMarker(dev, color) {
	marker = meicanMap.getMarker('dev', dev.id);
	if (marker) return marker;

    if (dev.name == '') dev.name = "default";
	
	if (dev.lat != null && dev.lng != null) {
		var myLatlng = new google.maps.LatLng(dev.lat,dev.lng);
	} else {
		var myLatlng = new google.maps.LatLng(0, 0);
	}
	
	var marker = meicanMap.DeviceMarker({
		position: meicanMap.getValidMarkerPosition('dev', myLatlng),
        type: 'dev',
		id: dev.id,
        domainName: dev.dom
	}, color);
	
	meicanMap.addMarker(marker);
	
	addMarkerListeners(marker);
	
	marker.setMap(meicanMap.getMap());
}

function areMarkersReady(ids) {
	for (var i = 0; i < ids.length; i++) {
        var marker = meicanMap.getMarker('dev',ids[i]);
		if (marker === null) {
			return false;
		}
	}
	
	return true;
}

//////////// LISTENERS DOS MARCADORES /////////////

function addMarkerListeners(marker) {
	google.maps.event.addListener(marker, 'mouseover', function() {
		meicanMap.closeWindows();
		meicanMap.openWindow(marker);
	});
}

////////// DEFINE ZOOM E LIMITES DO MAPA A PARTIR DE UM CAMINHO ////////

function setMapBounds(path) {
    //console.log(path);
    if (path.length < 2) return;
    polylineBounds = new google.maps.LatLngBounds();
    for (var i = 0; i < path.length; i++) {
    	polylineBounds.extend(path[i]);
    }
    meicanMap.getMap().fitBounds(polylineBounds);
    meicanMap.getMap().setCenter(polylineBounds.getCenter());
    meicanMap.getMap().setZoom(meicanMap.getMap().getZoom() - 1);
}

google.maps.event.addDomListener(window, 'load', initialize);