js_function_interval = setInterval("refreshStatus(" + reservation_id + ")", 30000);
var cancelCont = 0;
if (status_array) {
    for (var index in status_array) {
        checkStatus(index, status_array[index]);
    }
}

 var markersArray = [];
 var topology = false;
 var center = new google.maps.LatLng(0,0);
 var bounds = [];
 var myOptions = {
   zoom: 3,
   center: center,
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
 var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
 toggleTopology();