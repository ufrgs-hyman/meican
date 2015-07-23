var MeicanMaps = new function() {
    this.DeviceMarker = function(options, color) {
        if (!color) color = MeicanMaps.generateColor(options.domainId);
        return new google.maps.Marker({
            icon: {
                path: 'M 15 15 L 35 15 L 25 35 z',
                anchor: new google.maps.Point(25, 35),
                fillColor: '#' + color,
                fillOpacity: 1,
                strokeColor: 'black',
            },
            options
        });
    }
    this.NetworkMarker = function(options, color) {
        if (!color) color = MeicanMaps.generateColor(options.domainId);
        return new StyledMarker({
            styleIcon: new StyledIcon(
                StyledIconTypes.MARKER,
                    {
                        color: color,
                    }
            ),
            options
        });
    }

    this.generateColor = function(id) {
        id = MeicanMaps.temp_fixId(id);

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

    this.temp_fixId = function(id) {
        while(id > 50) {
            return this.temp_fixId(id - 50);
        }
        return id;
    }

    this.getValidMarkerPosition = function(markers, type, position) {
        size = markers.length;
        lat = position.lat().toString().substring(0,6);
        lng = position.lng().toString().substring(0,6);

        for(i = 0; i < size; i++){
            anotherLat = markers[i].position.lat().toString().substring(0,6);
            anotherLng = markers[i].position.lng().toString().substring(0,6);

            if (markers[i].type == type &&
                    anotherLat == lat && 
                    anotherLng == lng) {
                return MeicanMaps.getValidMarkerPosition(markers, type, new google.maps.LatLng(position.lat(), position.lng() + 0.01));
            }
        }
        
        return position;
    }

    this.closeWindows = function() {
        var size = MeicanMaps.openedWindows.length;
        for (var i = 0; i < size; i++) {
            MeicanMaps.openedWindows[i].close();
        }
    }

    this.openedWindows = [];

    this.openWindow = function(map, marker, extra) {
        if (extra) {
            extra = '<br>' + extra + '</div></div>';
        } else {
            extra = '</div></div>';
        }

        markerWindow = new google.maps.InfoWindow({
            content: '<div class = "MarkerPopUp" style="width: 230px;"><div class = "MarkerContext">' +
                marker.info + extra
            });

        MeicanMaps.openedWindows.push(markerWindow);
        
        markerWindow.open(map, marker);

        return markerWindow;
    }

    this.getMarker = function(markers, type, id) {
        for(i = 0; i < markers.length; i++){
            if (markers[i].type == type && markers[i].id == parseInt(id)) {
                return markers[i];
            }
        }
        
        return null;
    }

    this.getMarkerByDomain = function(markers, type, domainId) {
    for(i = 0; i < markers.length; i++){
        if (markers[i].type == type && markers[i].domainId == domainId) {
            return markers[i];
        }
    }
    
    return null;
}

    this.setMarkerTypeVisible = function(markers, type) {
        var size = markers.length;
        for(i = 0; i < size; i++){ 
            if (markers[i].type == type) {
                markers[i].setVisible(true);
            } else {
                markers[i].setVisible(false);
            }
        }
    }
}

String.prototype.ucFirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
