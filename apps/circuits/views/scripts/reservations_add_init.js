$("#res_name").focus();
var currentTab = "t1";
var tab1_valid = false;
var tab2_valid = true;
var previousTab;

var firstTime;
var edit_markersArray = new Array();
var edit_selectedMarkers = new Array();
var view_markersArray = new Array();
var edit_bounds = new Array();
var edit_lines = new Array();
var view_bounds = new Array();
var view_lines = new Array();

var waypoints = new Array();
var waypointsMarkers = new Array();

var src_networks = null;
var dst_networks = null;
var src_urn = null;
var dst_urn = null;
var path = new Array();

var counter = 0;

createTabs();
createSlider();    

var firstColor = "3a5879";
var color = new Array();
//for (var i in domains) {
//    color[i] = genHex(domains[i].id);
//}  

for (var i=0; i<domains.length; i++) {
    color[i] = genHex(i);
}

// MAPA PARA EDIÇÃO
var edit_center = new google.maps.LatLng(-23.051931,-60.975511);
var edit_myOptions = {
    zoom: 5,
    center: edit_center,
    streetViewControl: false,
    navigationControlOptions: {
        style: google.maps.NavigationControlStyle.ZOOM_PAN
    },
    backgroundColor: "white",
    mapTypeControl: false,
    mapTypeId: google.maps.MapTypeId.TERRAIN
};
var edit_map = new google.maps.Map(document.getElementById("edit_map_canvas"), edit_myOptions);
google.maps.event.trigger(edit_map, 'resize');
edit_map.setZoom( edit_map.getZoom() );

// MAPA PARA VISUALIZAÇÃO
var view_center = new google.maps.LatLng(-23.051931,-60.975511);
var view_myOptions = {
    zoom: 5,
    zoomControl: false,
    center: view_center,
    streetViewControl: false,
    mapTypeControl: false,
    draggable: false,
    disableDoubleClickZoom: true,
    keyboardShortcuts: false,
    scrollwheel: false,
    backgroundColor: "white",
    mapTypeId: google.maps.MapTypeId.TERRAIN
};
var view_map = new google.maps.Map(document.getElementById("view_map_canvas"), view_myOptions);
google.maps.event.trigger(view_map, 'resize');
view_map.setZoom( view_map.getZoom() );

// Create the context menu element
var contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
contextMenu.append('<li><a href="#fromHere">' + from_here_string + '</a></li>');
contextMenu.append('<li><a href="#toHere">' + to_here_string + '</a></li>');
contextMenu.bind('contextmenu', function() {
    return false;
});
$(edit_map.getDiv()).append(contextMenu);

MyOverlay.prototype = new google.maps.OverlayView();
MyOverlay.prototype.onAdd = function() { }
MyOverlay.prototype.onRemove = function() { }
MyOverlay.prototype.draw = function() { }
function MyOverlay(edit_map) {
    this.setMap(edit_map);
}
var overlay = new MyOverlay(edit_map);
var mapDiv = $(edit_map.getDiv());

edit_initializeMap();
initializeTimer();