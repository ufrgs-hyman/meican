var cancelCont = 0;

var res_map = null;
var res_markersArray = [];
var res_bounds = [];
var res_lines = [];

function res_buildMap(){
    if ((typeof(refreshReservation) != "undefined") && refreshReservation) {
        griRefreshStatus(reservation_id);
        js_function_interval = setInterval("griRefreshStatus(" + reservation_id + ")", 30000);
    }

    if ((typeof(status_array) != "undefined") && status_array) {
        for (var index in status_array) {
            checkStatus(status_array[index].id, status_array[index].status);
        }
    }
    
    
    this.resizefn = function() {
        if (!$('#res_mapCanvas'))
            return;
        var of1 = $('#subtab-points').offset(),
            of2 = $('#tabs-2').offset();
        if (of1 && of2)
            $('#res_mapCanvas').css('width', of1.left-of2.left-4 );
    };
    $(window).resize(this.resizefn);
    var finishfn = function(){
        $(window).unbind('resize');
        $('#main').unbind('pjax:start', finishfn);
    };
    $('#main').bind('pjax:start', finishfn);
    
    this.res_map = new google.maps.Map(document.getElementById("res_mapCanvas"), {
        zoom: 3,
        center: new google.maps.LatLng(0,0),
        draggable: false,
        disableDoubleClickZoom: true,
        scrollwheel: false,
        keyboardShortcuts: false,
        streetViewControl: false,
        navigationControl: false,
        scaleControl: false,
        mapTypeControl: false,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    });
    this.res_showCircuit();
}

(function($){
    $(function(){
        res_buildMap();
    });
})(jQuery);


function griRefreshStatus(res_id) {
    $('.load').show();
    $.ajax ({
        type: "POST",
        url: baseUrl+'circuits/reservations/gri_refresh_status',
        data: {
            res_id: res_id
        },
        dataType: "json",
        success: function(data) {
            $('.load').hide();
            if (data) {
                if (data.length != 0) {
                    var status_id = null;

                    for (var i in data) {
                        status_id = '#status' + data[i].id;
                
                        if (data[i].status != $(status_id).html()) {
                            $(status_id).empty();
                            $(status_id).html(data[i].status);
                
                            checkStatus(data[i].id, data[i].original_status);
                        }
                    }
                }
            } else {
                setFlash(str_error_refresh_status,"error");
            }
        },
        error: function(jqXHR) {
            if (jqXHR.status == 406)
                location.href = baseUrl+'init/gui';
        }
    });
}

function disabelCancelButton(elemId) {
    if ($(elemId).attr("checked"))
        cancelCont++;
    else
        cancelCont--;

    if (cancelCont) {
        $("#cancel_button").removeAttr("disabled");
        $("#cancel_button").removeClass("ui-state-disabled");
    } else {
        $("#cancel_button").attr("disabled", "disabled");
    }
}

function res_showCircuit(){
    //    if ((src_lat_network == dst_lat_network) && (src_lng_network == dst_lng_network)) {
    //        var aux = parseFloat(dst_lng_network);
    //        aux += 0.0005;
    //        dst_lng_network = aux.toString();
    //    }
    //    alert(reservation_path[0].descr);
    //    alert(reservation_path[1].descr);
    var networks_coordinates = [];
    if(typeof reservation_path === "undefined" &&
        (typeof src_lat_network === "undefined" || 
        typeof src_lng_network === "undefined")) {
        return ;
        }
    
    if ((typeof(reservation_path)!="undefined") && (reservation_path.length > 0)) {
        
        for (var i=0;i<reservation_path.length;i++){
        
            var coord = new google.maps.LatLng(reservation_path[i].latitude, reservation_path[i].longitude);
            if (i==0) {
                this.res_addMarker(coord, "src");
            } else if (i== reservation_path.length-1) {
                this.res_addMarker(coord, "dst");
            } else {
                this.res_addMarker(coord, "way");
            }
        
            networks_coordinates.push(coord);
        
            this.res_bounds.push(coord);
            this.res_setBounds(res_bounds);
        
        }
    } else {
        var coord_src = new google.maps.LatLng(src_lat_network, src_lng_network);
        this.res_addMarker(coord_src, "src");
        this.res_bounds.push(coord_src);

        var coord_dst = new google.maps.LatLng(dst_lat_network, dst_lng_network);
        this.res_addMarker(coord_dst, "dst");   
        this.res_bounds.push(coord_dst);
        
        networks_coordinates.push(coord_src);
        networks_coordinates.push(coord_dst);
        this.res_setBounds(res_bounds);
    }
    this.res_drawPath(networks_coordinates);
}

function res_addMarker(location, where) {

    var color;

    if (where == "src") {
        color = "0000EE";
    } else if (where == "dst") {
        color = "FF0000";
    } else if (where == "way") {
        color = "00FF00";
    }
    
    var res_marker = new StyledMarker({
        position: location,
        styleIcon:new StyledIcon(StyledIconTypes.MARKER,{
            color:color
        }),
        map: this.res_map
    });

    this.res_markersArray.push(res_marker);
    res_marker.setMap(this.res_map);
}

function res_drawPath(networks_coordinates){
    //var origin = coordinatesArray[0];
    //var destination = coordinatesArray[(coordinatesArray.length -1)];
    //var flightPlanCoordinates = [origin, destination];
    var line = new google.maps.Polyline({
        path: networks_coordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });

    line.setMap(this.res_map);
    this.res_lines.push(line);
}

function res_drawTopology(coordinatesArray){
    
    var flightPlanCoordinates = [];
        
    for (var i=0; i<coordinatesArray.length; i++){
        flightPlanCoordinates.push(coordinatesArray[i]);
    }
        
    var line = new google.maps.Polyline({
        path: flightPlanCoordinates,
        strokeColor: "#0000FF",
        strokeOpacity: 0.5,
        strokeWeight: 4
    });

    line.setMap(this.res_map);
    this.res_lines.push(line);
}

function res_clearAll(){
    for (var i = 0; i < res_lines.length; i++) {
        this.res_lines[i].setMap(null);
    }    
    this.res_setBounds(res_bounds);
}

function res_clearMarkers(){
    for (var i=0; i<res_markersArray.length; i++){
        this.res_markersArray[i].setMap(null);
    }
}

function res_clearBounds(){
    for (var i=0; i<res_bounds.length; i++){
        this.res_bounds.pop();
    }
}

function res_clearAll(){
    this.res_clearMarkers();
    this.res_clearAll();
    this.res_clearBounds();
}

function res_setBounds(flightPlanCoordinates){
    polylineBounds = new google.maps.LatLngBounds();

    for (i=0; i<flightPlanCoordinates.length; i++) {
        polylineBounds.extend(flightPlanCoordinates[i]);
    }
    this.res_map.fitBounds(polylineBounds);
    this.res_map.setCenter(polylineBounds.getCenter());
}