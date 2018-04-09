/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var refresher;
var modalMode;

$(document).on('ready pjax:success', function() {
	$("#test-grid").on("click",'span.fa-pencil',  function() {
		console.log('edit');
		edit(this);
	});
});

$(document).ready(function() {
	prepareRefreshButton();
	
	$(".add-grid-btn").click(function() {
        console.log('dsds');
		openCreateModal();
		return false;
	});	

	domains = JSON.parse($("#domains").text());

	initEndPointSelects("testform-src_", domains);
    initEndPointSelects("testform-dst_", domains);

    $("#test-modal").on('click', '.confirm-btn', function() {
        if(modalMode == 'create') {
            create();
        } else { 
            save(this);
        }
    });

    $("#test-modal").on('click', '.close-btn', function() {
        $("#test-modal").modal('hide'); 
    });

	$('#cron-widget').cron({
        initial: "0 12 * * *",
        onChange: function() {
            $("#cron-value").val($(this).cron("value"));
        },
    });

    if($("#tester-mode").attr("value") == "create") openCreateModal();
});

function openCreateModal() {
	prepareCreate();
    modalMode = 'create';
    $("#test-modal").modal('show');
}

function prepareCreate() {
	//$( "#tabs" ).tabs( { disabled: [] } );
	//$('#tabs').tabs("option", "active", 0);
	domains = JSON.parse($("#domains").text());
	enableSelect("testform-src_", 'dom');
	enableSelect("testform-dst_", 'dom');
	$("#testform-src_dom").val("");
	$("#testform-dst_dom").val("");
	fillNetworkSelect("testform-src_");
	fillPortSelect("testform-src_");
	fillVlanSelect("testform-src_");
	fillNetworkSelect("testform-dst_");
	fillPortSelect("testform-dst_");
	fillVlanSelect("testform-dst_");
}

function submitDeleteForm() {
	$("#delete-test-form").submit();
}

function edit(row) {
    modalMode = 'update';
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
		if ($("#refresh-button").val() == "true") {
			disableAutoRefresh();
		} else {
            enableAutoRefresh();
		}
		return false;
	});
}

function disableAutoRefresh() {
	$("#refresh-button").val('false');
	clearInterval(refresher);
	$("#refresh-button").text(I18N.t("Enable auto refresh"));
}

function enableAutoRefresh() {
	$("#refresh-button").attr("disabled", false);
	updateGridView();
	$("#refresh-button").val('true');
	refresher = setInterval(updateGridView, 60000);
	$("#refresh-button").text(I18N.t("Disable auto refresh"));
}

function updateGridView() {
	$.pjax.defaults.timeout = false;
	$.pjax.reload({
		container:'#test-pjax'
	});
}

function create() {
	validateForm();

    setTimeout(function() {
        if($("#test-modal").find(".has-error").length > 0) {
            console.log("tem erro");
            MAlert.show(I18N.t("Invalid request."), I18N.t("Please, check your input and try again."), 'danger');
            return;
        }
		$.ajax({
	        type: "POST",
	        url: baseUrl + '/tester/manager/create',
	        data: $("#test-form").serialize(),
	        success: function (response) {
	        	window.location.href = baseUrl + "/tester/manager";
	        },
	    });
	}, 200);
}

function validateForm() {
	validateInput("src_dom");
    validateInput("src_dev");
    validateInput("src_port");
    validateInput("src_vlan");
    validateInput("dst_dom");
    validateInput("dst_dev");
    validateInput("dst_port");
    validateInput("dst_vlan");
}

function validateInput(elementId) {
	$("#test-form").yiiActiveForm("validateAttribute", 'testform-' + elementId);
}

function save(row) {
	rowId = $(row).parent().parent().parent().attr('data-key');
	$.ajax({
        type: "POST",
        url: baseUrl + '/tester/manager/update?id=' + rowId,
        data: $("#test-details-form").serialize(),
        success: function (response) {
        	window.location.href = baseUrl + "/tester/manager";
        },
    });
}

function fillDomainSelect(endPointType, domains, domainId, initDisabled) {
	disableSelect(endPointType, "dom");
	$("#"+ endPointType + "dom").append('<option value="">' + I18N.t('select') + '</option>');
	for (var i = 0; i < domains.length; i++) {
		$("#"+ endPointType + "dom").append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
	}
	if (domainId != null) {
		$("#"+ endPointType + "dom").val(domainId);
	}
	if(!initDisabled) enableSelect(endPointType, "dom");
}

function fillNetworkSelect(endPointType, domainId, networkId, initDisabled) {
    disableSelect(endPointType, "net");
	clearSelect(endPointType, "net");
	if (domainId != "" && domainId != null) {
		$("#"+ endPointType + "net").append('<option value="">' + I18N.t('loading') + '</option>');
		$.ajax({
			url: baseUrl+'/topology/network/get-by-domain',
			data: {
				id: domainId,
			},
			dataType: 'json',
			success: function(response){
				clearSelect(endPointType, "net");
				$("#"+ endPointType + "net").append('<option value="">' + I18N.t('select') + '</option>');
				for (var i = 0; i < response.length; i++) {
					$("#"+ endPointType + "net").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
			    }
				if (networkId != null) {
					$("#"+ endPointType + "net").val(networkId);
				}
				if (!initDisabled) enableSelect(endPointType, "net");
			}
		});
	} 
}

function fillPortSelect(endPointType, networkId, portId, initDisabled) {
    disableSelect(endPointType, "port");
	clearSelect(endPointType, "port");
	if (networkId != "" && networkId != null) {
		$("#"+ endPointType + "port").append('<option value="">' + I18N.t('loading') + '</option>');
		$.ajax({
			url: baseUrl+'/topology/port/get-by-network',
			dataType: 'json',
			data: {
				id: networkId,
				cols: JSON.stringify(['id','name']),
			},
			success: function(response){
				clearSelect(endPointType, "port");
				$("#"+ endPointType + "port").append('<option value="">' + I18N.t('select') + '</option>');
				for (var i = 0; i < response.length; i++) {
					$("#"+ endPointType + "port").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
			    }
                if (portId != null && portId != "") $("#"+ endPointType + "port").val(portId);
                if (!initDisabled) enableSelect(endPointType, "port");
			}
		});
	} 
}

function fillVlanSelect(endPointType, portId, vlan, initDisabled) {
    disableSelect(endPointType, "vlan");
	clearSelect(endPointType, "vlan");
	if (portId != "" && portId != null) {
		$("#"+ endPointType + "vlan").append('<option value="">' + I18N.t('loading') + '</option>');
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
                            $("#"+ endPointType + "vlan").append('<option value="' + ranges[i] + '">' + ranges[i] + '</option>');
    			    }

                    for (var i = 0; i < ranges.length; i++) {
                        var interval = response.split("-");
                        var low = parseInt(interval[0]);
                        var high = low;
                        if (interval.length > 1) {
                            high = parseInt(interval[1]);
                        }
                        
                        for (var j = low; j < high+1; j++) {
                            $("#"+ endPointType + "vlan").append('<option value="' + j + '">' + j + '</option>');
                        }
                        if (vlan != null && vlan != "") {
                            $("#"+ endPointType + "vlan").val(vlan);
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
	
	$('#' + endPointType + 'dom').on('change', function() {
		fillNetworkSelect(endPointType, this.value);
		fillPortSelect(endPointType);
		fillVlanSelect(endPointType);
	});
	
	$('#' + endPointType + 'net').on('change', function() {
		fillPortSelect(endPointType, this.value);
	});

	$('#' + endPointType + 'port').on('change', function() {
		fillVlanSelect(endPointType, this.value);
	});
}

function clearSelect(endPointType, object) {
	$('#' + endPointType + object).children().remove();
}

function disableSelect(endPointType, object) {
	$('#' + endPointType + object).prop('disabled', true);
}

function enableSelect(endPointType, object) {
	$('#' + endPointType + object).prop('disabled', false);
}

