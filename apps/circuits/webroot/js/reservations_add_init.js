$("#res_name").focus();
var currentTab = "t1";
var tab1_valid = false;
var tab2_valid = true;
var previousTab;

var srcSet = false;
var dstSet = false;
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


/*createTabs();
createSlider();*/ 

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
//    mapTypeControl: false,
    mapTypeId: google.maps.MapTypeId.TERRAIN
};

 function HomeControl(map, div, home) {

  // Get the control DIV. We'll attach our control
  // UI to this DIV.
  var controlDiv = div;

  // We set up a variable for the 'this' keyword
  // since we're adding event listeners later
  // and 'this' will be out of scope.
  var control = this;

  // Set CSS styles for the DIV containing the control
  // Setting padding to 5 px will offset the control
  // from the edge of the map
  controlDiv.style.padding = '5px';

  // Set CSS for the control border
  var goHomeUI = document.createElement('DIV');
  goHomeUI.title = 'Click to reset zoom';
  controlDiv.appendChild(goHomeUI);
  
  // Set CSS for the control interior
  var goHomeText = document.createElement('DIV');
  goHomeText.innerHTML = reset_zoom;
  goHomeUI.appendChild(goHomeText);
  $(goHomeText).addClass("zoom ui-button ui-widget ui-state-default ui-corner-all ui-widget-content").attr('style', "direction: ltr;overflow: hidden;text-align: center;position: relative;font-family: Arial, sans-serif;-webkit-user-select: none;font-size: 12px;line-height: 160%;padding: 0px 6px;border-radius: ;-webkit-box-shadow: rgba(0, 0, 0, 0.347656) 2px 2px 3px;box-shadow: rgba(0, 0, 0, 0.347656) 2px 2px 3px;min-width: 44px;color: black;border: 1px solid #A9BBDF;border-image: initial;padding-left: 6px;font-weight: normal;background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FEFEFE), to(#F3F3F3));background-osition: initial initial;background-repeat: initial initial;");
  
  google.maps.event.addDomListener(goHomeUI, 'click', function() {
    edit_resetZoom();
  });
}

var edit_map;
var view_map;
var view_center;
var overlay; var mapDiv;
var contextMenu;

(function($){
	/*função para criar mapa de edição */
	$.fn.makeEditMap = function(){ /*inicializa mapa */
		edit_map = new google.maps.Map(document.getElementById("edit_map_canvas"), edit_myOptions);
	
		var homeControlDiv = document.createElement('DIV');
		var homeControl = new HomeControl(edit_map, homeControlDiv);
		homeControlDiv.index = 1;
		edit_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
	
		google.maps.event.trigger(edit_map, 'resize');
		edit_map.setZoom( edit_map.getZoom() );
		infowindow = new google.maps.InfoWindow();
		google.maps.event.addListener(edit_map, 'zoom_changed', function() {
		   if (infowindow) {
			   infowindow.close(edit_map);
		   }
		});
	
		contextMenu = $(document.createElement('ul')).attr('id', 'contextMenu');
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
	//	MyOverlay.prototype.draw.setMap(edit_map);
		function MyOverlay(edit_map) {
			this.setMap(edit_map);
		}
		overlay = new MyOverlay(edit_map);
		mapDiv = $(edit_map.getDiv());
		edit_initializeMap();
		initializeTimer();
	
	
		// MAPA PARA VISUALIZAÇÃO

	view_center = new google.maps.LatLng(-23.051931,-60.975511);
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
	view_map = new google.maps.Map(document.getElementById("view_map_canvas"), view_myOptions);


	google.maps.event.trigger(view_map, 'resize');
	view_map.setZoom( view_map.getZoom() );
	};
	
	$(document).ready(function(){
		var f = function(){
		    var v = ($("#bandwidth[type=number]").val()/band_max)*100;
		    var k = 2*(50-v);
		    
		   $('#bandwidth_bar_inside').animate({width: v+'%', 'background-color': 'rgb('+(Math.round(255*(100-(k<0?0:k))/100))+','+(Math.round(255*(100-(-k<0?0:-k))/100))+',0)'}, 100);       
		};
		$("#bandwidth[type=number]").change(f).keyup(f).click(f).scroll(f);
		if (false){ /*configura tabs? não */
		    $('#tabs-res ul').show();
		    $('#tabs-3').show();
		    $('#tabs-res').tabs({select: function(event, ui){
		        clearFlash();
		        // antes de mostrar a aba, copia conteudo dos campos
		        fillConfirmationTab();
		        google.maps.event.trigger(view_map, 'resize');
		        view_setBounds(view_bounds);
		    }});
		} else {
		    $('#tabs-res ul').hide();
		    $('#tabs-3').hide();
		}
		/*$('#repeat_chkbox').button();*/
		/*$('#weekdays input[type=checkbox]').button();*/
		/* edições e cliques */
		$('#src_clearpath,#dst_clearpath').click(function(){
		    edit_clearAll();
		});
		$('#repeat_chkbox').click(function(){
		    showRecurrenceBox();
		});
		$('.recurrence-table td input[type=checkbox]').click(function(){
		    checkWeekDay(this.id);
		});
		$('#recur_radio,#nr_occurr,#untilDate').change(function(){
		    setUntilType();
		});
		/* resize da janela muda tamanho do mapa */
		$(window).resize(function() {
		  $('#edit_map_canvas').css('width', $('#main').width()-$($('#tabs-1 div.tab_subcontent')[1]).width()-25);
	/*      $('.tab-overlay').each(function(n, item){
		  	$(item).css({'width': $(item).parent().width(), 'height': $(item).parent().height()});
		  });*/
		});
		/* quando digita nome, tira overlay */
		$('#res_name').keyup(function(){
			if ($(this).val())
				$('.tab-overlay').fadeOut();
			else
				$('.tab-overlay').fadeIn();
		});
		$('form#reservation_add').submit(function() {
			validateReservationForm();
		});
		$('#res_name').focus();
		$('#edit_map_canvas').css('width', $('#main').width()-$($('#tabs-1 div.tab_subcontent')[1]).width()-50);
	  	$.fn.makeEditMap();
	});
	
	
	
})(jQuery);

