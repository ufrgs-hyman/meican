var cancelCont = 0;

var res_map = null;
var res_center = null;
var res_markersArray = [];
var res_bounds = [];
var res_lines = [];
var res_myOptions = null;

$(document).ready(function() {
    if (refreshReservation) {
        griRefreshStatus(reservation_id);
        js_function_interval = setInterval("griRefreshStatus(" + reservation_id + ")", 30000);
    }

    if (status_array) {
        for (var index in status_array) {
            checkStatus(status_array[index].id, status_array[index].status);
        }
    }

    res_center = new google.maps.LatLng(0,0);

    res_myOptions = {
        zoom: 3,
        center: res_center,
        draggable: false,
        disableDoubleClickZoom: true,
        scrollwheel: false,
        keyboardShortcuts: false,
        streetViewControl: false,
        navigationControl: false,
        scaleControl: false,
        mapTypeControl: false,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    };
    
    res_map = new google.maps.Map(document.getElementById("res_mapCanvas"), res_myOptions);
    res_showCircuit();
});