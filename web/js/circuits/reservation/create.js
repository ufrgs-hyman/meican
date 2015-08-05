$(document).ready(function() {
	  $('#map-canvas').show();
	 
    $('#search-box').focus();
    
    prepareConfirmDialog();
    prepareBandwidthSpinner();
    
    $(".hourPicker").timepicker({
		    timeFormat: "H:i",
        step: 30,
	  });
});

var autoCompleteService;
var placesService;
var searchTerm;
var searchSource = [];
var fillSearchSource;
var previousTerm;

var markers = [];
var sourceMarker;
var destinMarker;
var wayPoints = [];
var domainsList;
var map;
var markerCluster;
var circuit;
var currentMarkerType = "net";
var devicesLoaded = false;

var MARKER_OPTIONS_NET = '' +
'<div><button style="font-size: 11px; width: 48.25%;" id="set-as-source">' + tt('From here') + '</button>' +
'<button style="font-size: 11px; width: 48.25%;" id="set-as-dest">' + tt('To here') + '</button></div><div style="height: 2px;"></div>' +
'<div><button style="font-size: 11px; width: 48.25%;" id="add-waypoint">' + tt('Add waypoint') + '</button>' +
'<button style="font-size: 11px; width: 48.25%;" id="set-as-intra">' + tt('Intra-domain') + '</button></div>';
var MARKER_OPTIONS_DEV = '' +
'<div><button style="font-size: 11px; width: 48.25%;" id="set-as-source">' + tt('From here') + '</button>' +
'<button style="font-size: 11px; width: 48.25%;" id="set-as-dest">' + tt('To here') + '</button></div><div style="height: 2px;"></div>' +
'<div><button style="font-size: 11px; width: 98%;" id="add-waypoint">' + tt('Add waypoint') + '</button>' +
'</div>';
var MARKER_OPTIONS_END_POINT = '' +
'<div><button style="font-size: 11px; width: 98%;" id="remove-endpoint">' + tt('Remove endpoint') + '</button></div><div style="height: 2px;"></div>' +
'<div><button style="font-size: 11px; width: 98%;" id="add-waypoint">' + tt('Add waypoint') + '</button></div>';
var MARKER_OPTIONS_INTRA = '' +
'<button style="font-size: 11px; width: 98%;" id="remove-intra">' + tt('Remove intra-domain circuit') + '</button>';



function prepareConfirmDialog() {
	$("#confirm-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        appendTo: "#reservation-form",
        buttons: [{
        	id:"confirm-button",
            text: tt('Yes'),
            click: function() {
            	$("#confirm-button").attr("disabled", "disabled");
            	if (validateForm()) {
            		$.ajax({
            	        type: "POST",
            	        url: baseUrl + '/circuits/reservation/request',
            	        data: $("#reservation-form").serialize(),
            	        success: function (resId) {
            	        	if (resId>0) {
            	        		$.ajax({
            	        			type: "POST",
            	        			url: baseUrl + '/circuits/reservation/confirm', 
            	        			data: {
            	        				id: resId,
            	        			}
                        	    });
                	        	window.location.href = baseUrl + '/circuits/reservation/view?id=' + resId;
            	        	} else if(resId==-1){
            	        		showError(tt("You are not allowed to create a reservation involving these selected domains."));
            	        	} else {
            	        		showError(tt("Error proccessing your request. Contact your administrator."));
            	        	}
            	        },
            	        error: function() {
            	        	showError(tt("Error proccessing your request. Contact your administrator."));
            	        }
            	    });
            	}
        	}
        },{
        	id:"cancel-button",
            text: tt('No'),
            click: function() {
            	$("#confirm-dialog").dialog( "close" );
            }
        }],
        close: function() {
        	$("#error-confirm-dialog").hide();
        	$("#error-confirm-dialog").html("");
        	$("#confirm-button").attr("disabled", false);
        },
    });
    
    $("#request-button").click(function() {
    	$("#confirm-dialog").dialog("open");
    	return false;
    });
}

function validateForm() {
	var isValid = true;
	var errors = "";
	if (!$("#src-vlan").val() || $("#src-vlan").val() == "null") {
		errors += '<br>- ' + tt('Source end point is undefined or incomplete.');
		isValid = false;
	}
    if (!$("#dst-vlan").val() || $("#dst-vlan").val() == "null") {
		errors += '<br>- ' + tt('Destination end point is undefined or incomplete.');
		isValid = false;
    }
    if ($("#waypoints_order").children('.ui-state-default').length > 0) {
    	var wayPointsItems = $("#waypoints_order").children();
    	for (var i = 0; i < wayPointsItems.length; i++) {
    		var wayPoint = wayPointsItems.children(".vlan").val();
    		if (wayPoint == '') {
    			errors += '<br>- ' + tt('Waypoint information is required.');
        		isValid = false;
    		}
        }
    }
	if (diffDate($("#start-time").val(),$("#finish-time").val(),$("#start-date").val(),$("#finish-date").val()) == false) {
		errors += '<br>- ' + tt('The finish date must be after start date.');
		isValid = false;
	}
	if (isNaN($("#start-time").val().split(":").toString().replace(",","")) || isNaN($("#finish-time").val().split(":").toString().replace(",",""))) {
		errors += '<br>- ' + tt('The start time or the finish time are invalid.');
		isValid = false;
	}
	if (isNaN($("#bandwidth").val()) || parseInt($("#bandwidth").val()) > 1000 || parseInt($("#bandwidth").val()) < 1) {
		errors += '<br>- ' + tt('The bandwidth must be between 1 and 1000.');
		isValid = false;
	}
  if (!($("#name").val().trim())) {
    errors += '<br>- ' + tt('A reservation name is required.');
    isValid = false;
  }
	
	if(!isValid) {
		showError(tt('Error(s) found') + ":<br>"+errors);
		$("#error-confirm-dialog").show();
	}
	
	return isValid;
}

function showError(message) {
	$("#error-confirm-dialog").html("<br>"+message);
	$("#error-confirm-dialog").show();
}

///////// inicializa bandwith spinner /////////////

function prepareBandwidthSpinner() {
	var f = function() {
        var v = ($("#bandwidth").val() / $("#bandwidth").attr('aria-valuemax')) * 100;
        if (v > 100 || v < 0)
            return;
        var k = 2 * (50 - v);
        $('#bandwidth_bar_inside').width(v + '%');
    };

    $('#bandwidth').attr("min", 100).attr("max", 1000).attr("step", 100).
    		spinner({
    			spin: f,
				stop: f
    }).spinner("disable").bind('spin', f).change(f).keyup(f).click(f).scroll(f);
    $("#bandwidth").val(100);
    f();
    disableBandwidthSpinner();
}

function disableBandwidthSpinner() {
	$("#bandwidth_un").attr("disabled", "disabled");
    $("#bandwidth").spinner('disable');
}

function enableBandwidthSpinner() {
        //var bmin_tmp = (src_min_cap >= dst_min_cap) ? src_min_cap : dst_min_cap;
        //var bmax_tmp = (src_max_cap <= dst_max_cap) ? src_max_cap : dst_max_cap;
        //var bdiv_tmp = (src_div_cap == dst_div_cap) ? src_div_cap : band_div;
        $('#bandwidth').spinner({
            min: 100,
            max: 1000,
            step: 100
        }).spinner("enable").disabled(false).trigger('click');
        $("#bandwidth").trigger("change");
}

/////////////// botoes superiores da tabela origem destino /////////

function initEndPointButtons(endPointType, markers) {
    $('#' + endPointType + '-clear-endpoint').click(function() {
		    removeMarkerEndPoint(endPointType);
        setNetworkSelected(endPointType);
        $('#' + endPointType + '-domain').val("");
    });
	
    $('#' + endPointType + '-select-current-host').click(function() {
      	var currentHostMarker = findCurrentHostMarker(markers);
      	setEndPoint(endPointType, currentHostMarker);
    });
    
    $('#' + endPointType + '-copy-urn').click(function() {
    	  openCopyUrnDialog(endPointType);
    });
    
    $("#copy-urn-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: "auto",
        height: "110",
        beforeClose: function() {
            $("#copy-urn-field").val("");
        }
    });
}

function findCurrentHostMarker(markers) {
  	var currentHost = document.URL;
  	currentHost = currentHost.split("/")[2].split(".");
  	
  	if (currentMarkerType == "net") {
  		for (var i = 0; i < markers.length; i++) {
  	    	if (markers[i].type == "net") {
  	    		var domain = markers[i].name; /////// ARRRUMA
  	    		domain = domain.split(".");
  	    		var equal = true;
  	    		var j = domain.length - 1;
      			var k = currentHost.length - 1;
  	    		while (j > -1 && k > -1) {
  	    			console.log(domain[j] + currentHost[k]);
  	    			if (domain[j] == currentHost[k]) {
  	    				j--;
  	    				k--;
  	    			} else {
  	    				equal = false;
  	    				break;
  	    			}
  	    		}
  	    		if (equal) return markers[i];
  	    	}
  	    }
  	}
	
  	alert(tt("Current host is not present in known topology"));
  	
  	return null;
}

function openCopyUrnDialog(endPointType) {
	$.ajax({
		url: baseUrl+'/topology/urn/get',
		dataType: 'json',
		data: {
			id: $('#' + endPointType + '-port').val(),
		},
		success: function(response){
			$("#copy-urn-field").val(response.value);
		    $("#copy-urn-dialog").dialog("open");
		}
	});
	
}

/////////////// LISTA INTERATIVA DE WAYPOINTS //////////////////////

function enableWayPointsSortable(markers) {
	$("#waypoints_order").sortable({
        update: function(event, ui) {
            var sortableOrder = $(this).sortable('toArray');
            var newWayPoints = [];
        	for (var i = 0; i < sortableOrder.length; i++) {
            	newWayPoints[i] = MeicanMaps.getMarker(markers, currentMarkerType, parseInt(sortableOrder[i].replace("way","")));
            }

            wayPoints = newWayPoints;
            
            drawCircuit();
        }

    }).css("display", "block");
}

function prepareDialogDeviceSelect(markers, wayObject) {
    var object = MeicanMaps.getMarker(markers, currentMarkerType, $(wayObject).attr("id").replace("way",""));
	
    $("#waypoint-domain").val($(wayObject).children(".domain-id").val());

    if (currentMarkerType == "net") {
        fillNetworkSelect('waypoint', $(wayObject).children(".domain-id").val(), $(wayObject).children(".network-id").val(), true);
        fillDeviceSelect('waypoint', $(wayObject).children(".domain-id").val(), $(wayObject).children(".network-id").val(), 
        $(wayObject).children(".device-id").val());
    } else {
        fillDeviceSelect('waypoint', $(wayObject).children(".domain-id").val(), $(wayObject).children(".network-id").val(), 
        $(wayObject).children(".device-id").val(), true);
    }

    fillPortSelect('waypoint', $(wayObject).children(".device-id").val(), $(wayObject).children(".port-id").val());
    fillVlanSelect('waypoint', $(wayObject).children(".port-id").val(), $(wayObject).children(".vlan").val());
}

function setWayPointDevice(wayObject) {
	var deviceId = $("#waypoint-device").val();
	var domainName = $(wayObject).html().split("(")[0];
	var rest = $(wayObject).html().split(")")[1];
	var deviceName = $("#waypoint-device").children("[value=" + deviceId + "]").text();
	if (deviceId != "null") {
		$(wayObject).html(domainName + "(" + deviceName + " - " + 
            $("#waypoint-port").children("[value=" + $("#waypoint-port").val() + "]").text() + " - " +
            $("#waypoint-vlan").val() + 
            ")" + rest);
	} else {
		$(wayObject).html(domainName + "(" + tt("click to fill waypoint") + ")" + rest);
	}

	$(wayObject).children(".domain-id").val($("#waypoint-domain").val());
    $(wayObject).children(".network-id").val($("#waypoint-network").val());
    $(wayObject).children(".device-id").val($("#waypoint-device").val());
    $(wayObject).children(".port-id").val($("#waypoint-port").val());
    $(wayObject).children(".vlan").val($("#waypoint-vlan").val());
    console.log($(wayObject).children(".domain-id").val(),
        $(wayObject).children(".network-id").val(),
        $(wayObject).children(".device-id").val(),
        $(wayObject).children(".port-id").val(),
        $(wayObject).children(".vlan").val());
}

function addWayPoint(markers, marker) {
    marker.circuitPoints++;
	markerCluster.removeMarker(marker);
	marker.setMap(map);
	
	if (wayPoints.length < 1) {
		$("#reservation-waypoints").slideDown(1);
	}
	
	wayPoints.push(marker);

    var inputData = '';
    if (marker.type == "net") {
        inputData = '<input value="' + marker.id + '" type="text" class="network-id" hidden></input>' + 
             '<input type="text" class="device-id" hidden></input>';
    } else {
        inputData = '<input type="text" class="network-id" hidden></input>' + 
             '<input value="' + marker.id + '" type="text" class="device-id" hidden></input>';
    }
	
	$("#waypoints_order").append("<li class='ui-state-default opener' id='way" + 
    		 marker.id + "'>" + getDomainName(marker.domainId) + " (" + tt("click to fill waypoint") + ")" + 
             '<input value="' + marker.domainId + '" type="text" class="domain-id" hidden></input>' + 
             inputData + 
             '<input name="ReservationForm[waypoints][port][]" type="text" class="port-id" hidden></input>' + 
             '<input name="ReservationForm[waypoints][vlan][]" type="text" class="vlan" hidden></input>' + 
             '</li>');
	
	$(".opener").click(function() {
		var content = this;
        $("#waypoint-dialog").dialog({
        	autoOpen: false,
            modal: true,
            resizable: false,
            width: "auto",
            open: function(event, ui) {
            	prepareDialogDeviceSelect(markers, content);
            },
            buttons: [{
                text: tt('Remove'),
                click: function() {
                    deleteWayPoint(content, markers);
                    $("#waypoint-dialog").dialog( "close" );
                }},{
                text: tt('Save'),
                click: function() {
                    setWayPointDevice(content);
                    $("#waypoint-dialog").dialog( "close" );
                }},{
                text: tt('Cancel'),
                click: function() {
                    $("#waypoint-dialog").dialog( "close" );
                }
            }],
        });
        $("#waypoint-dialog").dialog("open");
    });
	
	drawCircuit();
}

function hideReservationTabs() {
	$("#reservation-tab").fadeOut();
	$(".reservation-point").fadeOut();
}

function deleteWayPoint(wayObject, markers) {
	var marker = MeicanMaps.getMarker(markers, currentMarkerType, $(wayObject).attr("id").replace("way",""));
    marker.circuitPoints--;
	
	for (var i = 0; i < wayPoints.length; i++) {
		if (wayPoints[i] == marker) {
			wayPoints.splice(i, 1);
			break;
		}
	}
	
	if (wayPoints.length < 1) {
		$("#reservation-waypoints").hide();
	}

    if (marker.circuitPoints == 0) {
        marker.setMap(null);
        markerCluster.addMarker(marker);
    }

    $(wayObject).remove();
	
	drawCircuit();
}

function deleteWayPoints() {
	$("#waypoints_order").empty();
	
	var marker = wayPoints.pop();
	while(marker) {
		marker.setMap(null);
		markerCluster.addMarker(marker);
		marker.circuitMode = "none";
		
		marker = wayPoints.pop();
	}
	
	if (wayPoints.length < 1) {
		$("#reservation-waypoints").hide();
	}
	
	drawCircuit();
}

///////// SELECTS DINAMICOS ////////////

function fillDomainSelect(endPointType) {
	clearSelect(endPointType, "domain");
	$("#"+ endPointType + "-domain").append('<option value="">' + tt('loading') + '</option>');
	$.ajax({
		url: baseUrl+'/topology/domain/get-all',
		data: {
			cols: JSON.stringify(['id','name']),
		},
		dataType: 'json',
		success: function(domains){
			clearSelect(endPointType, "domain");
			$("#"+ endPointType + "-domain").append('<option value="">' + tt('select') + '</option>');
			for (var i = 0; i < domains.length; i++) {
				$("#"+ endPointType + "-domain").append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
			}
		},
	});
}

function fillNetworkSelect(endPointType, domainId, networkId, initDisabled) {
    disableSelect(endPointType, "network");
	clearSelect(endPointType, "network");
	if (domainId != "" && domainId != null) {
		$("#"+ endPointType + "-network").append('<option value="">' + tt('loading') + '</option>');
		$.ajax({
			url: baseUrl+'/topology/network/get-by-domain',
			data: {
				id: domainId,
			},
			dataType: 'json',
			success: function(response){
				clearSelect(endPointType, "network");
				$("#"+ endPointType + "-network").append('<option value="">' + tt('select') + '</option>');
				if (!initDisabled) enableSelect(endPointType, "network");
				for (var i = 0; i < response.length; i++) {
					$("#"+ endPointType + "-network").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
			    }
				if (networkId != null) {
					$("#"+ endPointType + "-network").val(networkId);
				}
			}
		});
	} 
}

function fillDeviceSelect(endPointType, domainId, networkId, deviceId, initDisabled) {
    disableSelect(endPointType, "device");
	clearSelect(endPointType, "device");
    parent = null;
	if (networkId != "" && networkId != null) {
        parent = [];
		parent[0] = "network";
        parent[1] = networkId;
	} else if (domainId != "" && domainId != null) {
        parent = [];
        parent[0] = "domain";
        parent[1] = domainId;
    } 

    if (parent) {
        $("#"+ endPointType + "-device").append('<option value="">' + tt('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/device/get-by-' + parent[0],
            dataType: 'json',
            data: {
                id: parent[1],
            },
            success: function(response){
                clearSelect(endPointType, "device");
                $("#"+ endPointType + "-device").append('<option value="">' + tt('select') + '</option>');
                if (!initDisabled) enableSelect(endPointType, "device");
                for (var i = 0; i < response.length; i++) {
                    if (response[i].name == "") response[i].name = "default";
                    $("#"+ endPointType + "-device").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                }
                if (deviceId != null && deviceId != "") {
                    $("#"+ endPointType + "-device").val(deviceId);
                }
            }
        });
    } 
}

function fillPortSelect(endPointType, deviceId, portId) {
    disableSelect(endPointType, "port");
	clearSelect(endPointType, "port");
	if (deviceId != "" && deviceId != null) {
		$("#"+ endPointType + "-port").append('<option value="">' + tt('loading') + '</option>');
		$.ajax({
			url: baseUrl+'/circuits/reservation/get-port-by-device',
			dataType: 'json',
			data: {
				id: deviceId,
				cols: JSON.stringify(['id','name']),
			},
			success: function(response){
				clearSelect(endPointType, "port");
				$("#"+ endPointType + "-port").append('<option value="">' + tt('select') + '</option>');
				enableSelect(endPointType, "port");
				for (var i = 0; i < response.length; i++) {
					var name = response[i].name;
					if (response[i].port == "") {
						name = tt("default");
					}
					$("#"+ endPointType + "-port").append('<option value="' + response[i].id + '">' + name + '</option>');
			    }
                if (portId != null && portId != "") $("#"+ endPointType + "-port").val(portId);
			}
		});
	} 
}

function fillVlanSelect(endPointType, portId, vlan) {
    disableSelect(endPointType, "vlan");
	clearSelect(endPointType, "vlan");
	if (portId != "" && portId != null) {
		$("#"+ endPointType + "-vlan").append('<option value="">' + tt('loading') + '</option>');
		$.ajax({
			url: baseUrl+'/topology/port/get-vlan-range',
			dataType: 'json',
			data: {
				id: portId,
			},
			success: function(response){
				clearSelect(endPointType, "vlan");
                if(response) {
    				var ranges = response.split(",");
    				for (var i = 0; i < ranges.length; i++) {
                        var interval = ranges[i].split("-");
                        if (interval.length > 1)
                            $("#"+ endPointType + "-vlan").append('<option value="' + ranges[i] + '">' + ranges[i] + '</option>');
    			    }

                    for (var i = 0; i < ranges.length; i++) {
                        var interval = response.split("-");
                        var low = parseInt(interval[0]);
                        var high = low;
                        if (interval.length > 1) {
                            high = parseInt(interval[1]);
                        }
                        
                        for (var j = low; j < high+1; j++) {
                            $("#"+ endPointType + "-vlan").append('<option value="' + j + '">' + j + '</option>');
                        }
                        if (vlan != null && vlan != "") {
                            $("#"+ endPointType + "-vlan").val(vlan);
                        }
                    }
    				enableSelect(endPointType, "vlan");
                }
			}
		});
	}
}

function setNetworkSelected(endPointType, marker) {
	if (marker) {
		$("#"+ endPointType + "-domain").val(marker.domainId);
		fillNetworkSelect(endPointType, marker.domainId, marker.id);
		fillDeviceSelect(endPointType, null, marker.id);
	} else {
		fillNetworkSelect(endPointType);
		fillDeviceSelect(endPointType);
	}
	
	fillPortSelect(endPointType);
	fillVlanSelect(endPointType);
}

function setDeviceSelected(endPointType, marker) {
	if (marker) {
		$("#"+ endPointType + "-domain").val(marker.domainId);
		fillNetworkSelect(endPointType, marker.domainId);
		fillDeviceSelect(endPointType, marker.domainId, null, marker.id);
		fillPortSelect(endPointType, marker.id);
	} else {
		$("#"+ endPointType + "-domain").val("null");
		fillNetworkSelect(endPointType);
		fillDeviceSelect(endPointType);
		fillPortSelect(endPointType);
	}
	
	fillVlanSelect(endPointType);
}

///////////// DESENHAR CIRCUITO NO MAPA ///////////////

function drawCircuit() {
	var path = [];
	
	if (circuit != null) 
		circuit.setMap(null);
	
	if (sourceMarker) {
		path.push(sourceMarker.position);
	} 
	
	for (i = 0; i < wayPoints.length; i++) {
		path.push(wayPoints[i].position);
	}
	
	if (destinMarker) {
		path.push(destinMarker.position);
	}
	
	if (path.length > 1) {
		circuit = new google.maps.Polyline({
	        path: path,
	        strokeColor: "#0000FF",
	        strokeOpacity: 0.5,
	        strokeWeight: 5,
	        geodesic: false,
	    });
	    
	    try {
	    	circuit.setMap(map);
	    	setMapBounds(path);
	    	enableBandwidthSpinner();
	    } catch (e) {
	    }
	} else {
		disableBandwidthSpinner();
	}
}

function setMarkerEndPoint(endPointType, marker) {
	removeMarkerEndPoint(endPointType);
	
	if (endPointType == "src") {
		if (marker) {
			sourceMarker = marker;
		} 
		
	} else if (marker) {
			destinMarker = marker;
	}
	
	if (marker && (sourceMarker == destinMarker)) {
		marker.circuitMode = 'intra';
	} else if (marker) {
		marker.circuitMode = endPointType;
		markerCluster.removeMarker(marker);
		marker.setMap(map);
	}
	
	drawCircuit();
}

function removeMarkerEndPoint(endPointType) {
	if (endPointType == "src") {
		if (sourceMarker) {
            sourceMarker.circuitMode = "none";
            if(sourceMarker.circuitPoints == 0) {
                sourceMarker.setMap(null);
                markerCluster.addMarker(sourceMarker);
                sourceMarker = null;
            }
		} 
		
	} else if (destinMarker) {
        destinMarker.circuitMode = "none";

        if (destinMarker.circuitPoints == 0) {
            destinMarker.setMap(null);
            markerCluster.addMarker(destinMarker);
            destinMarker = null;
        }
    }
	
	drawCircuit();
}

//////////// INICIALIZA MAPA /////////////////

function fillSearchBox(results, status) {
  searchSource = [];

  var devs = MeicanMaps.searchMarkerByName(markers, 'dev', searchTerm);
  var size = devs.length;
  for (var i = 0; i < size; i++) {
      searchSource.push(
          {
              label: devs[i].name,
              type: 'dev',
              marker: devs[i]
          }
      );

      if(i == 10) return fillSearchSource(searchSource);
  };

  searchSource = $.ui.autocomplete.filter(searchSource, searchTerm);
  console.log(results, status);

  if (status == google.maps.places.PlacesServiceStatus.OK) {
    for (var i = 0; i < results.length; i++) {
      searchSource.push(
          {
              id: results[i].place_id,
              label: results[i].terms,
              value: results[i].description,
              type: 'place',
          }
      );
    }
  }
  
  fillSearchSource(searchSource);
}

function initialize() {
	var myLatlng = new google.maps.LatLng(0,0);
	var mapOptions = {
			zoom: 3,
			minZoom: 3,
			maxZoom: 15,
			center: myLatlng,
			streetViewControl: false,
			panControl: false,
			zoomControl: true,
      zoomControlOptions: {
          style: google.maps.ZoomControlStyle.LARGE,
          position: google.maps.ControlPosition.LEFT_CENTER
      },
			mapTypeControl: false,
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  map.set('styles', [
    {
      featureType: 'poi',
      stylers: [
        { visibility: 'off' }
      ]
    }
  ]);

  $( "#search-box" ).autocomplete({
    autoFocus: true,
    delay: 200,   
    select: function (event, ui) {
      console.log(ui.item);

      switch (ui.item.type) {
          case 'place':
              if(!placesService) placesService = new google.maps.places.PlacesService(map);

              var request = {
                placeId: ui.item.id
              };

              placesService.getDetails(request, function(place, status) {
                if (status == google.maps.places.PlacesServiceStatus.OK) {
                  var bounds = new google.maps.LatLngBounds();

                  bounds.extend(place.geometry.location); 

                  map.fitBounds(bounds);
                  map.setZoom(11);
                }
              });
              break;
          case 'dev':
              var bounds = new google.maps.LatLngBounds();

              bounds.extend(ui.item.marker.getPosition()); 

              map.fitBounds(bounds);
              map.setZoom(11);
              Manager.openWindow(markers, ui.item.marker);
      }
    }, 
    source: function (request, response) {
        searchTerm = request.term;

        if(!searchTerm.trim()) return;

        if (searchTerm && (searchTerm == previousTerm)) {
            previousTerm = searchTerm;
            response(searchSource);
            return;
        }

        var query = {
          input: request.term,
          //types: ['geocode']
        };

        if(!autoCompleteService) autoCompleteService = new google.maps.places.AutocompleteService();
        autoCompleteService.getPlacePredictions(query, fillSearchBox);

        previousTerm = searchTerm;
        fillSearchSource = response;
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
                    (item.label ? '<span style="font-size: 11px; color: #999"> from ' + getDomainName(item.marker.domainId) + "</span>" : ""))
                .appendTo( ul );
      }
  };

  $("#search-box").on("focus", function () {
      $(this).autocomplete("search", $("#res_name").val());
  });

	var markerClustererOptions = {
			gridSize: 10, 
			maxZoom: 10,
			ignoreHidden: true
		};
	
	markerCluster = new MarkerClusterer(
			map, 
			null, 
			markerClustererOptions
	);
	
	google.maps.event.addListener(map, 'click', function() {
		MeicanMaps.closeWindows();
     $("#name").focus();
	});
	
	initSelect("src", markers);
	initSelect("dst", markers);
  initWaypointSelect();
	
	$.ajax({
		url: baseUrl+'/topology/network/get-all-parent-location',
		dataType: 'json',
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude','domain_id']),
        },
		success: function(response){
			for(index = 0; index < response.length; index++){
				var network = response[index];
				addNetworkMarker(markers, network);
			}
			
			markerCluster.addMarkers(markers);
			
			setMarkerType(markers, "dev");
			$("#marker-type-device").attr("checked", "checked");
		},
		error: function (request, status, error) {
		    alert(request + error + status);
		 
		}
	});
	
	$('#marker-type-network').on('change', function() {
		setMarkerType(markers, "net");
	});
	
	$('#marker-type-device').on('change', function() {
		setMarkerType(markers, "dev");
	});
	
	enableWayPointsSortable(markers);
	initEndPointButtons("src", markers);
  initEndPointButtons('dst', markers);
}

//////////// ADICIONA MARCADORES NO MAPA /////////////////

function addNetworkMarker(markers, network) {
    var contentString = tt('Domain') + ': <b>' + getDomainName(network.domain_id) + '</b><br>' + tt('Network') + ': <b>'+network.name+'</b><br>';

    if (network.latitude != null && network.longitude != null) {
        var myLatlng = new google.maps.LatLng(network.latitude, network.longitude);
        
    } else {
        var myLatlng = new google.maps.LatLng(0, 0);
    }
    
    var marker = MeicanMaps.NetworkMarker({
        position: MeicanMaps.getValidMarkerPosition(markers, "net", myLatlng),
        type: "net",
        circuitPoints: 0,
        circuitMode: 'none',
        id: network.id,
        domainId: network.domain_id,
        info: contentString,
    });
    
    markers.push(marker);
    
    google.maps.event.addListener(marker, 'mouseover', function() {
        Manager.openWindow(markers, marker);
    });
}

function addDeviceMarker(markers, device) {
    if (device.name == "") device.name = "default";
  	var contentString = tt('Domain') + ': <b>'+getDomainName(device.domain_id)+'</b><br>' + tt('Device') + ': <b>' + device.name + '</b><br>';
  	
    var network = MeicanMaps.getMarkerByDomain(markers, 'net', device.domain_id);

  	if (device.latitude != null && device.longitude != null) {
  		  var myLatlng = new google.maps.LatLng(device.latitude, device.longitude);
  	} else if (network) {
        var myLatlng = network.position;
    } else {
  		  var myLatlng = new google.maps.LatLng(0, 0);
  	}
	
  	var marker = MeicanMaps.DeviceMarker({
    		position: MeicanMaps.getValidMarkerPosition(markers, "dev", myLatlng),
    		type: "dev",
    		circuitPoints: 0,
        circuitMode: "none",
    		id: device.id,
    		domainId: device.domain_id,
    		info: contentString,
        name: device.name
  	});
	
  	markers.push(marker);
  	
    google.maps.event.addListener(marker, 'mouseover', function() {
        Manager.openWindow(markers, marker);
    });
}

var Manager = new function() {
    this.openWindow = function(markers, marker) {
        MeicanMaps.closeWindows();
          
        var contentWindow;
        
        switch(marker.type) {
          case "net":
            switch(marker.circuitMode) {
              case "src":
              case "dst":
                  contentWindow = MARKER_OPTIONS_END_POINT;
                  break;
              case "intra":
                  contentWindow = MARKER_OPTIONS_INTRA;
                  break;
              case "none":
              default: 
                  contentWindow = MARKER_OPTIONS_NET;
            }
            break;
          case "dev":
          default: 
            switch(marker.circuitMode) {
              case "src":
              case "dst":
                  contentWindow = MARKER_OPTIONS_END_POINT;
                  break;
              case "intra":
                  contentWindow = MARKER_OPTIONS_INTRA;
                  break;
              case "none":
              default: 
                  contentWindow = MARKER_OPTIONS_DEV;
            }
        }

        var markerWindow = MeicanMaps.openWindow(map, marker, contentWindow);

        google.maps.event.addListener(markerWindow, 'domready', function(){
            $('#set-as-source').on('click', function() {
                MeicanMaps.closeWindows();
                
                setMarkerEndPoint("src", marker);
                if (marker && marker.type == "net") {
                    setNetworkSelected("src", marker);
                } else {
                    setDeviceSelected("src", marker);
                }
            });
            
            $('#add-waypoint').on('click', function() {
                MeicanMaps.closeWindows();
                
                addWayPoint(markers, marker);
            });
            
            $('#set-as-dest').on('click', function() {
                MeicanMaps.closeWindows();
              
                setMarkerEndPoint("dst", marker);
                if (marker && marker.type == "net") {
                    setNetworkSelected("dst", marker);
                } else {
                    setDeviceSelected("dst", marker);
                }
            });
            
            $('#set-as-intra').on('click', function() {
                MeicanMaps.closeWindows();
                
                deleteWayPoints();              
                
                setMarkerEndPoint("src", marker);
                if (marker && marker.type == "net") {
                    setNetworkSelected("src", marker);
                } else {
                    setDeviceSelected("src", marker);
                }

                setMarkerEndPoint("dst", marker);
                if (marker && marker.type == "net") {
                    setNetworkSelected("dst", marker);
                } else {
                    setDeviceSelected("dst", marker);
                }
            });
            
            $('#remove-waypoint').on('click', function() {
                MeicanMaps.closeWindows();
              
                deleteWayPoint(marker);
            });
            
            $('#remove-endpoint').on('click', function() {
                MeicanMaps.closeWindows();

                setNetworkSelected(marker.circuitMode);
                $('#' + marker.circuitMode + '-domain').val("");
                removeMarkerEndPoint(marker.circuitMode);
            });
            
            $('#remove-intra').on('click', function() {
                MeicanMaps.closeWindows();
              
                removeMarkerEndPoint("src");

                setNetworkSelected("src");
                setNetworkSelected("dst");
            });
        });
    }
}

/////////////// ALTERAR MARCADORES VISIVEIS /////////////////////

function setMarkerType(markers, markerType) {
    MeicanMaps.closeWindows();
    currentMarkerType = markerType;
    removeMarkerEndPoint("src");
    setNetworkSelected("src");
    setNetworkSelected("dst");
    fillDomainSelect("src");
    removeMarkerEndPoint("dst");
    fillDomainSelect("dst");
    MeicanMaps.setMarkerTypeVisible(markers, markerType);
      markerCluster.repaint();
    if (markerType == "dev") {
    	if (!devicesLoaded) {
    		loadDeviceMarkers(markers);
    		devicesLoaded = true;
    	} 
    }
    deleteWayPoints();
}

////////////// ADICIONA MARCADORES DE DISPOSITIVOS ////////////////

function loadDeviceMarkers(markers) {
	$.ajax({
		url: baseUrl+'/topology/device/get-all',
		dataType: 'json',
    data: {
      cols: JSON.stringify(['id','name','latitude','longitude','domain_id']),
    },
		success: function(response){
			var deviceMarkers = [];
			
			for(index = 0; index < response.length; index++){
				var device = response[index];
				addDeviceMarker(markers, device);
			}
			
			markerCluster.clearMarkers();
			markerCluster.addMarkers(markers);
		},
		error: function (request, status, error) {
		    alert(request + error + status);
		}
	});	
}

////////// INICIALIZA SELECT LISTENERS //////////////////

function initSelect(endPointType, markers) {
  	fillDomainSelect(endPointType);
  	
  	$('#' + endPointType + '-domain').on('change', function() {
    		removeMarkerEndPoint(endPointType);
    		fillNetworkSelect(endPointType, this.value);
    		fillDeviceSelect(endPointType, this.value);
    		fillPortSelect(endPointType);
    		fillVlanSelect(endPointType);
  	});
  	
  	$('#' + endPointType + '-network').on('change', function() {
    		if (currentMarkerType == "net") {
    			var marker = MeicanMaps.getMarker(markers, 'net',this.value);
    			
    			setMarkerEndPoint(endPointType, marker);
    		}
    		
    		fillDeviceSelect(endPointType, $('#' + endPointType + '-domain').val(), this.value);
    		fillPortSelect(endPointType);
    		fillVlanSelect(endPointType);
  	});
  	
  	$('#' + endPointType + '-device').on('change', function() {
    		if (currentMarkerType == "dev") {
    			var marker = MeicanMaps.getMarker(markers, 'dev',this.value);

    			setMarkerEndPoint(endPointType, marker);
    		}
    		
    		fillPortSelect(endPointType, this.value);
    		fillVlanSelect(endPointType);
  	});
  	
  	$('#' + endPointType + '-port').on('change', function() {
    		if (this.value != "null") {
    			$('#' + endPointType + "-copy-urn").removeClass("ui-state-disabled");
    		}
    		fillVlanSelect(endPointType, this.value);
  	});
}

function initWaypointSelect() {
    var endPointType = "waypoint"
    fillDomainSelect(endPointType);
    
    $('#' + endPointType + '-domain').on('change', function() {
        fillNetworkSelect(endPointType, this.value);
        fillDeviceSelect(endPointType, this.value);
        fillPortSelect(endPointType);
        fillVlanSelect(endPointType);
    });
    
    $('#' + endPointType + '-network').on('change', function() {
        fillDeviceSelect(endPointType, $('#' + endPointType + '-domain').val(), this.value);
        fillPortSelect(endPointType);
        fillVlanSelect(endPointType);
    });
    
    $('#' + endPointType + '-device').on('change', function() {
        fillPortSelect(endPointType, this.value);
        fillVlanSelect(endPointType);
    });
    
    $('#' + endPointType + '-port').on('change', function() {
        fillVlanSelect(endPointType, this.value);
    });
}

function clearSelect(endPointType, object) {
	  $('#' + endPointType + '-' + object).children().remove();
}

function disableSelect(endPointType, object) {
  $('#' + endPointType + '-' + object).prop('disabled', true);
}

function enableSelect(endPointType, object) {
  	if ($('#' + endPointType + '-' + object).val() != null && $('#' + endPointType + '-' + object) != "null") {
  		$('#' + endPointType + '-' + object).prop('disabled', false);
  	}
}

function getDomainName(id) {
    if (!domainsList) domainsList = JSON.parse($("#domains-list").text());
    for (var i = 0; i < domainsList.length; i++) {
        if(domainsList[i].id == id)
        return domainsList[i].name;
    };
}

////////// DEFINE ZOOM E LIMITES DO MAPA A PARTIR DE UM CAMINHO ////////

function setMapBounds(path) {
    if (path.length < 2) return;
    polylineBounds = new google.maps.LatLngBounds();
    for (i = 0; i < path.length; i++) {
    	polylineBounds.extend(path[i]);
    }
    map.fitBounds(polylineBounds);
    map.setCenter(polylineBounds.getCenter());
}

////////// GERA COR A PARTIR DE ID /////////////////////////////////////

google.maps.event.addDomListener(window, 'load', initialize);