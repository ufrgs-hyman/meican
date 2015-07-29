$(document).ready(function() {
	$('#map-canvas').show();
});

var map;
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
    MeicanMaps.closeWindows();
    currentMarkerType = markerType;
    MeicanMaps.setMarkerTypeVisible(markers, markerType);
    setLinkTypeVisible(markerType);
    if (markerType == "device") {
        if (!devicesLoaded) {
            loadDeviceMarkers();
            devicesLoaded = true;
        } 
    }
}

function setLinkTypeVisible(markerType) {
    for(i = 0; i < links.length; i++){ 
        if (links[i].type == markerType) {
            links[i].setVisible(true);
        } else {
            links[i].setVisible(false);
        }
    }
}

////////////// ADICIONA MARCADORES DE DISPOSITIVOS ////////////////

function loadDeviceMarkers() {
    $.ajax({
        url: baseUrl+'/topology/device/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude','domain_id'])
        },
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
	var mapOptions = {
			zoom: 3,
			minZoom: 2,
			maxZoom: 15,
			center: new google.maps.LatLng(0,0),
			streetViewControl: false,
			panControl: false,
			zoomControl: false,
			mapTypeControl: false,
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	google.maps.event.addListener(map, 'click', function() {
		MeicanMaps.closeWindows();
	});
	
    $.ajax({
        url: baseUrl+'/topology/network/get-all-parent-location',
        dataType: 'json',
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude','domain_id'])
        },
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
        var marker = MeicanMaps.NetworkMarker({
            position: MeicanMaps.getValidMarkerPosition(markers, type, myLatlng),
            info: contentString,
            id: object.id,
            domainId: object.domain_id,
            type: type,
        });
    } else {
        var marker = MeicanMaps.DeviceMarker({
            position: MeicanMaps.getValidMarkerPosition(markers, type, myLatlng),
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
    srcMarker = MeicanMaps.getMarker(markers, type, srcId);
    dstMarker = MeicanMaps.getMarker(markers, type, dstId);

    drawCircuit(srcMarker, dstMarker);
}

//////////// LISTENERS DOS MARCADORES /////////////

function addMarkerListeners(index) {
	google.maps.event.addListener(markers[index], 'mouseover', function(key) {
		return function(){
			MeicanMaps.closeWindows(open);
			MeicanMaps.openWindow(map, markers[key]);
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

google.maps.event.addDomListener(window, 'load', initialize);