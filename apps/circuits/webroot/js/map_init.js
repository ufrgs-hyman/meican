 var markersArray = [];
 var center = new google.maps.LatLng(-23.051931,-43.975511);
 var bounds = [];
 var button = "origin";
 var button_origin_pressed = false;
 var button_destination_pressed = false;
 var myOptions = {
   zoom: 3,
   center: center,
   streetViewControl: false,
   navigationControlOptions: {style: google.maps.NavigationControlStyle.ZOOM_PAN},
   mapTypeId: google.maps.MapTypeId.TERRAIN
 };
 var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

 // Create the context menu element
 var contextMenu = $(document.createElement('ul'))
	.attr('id', 'contextMenu');
 contextMenu.append(
     '<li><a href="#fromHere">' + from_here_string + '</a></li>'
 );
 contextMenu.bind('contextmenu', function() {return false;});
 $(map.getDiv()).append(contextMenu);

 MyOverlay.prototype = new google.maps.OverlayView();
 MyOverlay.prototype.onAdd = function() { }
 MyOverlay.prototype.onRemove = function() { }
 MyOverlay.prototype.draw = function() { }
 function MyOverlay(map) { this.setMap(map); }
 var overlay = new MyOverlay(map);
 var mapDiv = $(map.getDiv());

 markPlaces();