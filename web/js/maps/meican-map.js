function MeicanMap() {
    this._autoCompleteService;       // Google Autocomplete Service
    this._placesService;             // Google Places Service
    this._map;                       // Google Map
    this._searchSource = [];         // searchbox source
    this._fillSearchSource;          // searchbox fill source function
    this._markers = [];              // markers container
    this._openedWindows = [];        // opened marker windows
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
    console.log("troca color");
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

MeicanMap.prototype.openWindow = function(marker, extra) {
    if (extra) {
        extra = '<br>' + extra + '</div></div>';
    } else {
        extra = '</div></div>';
    }

    markerWindow = new google.maps.InfoWindow({
        content: '<div class = "MarkerPopUp" style="width: 230px; line-height: 1.35; overflow: hidden; white-space: nowrap;"><div class = "MarkerContext">' +
            marker.info + extra
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

MeicanMap.prototype.searchMarkerByName = function (type, name) {
    results = [];
    name = name.toLowerCase();
    var size = this._markers.length;
    for(var i = 0; i < size; i++){
        if (this._markers[i].type == type && ((this._markers[i].name.toLowerCase()).indexOf(name) > -1)) {
            results.push(this._markers[i]);
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
        center: new google.maps.LatLng(0,0),
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

MeicanMap.prototype.buildSearchBox = function(divId, inputId, buttonId, openWindowFunction) {
    this._map.controls[google.maps.ControlPosition.TOP_LEFT].push(
        document.getElementById(divId));

    var currentMap = this;

    google.maps.event.addListenerOnce(this._map,'tilesloaded',function(){
       $("#" + inputId).focus();
    });

    $("#" + divId).show();

    $( "#" + inputId ).autocomplete({
    autoFocus: true,
    delay: 200,   
    select: function (event, ui) {
      console.log(ui.item);

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
            case 'dev':
                var bounds = new google.maps.LatLngBounds();

                bounds.extend(ui.item.marker.getPosition()); 

                currentMap._map.fitBounds(bounds);
                currentMap._map.setZoom(11);
                //openWindowFunction(markers, ui.item.marker);
        }
    }, 
    source: function (request, response) {
        var term = request.term;
        var previous = this.previous;
        
        if(!term.trim()) return;

        if (term && (term == previous)) {
            response(currentMap._searchSource);
            return;
        }

        var query = {
            input: request.term,
        };

        if(!currentMap._autoCompleteService) currentMap._autoCompleteService = new google.maps.places.AutocompleteService();

        currentMap._autoCompleteService.getPlacePredictions(query, function(results, status) {
            currentMap._searchSource = [];

            var devs = currentMap.searchMarkerByName('dev', request.term); 
            var size = devs.length;
            for (var i = 0; i < size; i++) {
                currentMap._searchSource.push(
                    {
                        label: devs[i].name,
                        type: 'dev',
                        marker: devs[i]
                    }
                );

                if(i == 10) return currentMap._fillSearchSource(currentMap._searchSource);
            };

            //searchSource = $.ui.autocomplete.filter(searchSource, searchTerm);
            console.log(results, status);

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

            currentMap._fillSearchSource(currentMap._searchSource);
        });

        currentMap._fillSearchSource = response;
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
                    (item.label ? '<span style="font-size: 11px; color: #999"> ' + "Device" + "</span>"  : "") +
                    (item.label ? '<span style="font-size: 11px; color: #999"> from ' + "domainName" + "</span>" : ""))
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
