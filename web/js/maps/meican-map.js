/**
 * MeicanMap 1.0
 *
 * @copyright (c) 2015, Maurício Quatrin Guerreiro @mqgmaster
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this
 * list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

function MeicanMap() {
    this._autoCompleteService;       // Google Autocomplete Service
    this._placesService;             // Google Places Service
    this._map;                       // Google Map
    this._searchSource = [];         // searchbox source
    this._fillSearchSource;          // searchbox fill source function
    this._markers = [];              // markers container
    this._openedWindows = [];        // opened marker windows
    this._currentMarkerType;         // current marker type visible
};

MeicanMap.prototype.DeviceMarker = function(options, color) {
    if (!color) color = this.generateColor(options.domainId);
    var marker =  new google.maps.Marker({
        icon: {
            path: 'M 15 15 L 35 15 L 25 35 z',
            anchor: new google.maps.Point(25, 35),
            fillColor: '#' + color,
            fillOpacity: 1,
            strokeColor: 'black',
        }
    });
    marker.setOptions(options);
    return marker;
}

MeicanMap.prototype.NetworkMarker = function(options, color) {
    if (!color) color = this.generateColor(options.domainId);
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

MeicanMap.prototype.changeDeviceMarkerColor = function(marker, color) {
    //console.log("troca color");
    marker.icon = {
            path: 'M 15 15 L 35 15 L 25 35 z',
            anchor: new google.maps.Point(25, 35),
            fillColor: '#' + color,
            fillOpacity: 1,
            strokeColor: 'black',
        };
} 

MeicanMap.prototype.generateColor = function(id) {
    id = this.temp_fixId(id);

    var firstColor = "3a5879";
    if (id == 0) {
        return firstColor;
    } else {
        var color = parseInt(firstColor, 16);
        color += (id * parseInt("d19510", 16));
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

MeicanMap.prototype.temp_fixId = function(id) {
    while(id > 50) {
        return this.temp_fixId(id - 50);
    }
    return id;
}

MeicanMap.prototype.getValidMarkerPosition = function(type, position) {
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

MeicanMap.prototype.closeWindows = function() {
    var size = this._openedWindows.length;
    for (var i = 0; i < size; i++) {
        this._openedWindows[i].close();
    }
}

MeicanMap.prototype.addWindow = function(infoWindow) {
    this._openedWindows.push(infoWindow);
}

MeicanMap.prototype.openWindow = function(marker, extra) {
    if (extra) {
        extra = '<br>' + extra + '</div></div>';
    } else {
        extra = '</div></div>';
    }

    markerWindow = new google.maps.InfoWindow({
        content: '<div class = "MarkerPopUp" style="width: 230px; line-height: 1.35; overflow: hidden; white-space: nowrap;"><div class = "MarkerContext">' +
            'Domain: ' + '<b>' + (marker.domainName ? marker.domainName : this.getDomainName(marker.domainId)) + '</b><br>' + 
            (marker.type == "dev" ? 'Device' : "Network") + ': <b>'+marker.name+'</b><br>' +
            extra
        });

    this._openedWindows.push(markerWindow);
    
    markerWindow.open(this._map, marker);

    return markerWindow;
}

MeicanMap.prototype.getMarker = function(type, id) {
    var size = this._markers.length;
    for(var i = 0; i < size; i++){
        if (this._markers[i].type == type && (this._markers[i].id.toString()) == (id.toString())) {
            return this._markers[i];
        }
    }
    
    return null;
}

MeicanMap.prototype.getCurrentMarkerType = function() {
    return this._currentMarkerType;
}

MeicanMap.prototype.searchMarkerByNameOrDomain = function (type, name) {
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

MeicanMap.prototype.getMarkerByDomain = function(type, domainId) {
    var size = this._markers.length;
    for(var i = 0; i < size; i++){
        if (this._markers[i].type == type && this._markers[i].domainId == domainId) {
            return this._markers[i];
        }
    }
    
    return null;
}

MeicanMap.prototype.setMarkerTypeVisible = function(type) {
    var size = this._markers.length;
    this._currentMarkerType = type;
    for(var i = 0; i < size; i++){ 
        if (this._markers[i].type == type) {
            this._markers[i].setVisible(true);
        } else {
            this._markers[i].setVisible(false);
        }
    }
}

MeicanMap.prototype.buildMap = function(divId) {
    var mapOptions = {
        zoom: 3,
        minZoom: 3,
        maxZoom: 15,
        center: new google.maps.LatLng(22.2033311,-96.8483379),
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

    var currentMap = this;

    google.maps.event.addListener(this._map, 'click', function() {
        currentMap.closeWindows();
    });
}

MeicanMap.prototype.getMap = function() {
    return this._map;
}

MeicanMap.prototype.getMarkers = function() {
    return this._markers;
}

MeicanMap.prototype.addMarker = function(marker) {
    this._markers.push(marker);
}

MeicanMap.prototype.removeMarkers = function() {
    var size = this._markers.length;
    for (var i = 0; i < size; i++) {
        this._markers[i].setMap(null);
    }
    this._markers = [];
}

MeicanMap.prototype.getDomainName = function(id) {
    if (!domainsList) domainsList = JSON.parse($("#domains-list").text());
    for (var i = 0; i < domainsList.length; i++) {
        if(domainsList[i].id == id)
        return domainsList[i].name;
    }
}

MeicanMap.prototype.disableMapLabels = function () {
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

MeicanMap.prototype.enableMapLabels = function () {
    this._map.set('styles', [
        {
            featureType: 'poi',
            stylers: [
                { visibility: 'off' }
            ]
        },

    ]);
} 

MeicanMap.prototype.buildMapTypeBox = function(divId, selectId) {
    this._map.controls[google.maps.ControlPosition.TOP_LEFT].push(
        document.getElementById(divId));

    $("#" + divId).show();

    var currentMap = this;

    $( "#" + selectId ).selectmenu({
        select: function( event, ui ) {
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
        },
    });
}

MeicanMap.prototype.buildMarkerTypeBox = function(divId, selectId, setMarkerTypeFunc) {
    this._map.controls[google.maps.ControlPosition.TOP_LEFT].push(
        document.getElementById(divId));

    $("#" + divId).show();

    var currentMap = this;

    $( "#" + selectId ).selectmenu({
        select: function( event, ui ) {
            setMarkerTypeFunc(ui.item.value);
        }
    });
}

MeicanMap.prototype.buildSearchBox = function(divId, inputId, buttonId, openWindowFunction) {
    this._map.controls[google.maps.ControlPosition.TOP_LEFT].push(
        document.getElementById(divId));

    var currentMap = this;

    google.maps.event.addListenerOnce(this._map,'bounds_changed',function(){
        document.getElementById(divId).style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
        document.getElementById(divId).style.webkitBoxShadow = '0 2px 6px rgba(0,0,0,0.3)';
        $("#" + divId).show();
    });

    google.maps.event.addListenerOnce(this._map,'idle',function(){
        $("#" + inputId).focus();
    });

    $("#" + buttonId).on('click', function() {
        $("#" + inputId).autocomplete("search", $("#"+inputId).val());
    });

    $( "#" + inputId ).autocomplete({
    autoFocus: true,
    delay: 200,   
    select: function (event, ui) {
      //console.log(ui.item);

        switch (ui.item.type) {
            case 'place':
                if(!currentMap._placesService) currentMap._placesService = new google.maps.places.PlacesService(currentMap._map);

                var request = {
                    placeId: ui.item.id
                };

                currentMap._placesService.getDetails(request, function(place, status) {
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        if (place.geometry) {
                            var bounds = new google.maps.LatLngBounds();
                            bounds.extend(place.geometry.location); 

                            currentMap._map.fitBounds(bounds);
                            currentMap._map.setZoom(11);
                        }
                    }
                });
                break;
            case 'net':
            case 'dev':
            case 'port':
                var bounds = new google.maps.LatLngBounds();

                bounds.extend(ui.item.marker.getPosition()); 

                currentMap._map.fitBounds(bounds);
                currentMap._map.setZoom(11);
                
                if(openWindowFunction) {
                    openWindowFunction(ui.item.marker);
                } else {
                    currentMap.closeWindows();
                    currentMap.openWindow(ui.item.marker);
                }
        }
    }, 
    source: function (request, response) {
        var term = request.term;
        var previous = this.previous;
        var pendingSources = 2;
        
        if(!term.trim()) return;

        if (term && (term == previous)) {
            response(currentMap._searchSource);
            return;
        }
        currentMap._searchSource = [];
        currentMap._fillSearchSource = response;

        $.ajax({
            type: "GET",
            url: baseUrl + '/topology/viewer/search',
            data: {
                term: term
            },
            dataType: 'json',
            success: function (response) {
                //console.log(response);

                var size = response.length;
                for (var i = 0; i < size; i++) {
                    var marker = null;
                    if (currentMap._currentMarkerType == 'dev') {
                        marker = currentMap.getMarker(currentMap._currentMarkerType, response[i].device_id);
                    } else if(response[i].network_id) {
                        marker = currentMap.getMarker(currentMap._currentMarkerType, response[i].network_id);
                    }

                    if (marker)
                        currentMap._searchSource.push(
                            {
                                label: response[i].name,
                                value: response[i].name,
                                type: 'port',
                                marker: marker
                            }
                        );

                    if(i == 5) break;
                };

                var markers = currentMap.searchMarkerByNameOrDomain(currentMap._currentMarkerType, request.term); 
                var size = markers.length;
                for (var i = 0; i < size; i++) {
                    currentMap._searchSource.push(
                        {
                            label: markers[i].name,
                            value: markers[i].name,
                            type: currentMap._currentMarkerType,
                            marker: markers[i]
                        }
                    );

                    if(i == 10) break;
                }

                pendingSources--;
                if (pendingSources == 0) {
                    return currentMap._fillSearchSource(currentMap._searchSource);
                }
            }
        });

        var query = {
            input: request.term,
        };

        if(!currentMap._autoCompleteService) currentMap._autoCompleteService = new google.maps.places.AutocompleteService();

        currentMap._autoCompleteService.getPlacePredictions(query, function(results, status) {
            //console.log(results, status);

            if (status == google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {
                    currentMap._searchSource.push(
                        {
                            id: results[i].place_id,
                            label: results[i].terms,
                            value: results[i].description,
                            type: 'place',
                        }
                    );
                }
            }

            pendingSources--;
            if (pendingSources == 0) {
                return currentMap._fillSearchSource(currentMap._searchSource);
            }
        });
    },
    minLength: 1
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
      switch(item.type) {
        case "place" :
            return $( "<li></li>" ).data("item.autocomplete", item)
                .append( '<b><span style="font-size: 13px; margin: 5px;">' + item.label[0].value + "</span></b>" + 
                    (item.label[1] ? '<span style="font-size: 11px; color: #999"> ' + item.label[1].value + "</span>"  : "") +
                    (item.label[2] ? '<span style="font-size: 11px; color: #999">, ' + item.label[2].value + "</span>" : "") +
                    (item.label[3] ? '<span style="font-size: 11px; color: #999">, ' + item.label[3].value + "</span>" : "") +
                    (item.label[4] ? '<span style="font-size: 11px; color: #999">, ' + item.label[4].value + "</span>" : "") +
                    (item.label[5] ? '<span style="font-size: 11px; color: #999">, ' + item.label[5].value + "</span>" : "")) 
                .appendTo( ul );
        case "dev" :
            return $( "<li></li>" ).data("item.autocomplete", item)
                .append( '<b><span style="font-size: 13px; margin: 5px;">' + item.label + "</span></b>" + 
                    (item.label ? '<span style="font-size: 11px; color: #999"> ' + I18N.t("Device") + "</span>"  : "") +
                    (item.label ? '<span style="font-size: 11px; color: #999"> ' + I18N.t('from') + ' ' + 
                    currentMap.getDomainName(item.marker.domainId) + "</span>" : ""))
                .appendTo( ul );
        case "net" :
            return $( "<li></li>" ).data("item.autocomplete", item)
                .append( '<b><span style="font-size: 13px; margin: 5px;">' + item.label + "</span></b>" + 
                    (item.label ? '<span style="font-size: 11px; color: #999"> ' + I18N.t("Network") + "</span>"  : "") +
                    (item.label ? '<span style="font-size: 11px; color: #999"> from ' + currentMap.getDomainName(item.marker.domainId) + "</span>" : ""))
                .appendTo( ul );
        case "port" :
            return $( "<li></li>" ).data("item.autocomplete", item)
                .append( '<b><span style="font-size: 13px; margin: 5px;">' + item.label + "</span></b>" + 
                    '<span style="font-size: 11px; color: #999"> ' + I18N.t("Port") + "</span>" +
                    ((currentMap._currentMarkerType == 'dev') ? '<span style="font-size: 11px; color: #999"> ' + I18N.t('on Device') + 
                    ' ' + item.marker.name + "</span>" : '') +
                    '<span style="font-size: 11px; color: #999"> ' + I18N.t('from') + ' ' + 
                    currentMap.getDomainName(item.marker.domainId) + "</span>")
                .appendTo( ul );
      }
    };

    $("#"+inputId).on("focus", function () {
        $(this).autocomplete("search", $("#"+inputId).val());
    });

    google.maps.event.addListener(this._map, 'click', function() {
        $("#"+inputId).blur();
    });
}

String.prototype.ucFirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
