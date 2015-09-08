var refresher;

$(document).on('ready pjax:success', function() {
	$('#test-grid').on("click", '.deleteCheckbox', function() {
		disableAutoRefresh();
		deleteButtonSwitch();
	});
	
	$("#test-grid").on("click",'img.edit-button',  function() {
		edit(this);

		var rowNode = this;
		$('#test-dialog').dialog({
			title: "Update",
			width: 360,
			height: 300,
			modal: true,
			buttons: [{
				text: tt("Save"),
				click: function() {
					save(rowNode);
		        }},
		        {
		        text: tt("Cancel"),
		        click: function() {
		          	$(this).dialog('close');
		        }
		    }],
		});
	});
});

$(document).ready(function() {
	prepareRefreshButton();
	
	$("#add-button").click(function() {
		openCreateDialog();
		return false;
	});	

	domains = JSON.parse($("#domains").text());

	initEndPointSelects("src", domains);
    initEndPointSelects("dst", domains);

	$("#tabs").tabs();
	$('#cron-widget').cron({
        initial: "0 12 * * *",
        onChange: function() {
            $("#cron-value").val($(this).cron("value"));
        },
    });

    if($("#at-mode").attr("value") == "create") openCreateDialog();
});

function openCreateDialog() {
	prepareCreate();
	$('#test-dialog').dialog({
		title: "Create",
		width: 360,
		height: 300,
		modal: true,
		buttons: [{
			text: tt("Save"),
			click: function() {
				create();
	        }},
	        {
	        text: tt("Cancel"),
	        click: function() {
	          	$(this).dialog('close');
	        }
	    }],
	});
}

function prepareCreate() {
	$( "#tabs" ).tabs( { disabled: [] } );
	$('#tabs').tabs("option", "active", 0);
	domains = JSON.parse($("#domains").text());
	enableSelect("src", 'domain');
	enableSelect("dst", 'domain');
	$("#src-domain").val("");
	$("#dst-domain").val("");
	fillDeviceSelect("src");
	fillNetworkSelect("src");
	fillPortSelect("src");
	fillVlanSelect("src");
	fillDeviceSelect("dst");
	fillNetworkSelect("dst");
	fillPortSelect("dst");
	fillVlanSelect("dst");
}

function submitDeleteForm() {
	deleteChecked();
}

function clearCheckbox() {
	$("#test-grid :checked").removeAttr('checked');
	$('#delete-button').hide();
}

function edit(row) {
	$( "#tabs" ).tabs( { disabled: [0, 1] } );
	$('#tabs').tabs("option", "active", 2);
	rowId = $(row).parent().parent().parent().attr('data-key');
	
	$("#cron-widget").cron("value", getData("cron-value", rowId));
}

function getData(object, rowId) {
	return $('#test-grid tr[data-key="' + rowId + '"] td.' + object)[0].getAttribute('data');
}

function prepareRefreshButton() {
	$("#refresh-button").click(function(){
		if ($("#refresh-button").val() == "false") {
			enableAutoRefresh();
		} else {
			disableAutoRefresh();
		}
		return false;
	});
}

function disableAutoRefresh(disableButton) {
	if (disableButton) 
		$("#refresh-button").attr("disabled", "disabled");

	$("#refresh-button").val('false');
	clearInterval(refresher);
	$("#refresh-button").text(tt("Enable auto refresh"));
}

function enableAutoRefresh() {
	$("#deleteButton").hide();
	
	$("#refresh-button").attr("disabled", false);
	updateGridView();
	$("#refresh-button").val('true');
	refresher = setInterval(updateGridView, 60000);
	$("#refresh-button").text(tt("Disable auto refresh"));
}

function updateGridView() {
	$.pjax.defaults.timeout = false;
	$.pjax.reload({
		container:'#test-pjax'
	});
}

function create() {
	if(validateForm()) {
		$.ajax({
	        type: "POST",
	        url: baseUrl + '/circuits/automated-test/create',
	        data: $("#test-details-form").serialize(),
	        success: function (response) {
	        	window.location.href = baseUrl + "/circuits/automated-test";
	        },
	    });
	} 
}

function validateForm() {
	return validateInput("src-domain") &&
		validateInput("src-device") &&
		validateInput("src-port") &&
		validateInput("src-vlan") &&
		validateInput("dst-domain") &&
		validateInput("dst-device") &&
		validateInput("dst-port") &&
		validateInput("dst-vlan");
}

function validateInput(elementId) {
	if (!$("#" + elementId).val() || $("#" + elementId).val() == "") {
		$("#" + elementId).animate({backgroundColor: "#CC0000"});
		$("#" + elementId).animate({backgroundColor: "blank"});
		$('#tabs').tabs("option", "active", $("#" + elementId).parent().parent().parent().attr("id").split('-')[1]);
		return false;
	} 
	return true;
}

function save(row) {
	rowId = $(row).parent().parent().parent().attr('data-key');
	$.ajax({
        type: "POST",
        url: baseUrl + '/circuits/automated-test/update?id=' + rowId,
        data: $("#test-details-form").serialize(),
        success: function (response) {
        	window.location.href = baseUrl + "/circuits/automated-test";
        },
    });
}

function deleteChecked() {
	var item = document.getElementById("test-grid");
	var keys = $(item).yiiGridView('getSelectedRows');
	
	$.ajax({
		type: "POST",
		url: baseUrl+'/circuits/automated-test/delete',
		dataType: 'json',
		data: {
			ids: JSON.stringify(keys),
		},
		success: function (response) {
        },
	});
}

function fillDomainSelect(endPointType, domains, domainId, initDisabled) {
	disableSelect(endPointType, "domain");
	$("#"+ endPointType + "-domain").append('<option value="">' + tt('select') + '</option>');
	for (var i = 0; i < domains.length; i++) {
		$("#"+ endPointType + "-domain").append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
	}
	if (domainId != null) {
		$("#"+ endPointType + "-domain").val(domainId);
	}
	if(!initDisabled) enableSelect(endPointType, "domain");
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
				for (var i = 0; i < response.length; i++) {
					$("#"+ endPointType + "-network").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
			    }
				if (networkId != null) {
					$("#"+ endPointType + "-network").val(networkId);
				}
				if (!initDisabled) enableSelect(endPointType, "network");
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
                for (var i = 0; i < response.length; i++) {
                    if (response[i].name == "") response[i].name = "default";
                    $("#"+ endPointType + "-device").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                }
                if (deviceId != null && deviceId != "") {
                    $("#"+ endPointType + "-device").val(deviceId);
                }
                if (!initDisabled) enableSelect(endPointType, "device");
            }
        });
    } 
}

function fillPortSelect(endPointType, deviceId, portId, initDisabled) {
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
				for (var i = 0; i < response.length; i++) {
					var name = response[i].name;
					if (response[i].port == "") {
						name = tt("default");
					}
					$("#"+ endPointType + "-port").append('<option value="' + response[i].id + '">' + name + '</option>');
			    }
                if (portId != null && portId != "") $("#"+ endPointType + "-port").val(portId);
                if (!initDisabled) enableSelect(endPointType, "port");
			}
		});
	} 
}

function fillVlanSelect(endPointType, portId, vlan, initDisabled) {
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
    				if (!initDisabled) enableSelect(endPointType, "vlan");
                }
			}
		});
	}
}

function initEndPointSelects(endPointType, domains) {
	fillDomainSelect(endPointType, domains);
	
	$('#' + endPointType + '-domain').on('change', function() {
		fillDeviceSelect(endPointType, this.value);
		fillNetworkSelect(endPointType, this.value);
		fillPortSelect(endPointType);
		fillVlanSelect(endPointType);
	});
	
	$('#' + endPointType + '-device').on('change', function() {
		fillPortSelect(endPointType, this.value);
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
	$('#' + endPointType + '-' + object).prop('disabled', false);
}

