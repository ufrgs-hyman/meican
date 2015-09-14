$(document).ready(function() {
	$('#map-canvas').show();
});

var meicanMap;
var domainsList;
var links = [];
var devicesLoaded = false;
var notsSize;

function setMarkerType(markerType) {
    meicanMap.closeWindows();
    meicanMap.setMarkerTypeVisible(markerType);
    setLinkTypeVisible(markerType);
    if (markerType == "dev") {
        if (!devicesLoaded) {
            loadDeviceMarkers();
            devicesLoaded = true;
        } 
    }
}

function setLinkTypeVisible(markerType) {
    for(var i = 0; i < links.length; i++){ 
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
                addMarker("dev", response[i]);
            };
            $.ajax({
                url: baseUrl+'/topology/viewer/get-device-links',
                dataType: 'json',
                method: "GET",
                success: function(response) {
                    var size = response.length;
                    for (var key in response) {
                        addCircuit('dev', response[key][0], response[key][1]);
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
	
	google.maps.event.addListener(link, 'click', function(event) {
        var srcDomain = meicanMap.getDomainName(source.domainId);
        var dstDomain = meicanMap.getDomainName(destin.domainId);
        meicanMap.closeWindows();
        var infoWin = new google.maps.InfoWindow({
            content: "Link between <b>" + 
                ((source.name == srcDomain) ? srcDomain : srcDomain + " (" + source.name + ")") + '</b> and <b>' + 
                ((destin.name == dstDomain) ? dstDomain : dstDomain + " (" + destin.name + ")")  + "</b>",
            position: event.latLng,
        });
        infoWin.open(meicanMap.getMap());
        meicanMap.addWindow(infoWin);
    });
	
    link.setMap(meicanMap.getMap());
    links.push(link);
}

//////////// INICIALIZA MAPA /////////////////

function initialize() {
	meicanMap = new MeicanMap;
    meicanMap.buildMap("map-canvas");
    meicanMap.buildSearchBox("search-row", "search-box", 'search-button');
    meicanMap.buildMapTypeBox("map-type-box", 'map-type-select');
    meicanMap.buildMarkerTypeBox("marker-type-box", 'marker-type-select', setMarkerType);
    /*meicanMap.getMap().controls[google.maps.ControlPosition.TOP_LEFT].push(
        document.getElementById("refresh-box"));*/

    /*$("#refresh-box").show();

    $("#refresh-button").on("click", function() {
        meicanMap.removeMarkers();
        var size = links.length;
        for (var i = 0; i < size; i++) {
            links[i].setMap(null);
        }
        links = [];
        devicesLoaded = false;
        loadNetworks();
    });*/

    /*$("#notification_link").on("notify",function(event, length){
        if (notsSize != length) {
            console.log('notify', notsSize,length);
            notsSize = length;
            meicanMap.removeMarkers();
            var size = links.length;
            for (var i = 0; i < size; i++) {
                links[i].setMap(null);
            }
            links = [];
            devicesLoaded = false;
            loadNetworks();
        } 
    });*/

    loadNetworks();
}

function loadNetworks() {
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
                addMarker("net", response[i]);
            };
            $.ajax({
                url: baseUrl+'/topology/viewer/get-network-links',
                dataType: 'json',
                method: "GET",
                success: function(response) {
                    var size = response.length;
                    for (var key in response) {
                        addCircuit('net', response[key][0], response[key][1]);
                    }
                    setMarkerType("dev");
                }
            });
        }
    });
}

//////////// ADICIONA MARCADORES NO MAPA /////////////////

function addMarker(type, object) {
    var network = null;
    if (type == 'dev') network = meicanMap.getMarkerByDomain('net',object.domain_id);

    if (object.latitude != null && object.longitude != null) {
        var myLatlng = new google.maps.LatLng(object.latitude,object.longitude);
    } else if (network) {
        var myLatlng = network.position;
    } else {
        var myLatlng = new google.maps.LatLng(0, 0);
    }

    if (type == "net") {
        var marker = meicanMap.NetworkMarker({
            position: meicanMap.getValidMarkerPosition(type, myLatlng),
            id: object.id,
            domainId: object.domain_id,
            name: object.name,
            type: "net",
        });
    } else {
        var marker = meicanMap.DeviceMarker({
            position: meicanMap.getValidMarkerPosition(type, myLatlng),
            type: "dev",
            id: object.id,
            domainId: object.domain_id,
            name: object.name,
        });
    }
    
    meicanMap.addMarker(marker);
    
    addMarkerListeners(marker);
    
    marker.setMap(meicanMap.getMap());
}

function addCircuit(type, srcId, dstId) {
    srcMarker = meicanMap.getMarker(type, srcId);
    dstMarker = meicanMap.getMarker(type, dstId);

    drawCircuit(srcMarker, dstMarker);
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