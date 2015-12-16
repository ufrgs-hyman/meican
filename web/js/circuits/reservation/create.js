$(document).ready(function() {
    prepareConfirmDialog();
    prepareBandwidthSpinner();
    
    $(".hourPicker").timepicker({
		timeFormat: "H:i",
        step: 30,
	});
});

var meicanMap;
var sourceMarker;
var destinMarker;
var wayPoints = [];
var markerCluster;
var circuit;
var devicesLoaded = false;

var MARKER_OPTIONS_NET = '' +
'<div><button style="font-size: 10px; height: 22px; width: 48.25%;" id="set-as-source">' + tt('From here') + '</button>' +
'<button style="font-size: 10px; height: 22px; width: 48.25%;" id="set-as-dest">' + tt('To here') + '</button></div><div style="height: 2px;"></div>' +
'<div><button style="font-size: 10px; height: 22px; width: 48.25%;" id="add-waypoint">' + tt('Add waypoint') + '</button>' +
'<button style="font-size: 10px; height: 22px; width: 48.25%;" id="set-as-intra">' + tt('Intra-domain') + '</button></div>';
var MARKER_OPTIONS_DEV = '' +
'<div><button style="font-size: 10px; height: 22px; width: 48.25%;" id="set-as-source">' + tt('From here') + '</button>' +
'<button style="font-size: 10px; height: 22px; width: 48.25%;" id="set-as-dest">' + tt('To here') + '</button></div><div style="height: 2px;"></div>' +
'<div><button style="font-size: 10px; height: 22px; width: 98%;" id="add-waypoint">' + tt('Add waypoint') + '</button>' +
'</div>';
var MARKER_OPTIONS_END_POINT = '' +
'<div><button style="font-size: 10px; height: 22px; width: 98%;" id="remove-endpoint">' + tt('Remove endpoint') + '</button></div><div style="height: 2px;"></div>' +
'<div><button style="font-size: 10px; height: 22px; width: 98%;" id="add-waypoint">' + tt('Add waypoint') + '</button></div>';
var MARKER_OPTIONS_INTRA = '' +
'<button style="font-size: 10px; height: 22px; width: 98%;" id="remove-intra">' + tt('Remove intra-domain circuit') + '</button>';


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
	if ($("#bandwidth").val().length < 1 || isNaN($("#bandwidth").val()) || parseInt($("#bandwidth").val()) < 1) {
		errors += '<br>- ' + tt('The bandwidth is required.');
		isValid = false;
	}
	if (!($("#name").val().trim())) {
	    errors += '<br>- ' + tt('A reservation name is required.');
	    isValid = false;
	}
	if ($("#gri").val() && $("#gri").val().length > 30) {
		errors += '<br>- ' + tt('The GRI cannot be longer than 30 characters.');
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
	if(max_bandwidth) document.getElementById('bandwidth_bar_inside').style.display = 'block';
	
	var f = function() {
        var v = ($("#bandwidth").val() / $("#bandwidth").attr('aria-valuemax')) * 100;
        if (v > 100 || v < 0)
            return;
        var k = 2 * (50 - v);
        $('#bandwidth_bar_inside').width(v + '%');
    };

    $('#bandwidth').attr("min", 100).attr("max", max_bandwidth).attr("step", 100).
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
    $('#bandwidth').spinner({
        min: 100,
        max: max_bandwidth,
        step: 100
    }).spinner("enable").disabled(false).trigger('click');
    $("#bandwidth").trigger("change");
}

/////////////// botoes superiores da tabela origem destino /////////

function initEndPointButtons(endPointType) {
    $('#' + endPointType + '-clear-endpoint').click(function() {
		removeMarkerEndPoint(endPointType);
        setNetworkSelected(endPointType);
        $('#' + endPointType + '-domain').val("");
    });
	
    $('#' + endPointType + '-select-current-host').click(function() {
      	var currentHostMarker = findCurrentHostMarker();
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

function findCurrentHostMarker() {
  	var currentHost = document.URL;
  	currentHost = currentHost.split("/")[2].split(".");
  	
  	if (meicanMap.getCurrentMarkerType() == "net") {
  		for (var i = 0; i < meicanMap.getMarkers().length; i++) {
  	    	if (meicanMap.getMarkers()[i].type == "net") {
  	    		var domain = meicanMap.getMarkers()[i].name; /////// ARRRUMA
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
  	    		if (equal) return meicanMap.getMarkers()[i];
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

function enableWayPointsSortable() {
	$("#waypoints_order").sortable({
        update: function(event, ui) {
            var sortableOrder = $(this).sortable('toArray');
            var newWayPoints = [];
        	for (var i = 0; i < sortableOrder.length; i++) {
            	newWayPoints[i] = meicanMap.getMarker(meicanMap.getCurrentMarkerType(), parseInt(sortableOrder[i].replace("way","")));
            }

            wayPoints = newWayPoints;
            
            drawCircuit();
        }

    }).css("display", "block");
}

function prepareDialogDeviceSelect(wayObject) {
    var object = meicanMap.getMarker(meicanMap.getCurrentMarkerType(), $(wayObject).attr("id").replace("way",""));
	
    $("#waypoint-domain").val($(wayObject).children(".domain-id").val());

    if (meicanMap.getCurrentMarkerType() == "net") {
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

function addWayPoint(marker) {
    marker.circuitPoints++;
	markerCluster.removeMarker(marker);
	marker.setMap(meicanMap.getMap());
	
	wayPoints.push(marker);
    $("#waypoints-size").text(wayPoints.length);

    var inputData = '';
    if (marker.type == "net") {
        inputData = '<input value="' + marker.id + '" type="text" class="network-id" hidden></input>' + 
             '<input type="text" class="device-id" hidden></input>';
    } else {
        inputData = '<input type="text" class="network-id" hidden></input>' + 
             '<input value="' + marker.id + '" type="text" class="device-id" hidden></input>';
    }
	
	$("#waypoints_order").append("<li class='ui-state-default opener' id='way" + 
    		 marker.id + "'>" + meicanMap.getDomain(marker.domainId).name + " (" + tt("click to fill waypoint") + ")" + 
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
            	prepareDialogDeviceSelect(content);
                disableTabSlide();
            },
            close: function(event, ui) {
                enableTabSlide();
            },
            buttons: [{
                text: tt('Remove'),
                click: function() {
                    deleteWayPoint(content);
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

function deleteWayPoint(wayObject) {
	var marker = meicanMap.getMarker(meicanMap.getCurrentMarkerType(), $(wayObject).attr("id").replace("way",""));
    marker.circuitPoints--;
	
	for (var i = 0; i < wayPoints.length; i++) {
		if (wayPoints[i] == marker) {
			wayPoints.splice(i, 1);
			break;
		}
	}

    $("#waypoints-size").text(wayPoints.length);
	
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
	
    $("#waypoints-size").text(wayPoints.length);
	
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
                        var interval = ranges[i].split("-");
                        var low = parseInt(interval[0]);
                        var high = low;
                        if (interval.length > 1) {
                            high = parseInt(interval[1]);
                            for (var j = low; j < high+1; j++) {
	                        $("#"+ endPointType + "-vlan").append('<option value="' + j + '">' + j + '</option>');
	                    }
                        } else {
                            $("#"+ endPointType + "-vlan").append('<option value="' + low + '">' + low + '</option>');
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
	
	for (var i = 0; i < wayPoints.length; i++) {
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
	    	circuit.setMap(meicanMap.getMap());
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
		marker.setMap(meicanMap.getMap());
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

function enableTabSlide() {
    $('#reservation-tab').hoverIntent(openTab, closeTab);
}

function closeTab() {
    $("#slide").slideUp(500, function(){
        $(window).trigger('resize');
    });
    $("#waypoints_order").slideUp(500, function(){
        $(window).trigger('resize');
    });
}

function openTab() {
    $("#slide").slideDown(500, function(){
        $(window).trigger('resize');
    });
    $("#waypoints_order").slideDown(500, function(){
        $(window).trigger('resize');
    });
}

function disableTabSlide() {
    $("#reservation-tab").unbind("mouseenter").unbind("mouseleave");
    $("#reservation-tab").removeProp('hoverIntent_t');
    $("#reservation-tab").removeProp('hoverIntent_s');
}

//////////// INICIALIZA MAPA /////////////////

function initialize() {
    meicanMap = new MeicanMap;
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        data: {
            cols: JSON.stringify(['id','name','color']),
        },
        success: function(response){
            meicanMap.setDomains(response);
            initAll();
        },
        error: function (request, status, error) {
            alert(error);
        }
    });
}

function initAll() {
    meicanMap.buildMap('map-canvas');

    meicanMap.buildSearchBox("search-row", "search-box", 'search-button', function(marker) {
        Manager.openWindow(marker);
    });

    meicanMap.buildMapTypeBox("map-type-box", 'map-type-select');
    meicanMap.buildMarkerTypeBox("marker-type-box", 'marker-type-select', setMarkerType);

    enableTabSlide();

    $("#reservation-tab").on("focusin", function () {
        openTab();
        disableTabSlide();
    });

    $("#reservation-tab").on("focusout", function () {
        enableTabSlide();
    });

    var markerClustererOptions = {
            gridSize: 10, 
            maxZoom: 10,
            ignoreHidden: true
        };
    
    markerCluster = new MarkerClusterer(
            meicanMap.getMap(), 
            null, 
            markerClustererOptions
    );
    
    google.maps.event.addListener(meicanMap.getMap(), 'click', function() {
        meicanMap.closeWindows();
        $("#src-domain").focus();

        closeTab();
    });

    initSelect("src");
    initSelect("dst");
    initWaypointSelect();
    
    $.ajax({
        url: baseUrl+'/topology/network/get-all',
        dataType: 'json',
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude','domain_id']),
        },
        success: function(response){
            for(var index = 0; index < response.length; index++){
                var network = response[index];
                addNetworkMarker(network);
            }
            
            markerCluster.addMarkers(meicanMap.getMarkers());
            
            setMarkerType("dev");
        },
        error: function (request, status, error) {
            alert(request + error + status);
        }
    });
    
    enableWayPointsSortable();
    initEndPointButtons("src");
    initEndPointButtons('dst');
}

//////////// ADICIONA MARCADORES NO MAPA /////////////////

function addNetworkMarker(network) {
    if (network.latitude != null && network.longitude != null) {
        var myLatlng = new google.maps.LatLng(network.latitude, network.longitude);
        
    } else {
        var myLatlng = new google.maps.LatLng(0, 0);
    }
    
    var marker = meicanMap.NetworkMarker({
        position: meicanMap.getValidMarkerPosition("net", myLatlng),
        type: "net",
        circuitPoints: 0,
        circuitMode: 'none',
        id: network.id,
        domainId: network.domain_id,
        name: network.name,
    });
    
    meicanMap.addMarker(marker);
    
    google.maps.event.addListener(marker, 'mouseover', function() {
        Manager.openWindow(marker);
    });
}

function addDeviceMarker(device) {
    if (device.name == "") device.name = "default";
  	
    var network = meicanMap.getMarkerByDomain('net', device.domain_id);

  	if (device.latitude != null && device.longitude != null) {
  		  var myLatlng = new google.maps.LatLng(device.latitude, device.longitude);
  	} else if (network) {
        var myLatlng = network.position;
    } else {
  		  var myLatlng = new google.maps.LatLng(0, 0);
  	}
	
  	var marker = meicanMap.DeviceMarker({
		position: meicanMap.getValidMarkerPosition("dev", myLatlng),
		type: "dev",
		circuitPoints: 0,
        circuitMode: "none",
		id: device.id,
		domainId: device.domain_id,
        name: device.name
  	});
	
  	meicanMap.addMarker(marker);
  	
    google.maps.event.addListener(marker, 'mouseover', function() {
        Manager.openWindow(marker);
    });
}

var Manager = new function() {
    this.openWindow = function(marker) {
        meicanMap.closeWindows();
          
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

        var markerWindow = meicanMap.openWindow(marker, contentWindow);

        google.maps.event.addListener(markerWindow, 'domready', function(){
            $('#set-as-source').on('click', function() {
                meicanMap.closeWindows();
                
                setMarkerEndPoint("src", marker);
                if (marker && marker.type == "net") {
                    setNetworkSelected("src", marker);
                } else {
                    setDeviceSelected("src", marker);
                }
            });
            
            $('#add-waypoint').on('click', function() {
                meicanMap.closeWindows();
                
                addWayPoint(marker);
            });
            
            $('#set-as-dest').on('click', function() {
                meicanMap.closeWindows();
              
                setMarkerEndPoint("dst", marker);
                if (marker && marker.type == "net") {
                    setNetworkSelected("dst", marker);
                } else {
                    setDeviceSelected("dst", marker);
                }
            });
            
            $('#set-as-intra').on('click', function() {
                meicanMap.closeWindows();
                
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
                meicanMap.closeWindows();
              
                deleteWayPoint(marker);
            });
            
            $('#remove-endpoint').on('click', function() {
                meicanMap.closeWindows();

                setNetworkSelected(marker.circuitMode);
                $('#' + marker.circuitMode + '-domain').val("");
                removeMarkerEndPoint(marker.circuitMode);
            });
            
            $('#remove-intra').on('click', function() {
                meicanMap.closeWindows();
              
                removeMarkerEndPoint("src");

                setNetworkSelected("src");
                setNetworkSelected("dst");
            });
        });
    }
}

/////////////// ALTERAR MARCADORES VISIVEIS /////////////////////

function setMarkerType(markerType) {
    meicanMap.closeWindows();
    removeMarkerEndPoint("src");
    setNetworkSelected("src");
    setNetworkSelected("dst");
    fillDomainSelect("src");
    removeMarkerEndPoint("dst");
    fillDomainSelect("dst");
    meicanMap.setMarkerTypeVisible(markerType);
    markerCluster.repaint();
    if (markerType == "dev") {
    	if (!devicesLoaded) {
    		loadDeviceMarkers();
    		devicesLoaded = true;
    	} 
    }
    deleteWayPoints();
}

////////////// ADICIONA MARCADORES DE DISPOSITIVOS ////////////////

function loadDeviceMarkers() {
	$.ajax({
		url: baseUrl+'/topology/device/get-all',
		dataType: 'json',
    data: {
      cols: JSON.stringify(['id','name','latitude','longitude','domain_id']),
    },
		success: function(response){
			var deviceMarkers = [];
			
			for(var index = 0; index < response.length; index++){
				var device = response[index];
				addDeviceMarker(device);
			}
			
			markerCluster.clearMarkers();
			markerCluster.addMarkers(meicanMap.getMarkers());
		},
		error: function (request, status, error) {
		    alert(request + error + status);
		}
	});	
}

////////// INICIALIZA SELECT LISTENERS //////////////////

function initSelect(endPointType) {
  	fillDomainSelect(endPointType);
  	
  	$('#' + endPointType + '-domain').on('change', function() {
		removeMarkerEndPoint(endPointType);
		fillNetworkSelect(endPointType, this.value);
		fillDeviceSelect(endPointType, this.value);
		fillPortSelect(endPointType);
		fillVlanSelect(endPointType);
  	});
  	
  	$('#' + endPointType + '-network').on('change', function() {
		if (meicanMap.getCurrentMarkerType() == "net") {
			var marker = meicanMap.getMarker("net",this.value);
			
			setMarkerEndPoint(endPointType, marker);
		}
		
		fillDeviceSelect(endPointType, $('#' + endPointType + '-domain').val(), this.value);
		fillPortSelect(endPointType);
		fillVlanSelect(endPointType);
  	});
  	
  	$('#' + endPointType + '-device').on('change', function() {
		if (meicanMap.getCurrentMarkerType() == "dev") {
			var marker = meicanMap.getMarker('dev',this.value);

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

////////// DEFINE ZOOM E LIMITES DO MAPA A PARTIR DE UM CAMINHO ////////

function setMapBounds(path) {
    if (path.length < 2) return;
    polylineBounds = new google.maps.LatLngBounds();
    for (var i = 0; i < path.length; i++) {
    	polylineBounds.extend(path[i]);
    }
    meicanMap.getMap().fitBounds(polylineBounds);
    meicanMap.getMap().setCenter(polylineBounds.getCenter());
}

////////// GERA COR A PARTIR DE ID /////////////////////////////////////

google.maps.event.addDomListener(window, 'load', initialize);
