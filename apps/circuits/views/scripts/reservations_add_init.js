var currentTab = "t1";
var previousTab;

createTabs();
createSlider();

var firstTime = true;

var center = new google.maps.LatLng(-23.051931,-60.975511);
var myOptions = {
    zoom: 5,
    center: center,
    streetViewControl: false,
    navigationControlOptions: {
        style: google.maps.NavigationControlStyle.ZOOM_PAN
    },
    backgroundColor: "white",
    mapTypeControl: false,
    mapTypeId: google.maps.MapTypeId.TERRAIN
};
var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
google.maps.event.trigger(map, 'resize');
map.setZoom( map.getZoom() );

var src_networks = null;
var dst_networks = null;
var src_urn = null;
var dst_urn = null;
initializeTimer();