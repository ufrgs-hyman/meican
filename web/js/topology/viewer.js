$(document).ready(function() {
	$('#map-canvas').show();
});

var map;
var shift = 0.01;
var markerWindow;
var markers = [];
var domainsList;
var links = [];
var currentMarkerType = 'network';
var devicesLoaded = false;

$('#marker-type-network').on('change', function() {
    setMarkerType("network");
});
    
$('#marker-type-device').on('change', function() {
    setMarkerType("device");
});

function setMarkerType(markerType) {
    closeWindow();
    currentMarkerType = markerType;
    setMarkersVisible(markerType);
    if (markerType == "device") {
        if (!devicesLoaded) {
            loadDeviceMarkers();
            devicesLoaded = true;
        } 
    }
}

function setMarkersVisible(markerType) {
    for(i = 0; i < markers.length; i++){ 
        switch (markerType) {
            case "network" : 
                if (markers[i].type == "network") {
                    markers[i].setVisible(true);
                } else {
                    markers[i].setVisible(false);
                }
                break;
            case "device" :
                if (markers[i].type == "device") {
                    markers[i].setVisible(true);
                } else {
                    markers[i].setVisible(false);
                }
                break;
            default : 
                console.log("error?" + markerType);
            }
    }
    for(i = 0; i < links.length; i++){ 
        switch (markerType) {
            case "network" : 
                if (links[i].type == "network") {
                    links[i].setVisible(true);
                } else {
                    links[i].setVisible(false);
                }
                break;
            case "device" :
                if (links[i].type == "device") {
                    links[i].setVisible(true);
                } else {
                    links[i].setVisible(false);
                }
                break;
            default : 
                console.log("error?" + markerType);
            }
    }
}

////////////// ADICIONA MARCADORES DE DISPOSITIVOS ////////////////

function loadDeviceMarkers() {
    $.ajax({
        url: baseUrl+'/topology/device/get-all',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            var size = response.length;
            for (var i = 0; i < size; i++) {
                addMarker("device", response[i]);
            };
            $.ajax({
                url: baseUrl+'/topology/viewer/get-device-links',
                dataType: 'json',
                method: "GET",
                success: function(response) {
                    var size = response.length;
                    for (var key in response) {
                        addCircuit('device', response[key][0], response[key][1]);
                    }
                }
            });
        }
    });
}

///////////// DESENHAR LINK NO MAPA ///////////////

function drawCircuit(source, destin) {
	strokeColor = "#0000FF"; 
	strokeOpacity = 0.2;
	
	link = new google.maps.Polyline({
        path: [source.position, destin.position],
        strokeColor: strokeColor,
        strokeOpacity: strokeOpacity,
        strokeWeight: 5,
        geodesic: false,
        type: source.type,
    });
	
	/*google.maps.event.addListener(circuit, 'click', function(event) {
		markerWindow = new google.maps.InfoWindow({
			content: '<div class = "MarkerPopUp" style="width: 230px;"><div class = "MarkerContext">' +
				'what i say?</div></div>'
			});
		markerWindow.position = google.maps.geometry.spherical.interpolate(circuit.path[0], circuit.path[1], 0.5);  
		markerWindow.open(map);
    });*/
	
    link.setMap(map);
    links.push(link);
}

//////////// INICIALIZA MAPA /////////////////

function initialize() {
	var myLatlng = new google.maps.LatLng(0,0);
	var mapOptions = {
			zoom: 3,
			minZoom: 2,
			maxZoom: 15,
			center: myLatlng,
			streetViewControl: false,
			panControl: false,
			zoomControl: false,
			mapTypeControl: false,
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	google.maps.event.addListener(map, 'click', function() {
		closeWindow();
	});
	
	var markers = [];

    if (false && $("input[name=marker-type]").val() == "net") {
        $.ajax({
            url: baseUrl+'/topology/device/get-sdps',
            dataType: 'json',
            method: "GET",
            success: function(response) {
                for (var key in response) {
                    addSdp('network', response[key][0], response[key][1]);
                }
            }
        });
    } else {
        $.ajax({
            url: baseUrl+'/topology/network/get-all',
            dataType: 'json',
            method: "GET",
            success: function(response) {
                var size = response.length;
                for (var i = 0; i < size; i++) {
                    addMarker("network", response[i]);
                };
                $.ajax({
                    url: baseUrl+'/topology/viewer/get-network-links',
                    dataType: 'json',
                    method: "GET",
                    success: function(response) {
                        var size = response.length;
                        for (var key in response) {
                            addCircuit('network', response[key][0], response[key][1]);
                        }
                    }
                });
            }
        });
    }

    
}

String.prototype.ucFirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

//////////// ADICIONA MARCADORES NO MAPA /////////////////

function addMarker(type, object) {
    domainName = getDomainName(object.domain_id);
    if (object.name == "") {
        object.name = 'default';
    }

    var network = null;
    if (type == 'device') network = getNetworkMarkerByDomain(object.domain_id);

    var contentString = 'Domain: ' + '<b>' + domainName + '</b><br>' + type.ucFirst() + ': <b>'+object.name+'</b><br><br><br>';

    if (object.latitude != null && object.longitude != null) {
        var myLatlng = new google.maps.LatLng(object.latitude,object.longitude);
    } else if (network) {
        var myLatlng = network.position;
    } else {
        var myLatlng = new google.maps.LatLng(0, 0);
    }

    if (type == "network") {
        var marker = new StyledMarker({
            styleIcon: new StyledIcon(
                StyledIconTypes.MARKER,
                    {
                        color: generateColor(object.domain_id),
                    }
            ),
            position: getValidMarkerPosition(myLatlng),
            info: contentString,
            id: object.id,
            domainId: object.domain_id,
            type: type,
        });
    } else {
        var marker = new google.maps.Marker({
            icon: {
                path: 'M 15 15 L 35 15 L 25 35 z',
                anchor: new google.maps.Point(25, 35),
                fillColor: '#' + generateColor(object.domain_id),
                fillOpacity: 1,
                strokeColor: 'black',
            },
            position: getValidMarkerPosition(myLatlng),
            type: type,
            id: object.id,
            domainId: object.domain_id,
            info: contentString,
        });
    }
    
    var length = markers.push(marker);
    
    addMarkerListeners(length - 1);
    
    marker.setMap(map);
}

function addCircuit(type, srcId, dstId) {
    srcMarker = getMarker(type, srcId);
    dstMarker = getMarker(type, dstId);

    drawCircuit(srcMarker, dstMarker);
}

function getValidMarkerPosition(position) {
	for(i = 0; i < markers.length; i++){
		if ((markers[i].position.lat().toString().substring(0,6) == position.lat().toString().substring(0,6)) && 
				(markers[i].position.lng().toString().substring(0,6) == position.lng().toString().substring(0,6))) {
			return getValidMarkerPosition(new google.maps.LatLng(position.lat(), position.lng() + shift));
		}
	}
	
	return position;
}

function closeWindow() {
	if (markerWindow) {
		markerWindow.close();
	}
}

//////////// LISTENERS DOS MARCADORES /////////////

function addMarkerListeners(index) {
	google.maps.event.addListener(markers[index], 'mouseover', function(key) {
		return function(){
			closeWindow();
			
			markerWindow = new google.maps.InfoWindow({
				content: '<div class = "MarkerPopUp" style="width: 230px;"><div class = "MarkerContext">' +
					markers[key].info + '</div></div>'
				});
			
			markerWindow.open(map, markers[key]);
		}
	}(index));
}

////////// DEFINE ZOOM E LIMITES DO MAPA A PARTIR DE UM CAMINHO ////////

function setMapBounds(path) {
    if (path.length < 2) return;
    polylineBounds = new google.maps.LatLngBounds();
    for (i = 0; i < path.length; i++) {
    	polylineBounds.extend(path[i]);
    }
    map.fitBounds(polylineBounds);
    map.setCenter(polylineBounds.getCenter());
    map.setZoom(map.getZoom() - 1);
}

function getMarker(type, id) {
	for(i = 0; i < markers.length; i++){
		if (markers[i].type == type && markers[i].id == parseInt(id)) {
			return markers[i];
		}
	}
	
	return null;
}

function getNetworkMarkerByDomain(domainId) {
    for(i = 0; i < markers.length; i++){
        if (markers[i].type == "network" && markers[i].domainId == domainId) {
            return markers[i];
        }
    }
    
    return null;
}

function getDomainName(id) {
    if (!domainsList) domainsList = JSON.parse($("#domains-list").text());
    for (var i = 0; i < domainsList.length; i++) {
        if(domainsList[i].id == id)
        return domainsList[i].name;
    };
}

function generateColor(domainId) {
    domainId = fixDomainId(domainId);

    var firstColor = "3a5879";
    if (domainId == 0) {
        return firstColor;
    } else {
        var color = parseInt(firstColor, 16);
        color += (domainId * parseInt("d19510", 16));
        if ((color == "eee") && (color == "eeeeee")) {
            color = "dddddd";
            color = color.toString(16);
        } else if (color > 0xFFFFFF) {
            color = color.toString(16);
            color = color.substring(1, color.length);
            if(color.length > 6) {
                var str = color.split("");
                color = "";
                for (var i=1; i<str.length; i++) {
                    color = color + str[i];
                }
            }
        } else {
            color = color.toString(16);
        }
        return color;
    }
}

function fixDomainId(domainId) {
    while(domainId > 50) {
        return fixDomainId(domainId - 50);
    }
    return domainId;
}

google.maps.event.addDomListener(window, 'load', initialize);