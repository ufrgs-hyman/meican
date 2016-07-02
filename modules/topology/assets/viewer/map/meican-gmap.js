/**
 * Meican GMap 2.0
 *
 * A DCN topology visualization based on Google Maps library.
 *
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

function MeicanGMap(canvasDivId) {
    this._canvasDivId = canvasDivId;
    this._autoCompleteService;       // Google Autocomplete Service
    this._placesService;             // Google Places Service
    this._map;                       // Google Map
    this._searchSource = [];         // searchbox source
    this._fillSearchSource;          // searchbox fill source function
    this._markers = [];              // markers container
    this._clusterer;
    this._links = [];
    this._openedWindows = [];        // opened marker windows
    this._currentMarkerType;         // current marker type visible
    this._domainsList;               // domains list reference;
    this._lastShowedMarker;    
};

MeicanGMap.prototype.show = function(mapType, markerType) {
    if($("#map-g").length == 1) {
        $("#map-g").show();
    } else {
        $("#"+this._canvasDivId).append('<div id="map-g" style="width:100%; height:100%;"></div>');
        this.build('map-g');
    }

    google.maps.event.trigger(this._map, "resize");

    //this.setTypeVisible(markerType);
}

MeicanGMap.prototype.hide = function() {
    $("#map-g").hide();
}

MeicanGMap.prototype.addLink = function(srcId, dstId, type) {
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
        var srcDomain = MeicanGMap.getDomainName(source.domainId);
        var dstDomain = MeicanGMap.getDomainName(destin.domainId);
        MeicanGMap.closeWindows();
        var infoWin = new google.maps.InfoWindow({
            content: "Link between <b>" + 
                ((source.name == srcDomain) ? srcDomain : srcDomain + " (" + source.name + ")") + '</b> and <b>' + 
                ((destin.name == dstDomain) ? dstDomain : dstDomain + " (" + destin.name + ")")  + "</b>",
            position: event.latLng,
        });
        infoWin.open(MeicanGMap.getMap());
        MeicanGMap.addWindow(infoWin);
    });*/
    
        link.setMap(this.getMap());
        this._links.push(link);
    }    
}

MeicanGMap.prototype.addMarker = function(object, type, color) {
    if (!color) color = this.getDomain(object.domain_id).color;
    if (object.latitude != null && object.longitude != null) {
        var pos = new google.maps.LatLng(object.latitude,object.longitude);
    } else {
        var pos = new google.maps.LatLng(0, 0);
    }
    var marker =  new google.maps.Marker({
        icon: {
            path: 'M 15 15 L 35 15 L 25 35 z',
            anchor: new google.maps.Point(25, 35),
            fillColor: color,
            fillOpacity: 1,
            strokeColor: 'black',            
        },
        position: this.getValidMarkerPosition(type, pos),
        type: type,
        id: type+object.id,
        domainId: object.domain_id,
        name: object.name,
    });

    this._markers.push(marker);
    this._clusterer.addMarker(marker);

    var currentMap = this;

    google.maps.event.addListener(marker, 'mouseover', function() {
        currentMap.closeWindows();
        currentMap.openWindow(marker);
    });

    google.maps.event.addListener(marker, 'click', function() {
        currentMap.showMarker(marker.id);
        $( "#"+currentMap._divId ).trigger( "markerClick",  marker.id);
    });
}

MeicanGMap.prototype.oldMarker = function(options, color) {
    if (!color) color = this.getDomain(options.domainId).color;
    var marker = new StyledMarker({
        styleIcon: new StyledIcon(
            StyledIconTypes.MARKER,
                {
                    color: color,
                }
        )
    });
    marker.setOptions(options);
    return marker;
}

MeicanGMap.prototype.getDomain = function(id) {
    for (var i = 0; i < this._domainsList.length; i++) {
        if (this._domainsList[i].id == id) return this._domainsList[i];
    }
}

MeicanGMap.prototype.setDomains = function(list) {
    this._domainsList = list;
}

MeicanGMap.prototype.changeDeviceMarkerColor = function(marker, color) {
    marker.icon = {
        path: 'M 15 15 L 35 15 L 25 35 z',
        anchor: new google.maps.Point(25, 35),
        fillColor: '#' + color,
        fillOpacity: 1,
        strokeColor: 'black',
    };
} 

MeicanGMap.prototype.getValidMarkerPosition = function(type, position) {
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

MeicanGMap.prototype.closeWindows = function() {
    var size = this._openedWindows.length;
    for (var i = 0; i < size; i++) {
        this._openedWindows[i].close();
    }
}

MeicanGMap.prototype.addWindow = function(infoWindow) {
    this._openedWindows.push(infoWindow);
}

MeicanGMap.prototype.openWindow = function(marker, extra) {
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

MeicanGMap.prototype.getMarker = function(id) {
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

MeicanGMap.prototype.getCurrentMarkerType = function() {
    return this._currentMarkerType;
}

MeicanGMap.prototype.searchMarkerByNameOrDomain = function (type, name) {
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

MeicanGMap.prototype.getMarkerByDomain = function(type, domainId) {
    var size = this._markers.length;
    for(var i = 0; i < size; i++){
        if (this._markers[i].type == type && this._markers[i].domainId == domainId) {
            return this._markers[i];
        }
    }
    
    return null;
}

MeicanGMap.prototype.setTypeVisible = function(type) {
    this.closeWindows();
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

    this._clusterer.repaint();
}

MeicanGMap.prototype.build = function(divId) {
    var mapOptions = {
        zoom: 3,
        minZoom: 2,
        maxZoom: 15,
        center: new google.maps.LatLng(-6.6388011,-49.5877372),
        streetViewControl: false,
        panControl: false,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.DEFAULT,
            position: google.maps.ControlPosition.LEFT_CENTER
        },
        mapTypeControl: false,
    };
    this._map = new google.maps.Map(document.getElementById(divId), mapOptions);
    this._map.set('styles', [
        {
            featureType: 'poi',
            stylers: [
                { visibility: 'off' }
            ]
        }
    ]);

    $('#' + divId).show();   

    var markerClustererOptions = {
            gridSize: 10, 
            maxZoom: 10,
            ignoreHidden: true
        };
    
    this._clusterer = new MarkerClusterer(
            this._map, 
            null, 
            markerClustererOptions
    );    

    var currentMap = this;

    google.maps.event.addListener(this._map, 'click', function() {
        currentMap.closeWindows();
    });

    //VERIFICAR ESTA EXECUTANDO SOZINHO AO TOCAR NO MENU
   /* google.maps.event.addListener(this._map, 'resize', function() {
        currentMap.closeWindows();
        currentMap.showMarker(currentMap._lastShowedMarker);
        console.log("resized");
    });*/
}

MeicanGMap.prototype.getMap = function() {
    return this._map;
}

MeicanGMap.prototype.getMarkers = function() {
    return this._markers;
}

MeicanGMap.prototype.removeMarkers = function() {
    var size = this._markers.length;
    for (var i = 0; i < size; i++) {
        this._markers[i].setMap(null);
    }
    this._markers = [];
}

MeicanGMap.prototype.disableMapLabels = function () {
    this._map.set('styles', [
        {
            featureType: 'poi',
            stylers: [
                { visibility: 'off' }
            ]
        },
        {
            featureType: "all",
            elementType: "labels",
            stylers: [
                { visibility: "off" }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [
              { "color": "#ffffff" }
            ]
        }
    ]);
} 

MeicanGMap.prototype.enableMapLabels = function () {
    this._map.set('styles', [
        {
            featureType: 'poi',
            stylers: [
                { visibility: 'off' }
            ]
        },

    ]);
} 

MeicanGMap.prototype.setMapType = function(divId, selectId) {
            switch(ui.item.value) {
                case "r" : 
                    currentMap._map.setMapTypeId(google.maps.MapTypeId.ROADMAP); 
                    currentMap.enableMapLabels();
                    break;
                case "cr" : 
                    currentMap._map.setMapTypeId(google.maps.MapTypeId.ROADMAP); 
                    currentMap.disableMapLabels();
                    break;
                case "t" : 
                    currentMap._map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
                    currentMap.enableMapLabels(); 
                    break;
                case "h" : 
                    currentMap._map.setMapTypeId(google.maps.MapTypeId.HYBRID); 
                    currentMap.enableMapLabels();
                    break;
                case "s" : 
                    currentMap._map.setMapTypeId(google.maps.MapTypeId.SATELLITE); 
                    currentMap.enableMapLabels();
                    break;
            }
}

MeicanGMap.prototype.showMarker = function(id) {
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

