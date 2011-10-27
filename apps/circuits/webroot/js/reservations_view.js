js_function_interval = setInterval("griRefreshStatus(" + reservation_id + ")", 30000);
var cancelCont = 0;
if (status_array) {
    for (var index in status_array) {
        checkStatus(index, status_array[index]);
    }
}
alert("ola 2");
 var res_markersArray = [];

 var res_center = new google.maps.LatLng(0,0);
 var res_bounds = [];
 var res_lines = [];
 var res_myOptions = {
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
 var res_map = new google.maps.Map(document.getElementById("res_mapCanvas"), res_myOptions);
 
res_showCircuit();
alert("ola");