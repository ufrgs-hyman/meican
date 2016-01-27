/**
 * MeicanLMap 1.0
 *
 * A DCN topology visualization based on Leaflet Javascript library.
 *
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

function MeicanLMap(canvasDivId) {
    this._canvasDivId = canvasDivId;
    this._map;                       // Google Map
    this._markers = [];              // markers container
    this._links = [];
    this._openedWindows = [];        // opened marker windows
    this._currentMarkerType;         // current marker type visible
    this._currentTileSource;
    this._domainsList;               // domains list reference;
    this._lastShowedMarker; 
};

MeicanLMap.prototype.show = function(mapType, markerType) {
    if($("#map-l").length == 1) {
        $("#map-l").show();
    } else {
        $("#"+this._canvasDivId).append('<div id="map-l" style="width:100%; height:100%;"></div>');
        this.build('map-l');
    }

    var currentMap = this._map;

    setTimeout(function() {
        currentMap.invalidateSize(true);
    }, 200);

    this.setMapType(mapType);
    //this.setTypeVisible(markerType);
}

MeicanLMap.prototype.hide = function() {
    if($("#map-l").length == 1) {
        this._map.remove();
        $("#map-l").remove();
    }
}

MeicanLMap.prototype.addLink = function(srcId, dstId, type) {
    var srcMarker = this.getMarker(srcId);
    var dstMarker = this.getMarker(dstId);

    if(srcMarker && dstMarker) {
        strokeColor = "#0000FF"; 
        strokeOpacity = 0.1;
    
        link = new google.maps.Polyline({
            path: [srcMarker.position, dstMarker.position],
            strokeColor: strokeColor,
            strokeOpacity: strokeOpacity,
            strokeWeight: 5,
            geodesic: false,
            type: type,
        });
    
    /*google.maps.event.addListener(link, 'click', function(event) {
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
    });*/
    
        link.setMap(this.getMap());
        this._links.push(link);
    }    
}

MeicanLMap.prototype.addMarker = function(object, type, color) {
    if (!color) color = this.getDomain(object.domain_id).color;
    if (object.latitude != null && object.longitude != null) {
        var pos = [object.latitude,object.longitude];
    } else {
        var pos = [0, 0];
    }

    var icon = L.divIcon({
        iconSize: [22,22],
        iconAnchor: [11, 22],
        popupAnchor: [0,-24],
        html: '<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg">' + 
        '<g>' +
        '<path stroke="black" fill="' + color + '" d="m1,0.5l20,0l-10,20l-10,-20z"/>' + 
        '</g>' + 
        '</svg>',
        className: 'marker-icon-svg',
    });

    var marker = L.marker(
        pos, 
        {
            id: type+ object.id, 
            icon: icon
        }
    ).addTo(this._map).bindPopup('#');

    this._markers.push(marker);
    //this._clusterer.addMarker(marker);

    var currentMap = this;

    /*google.maps.event.addListener(marker, 'mouseover', function() {
        currentMap.closeWindows();
        currentMap.openWindow(marker);
    });

    google.maps.event.addListener(marker, 'click', function() {
        currentMap.showMarker(marker.id);
        $( "#"+currentMap._divId ).trigger( "markerClick",  marker.id);
    });*/
}

MeicanLMap.prototype.getDomain = function(id) {
    for (var i = 0; i < this._domainsList.length; i++) {
        if (this._domainsList[i].id == id) return this._domainsList[i];
    }
}

MeicanLMap.prototype.setDomains = function(list) {
    this._domainsList = list;
}

MeicanLMap.prototype.changeDeviceMarkerColor = function(marker, color) {
    marker.icon = {
        path: 'M 15 15 L 35 15 L 25 35 z',
        anchor: new google.maps.Point(25, 35),
        fillColor: '#' + color,
        fillOpacity: 1,
        strokeColor: 'black',
    };
} 

MeicanLMap.prototype.getValidMarkerPosition = function(type, position) {
    size = this._markers.length;
    lat = position.lat().toString().substring(0,6);
    lng = position.lng().toString().substring(0,6);

    for(var i = 0; i < size; i++){
        anotherLat = this._markers[i].position.lat().toString().substring(0,6);
        anotherLng = this._markers[i].position.lng().toString().substring(0,6);

        if (this._markers[i].type == type &&
                anotherLat == lat && 
                anotherLng == lng) {
            return this.getValidMarkerPosition(type, new google.maps.LatLng(position.lat(), position.lng() + 0.01));
        }
    }
    
    return position;
}

MeicanLMap.prototype.closeWindows = function() {
    var size = this._openedWindows.length;
    for (var i = 0; i < size; i++) {
        this._openedWindows[i].close();
    }
}

MeicanLMap.prototype.addWindow = function(infoWindow) {
    this._openedWindows.push(infoWindow);
}

MeicanLMap.prototype.openWindow = function(marker, extra) {
    if (extra) {
        extra = '<br>' + extra + '</div></div>';
    } else {
        extra = '</div></div>';
    }

    markerWindow = new google.maps.InfoWindow({
        content: '<div class = "MarkerPopUp" style="width: 230px; line-height: 1.35; overflow: hidden; white-space: nowrap;"><div class = "MarkerContext">' +
            I18N.t('Domain') + ': ' + '<b>' + (marker.domainName ? marker.domainName : this.getDomain(marker.domainId).name) + '</b><br>' + 
            (marker.type == "dev" ? I18N.t('Device') : I18N.t("Network")) + ': <b>'+marker.name+'</b><br>' +
            extra
        });

    this._openedWindows.push(markerWindow);
    
    markerWindow.open(this._map, marker);

    return markerWindow;
}

MeicanLMap.prototype.getMarker = function(id) {
    if(id) {
        var size = this._markers.length;
        for(var i = 0; i < size; i++){
            if ((this._markers[i].id.toString()) == (id.toString())) {
                return this._markers[i];
            }
        }
    }
    
    return null;
}

MeicanLMap.prototype.getCurrentMarkerType = function() {
    return this._currentMarkerType;
}

MeicanLMap.prototype.searchMarkerByNameOrDomain = function (type, name) {
    results = [];
    name = name.toLowerCase();
    var domainId;

    if (!domainsList) domainsList = JSON.parse($("#domains-list").text());
    for (var i = 0; i < domainsList.length; i++) {
        if(domainsList[i].name.toLowerCase().indexOf(name) > -1){
            domainId = domainsList[i].id;
            break;
        }
    };

    var size = this._markers.length;
    for(var i = 0; i < size; i++){
        if (this._markers[i].type == type) {
            if ((this._markers[i].name.toLowerCase()).indexOf(name) > -1) {
                results.push(this._markers[i]);
            } else if (domainId == this._markers[i].domainId) {
                results.push(this._markers[i]);
            }
        }
    }
    
    return results;
}

MeicanLMap.prototype.getMarkerByDomain = function(type, domainId) {
    var size = this._markers.length;
    for(var i = 0; i < size; i++){
        if (this._markers[i].type == type && this._markers[i].domainId == domainId) {
            return this._markers[i];
        }
    }
    
    return null;
}

MeicanLMap.prototype.setTypeVisible = function(type) {
   /* this.closeWindows();
    this._lastShowedMarker = null;
    this._currentMarkerType = type;

    var size = this._markers.length;
    
    for(var i = 0; i < size; i++){ 
        if (this._markers[i].type == type) {
            this._markers[i].setVisible(true);
        } else {
            this._markers[i].setVisible(false);
        }
    }   

    for (var i = 0; i < this._links.length; i++) {
        if (this._links[i].type == type) {
            this._links[i].setVisible(true);
        } else {
            this._links[i].setVisible(false);
        }
    }

    this._clusterer.repaint();*/
}

MeicanLMap.prototype.build = function(mapDiv) {
    this._map = L.map(mapDiv, {
        center: [0, 0],
        zoomControl: false,
        zoom: 3
    });

    new L.Control.Zoom({ position: 'topright' }).addTo(this._map);

    //$(".leaflet-top").css("margin-top","15%");

    $('#' + mapDiv).show();   
}

MeicanLMap.prototype.getMap = function() {
    return this._map;
}

MeicanLMap.prototype.getMarkers = function() {
    return this._markers;
}

MeicanLMap.prototype.removeMarkers = function() {
    var size = this._markers.length;
    for (var i = 0; i < size; i++) {
        this._markers[i].setMap(null);
    }
    this._markers = [];
}

MeicanLMap.prototype.setMapType = function(mapType) {
    switch(mapType) {
        case "osm" : 
            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                subdomains: ['a','b','c']
            }).addTo( this._map );
            break;
        case "mq" : 
            L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.jpg', {
                attribution: '&copy; MapQuest',
                subdomains: ['1','2','3','4']
                }).addTo(this._map);
            break;
        case 'rnp': 
            L.tileLayer('http://viaipe.rnp.br/mapa/{z}/{x}/{y}.png',{
                attribution: 'MEICAN Project | UFRGS | Map data &copy; 2016 <a href="www.rnp.br">RNP</a>',
                maxZoom: 15,
                minZoom: 2
            }).addTo(this._map);
    }
}

MeicanLMap.prototype.showMarker = function(id) {
    var marker = this.getMarker(id);
    if(marker) {
        var bounds = new google.maps.LatLngBounds();

        bounds.extend(marker.getPosition()); 

        this._map.fitBounds(bounds);
        this._map.setZoom(11);

        this.closeWindows();
        this.openWindow(marker);
        this._lastShowedMarker = id;
    }
}

