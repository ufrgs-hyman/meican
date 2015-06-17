var refresher;

$(document).on('ready pjax:success', function() {
	$('#test-grid').on("click", '.deleteCheckbox', function() {
		disableAutoRefresh();
		deleteButtonSwitch();
	});
	
	$("#test-grid").on("click",'img.edit-button',  function() {
		if ($(this).attr("disabled") != "disabled") { 
			edit(this);
			clearCheckbox();
		}
	});
});

$(document).ready(function() {
	prepareRefreshButton();
	
	$("#add-button").click(function() {
		add();
		$("#test-grid img.edit-button").attr("disabled", "disabled");
	});	
});

function submitDeleteForm() {
	deleteChecked();
	enableMainButtons();
}

function clearCheckbox() {
	$("#test-grid :checked").removeAttr('checked');
	$('#delete-button').hide();
}

function add() {
	disableMainButtons();
	
	var columns = '<tr data-key="new">';
    
	columns += '<td><a href="#"><img onclick="create()" src="'+baseUrl+'/images/ok.png"/></a></td>';
	columns += '<td><img onclick="cancelAdd()" alt="clear" border="0" id="delete" src="'+baseUrl+'/images/clear.png"/></td>';
    columns += '<td>' + tt("New") + '</td>';
    columns += '<td>' + tt("None") + '</td>';
    columns += '<td>' + tt("Never") + '</td>';
    
    columns += '<td><select id="src-domain-new"></select></td>';
    
    columns += '<td><select disabled id="src-device-new"/></td>';
    
    columns += '<td><select name="AutomatedTestForm[src_port]" disabled id="src-port-new"/></td>';
    
    columns += '<td><select id="dst-domain-new"></select></td>';
    columns += '<td><select disabled id="dst-device-new"/></td>';
    columns += '<td><select name="AutomatedTestForm[dst_port]" disabled id="dst-port-new"/></td>';
    columns += '<td><select name="AutomatedTestForm[provider]" disabled id="provider-select-new"/></td>';
    columns += '<td><select class="freq-type" name="AutomatedTestForm[freq_type]" id="freq-type-new"></select></td>';
    columns += '<td><input type="text" value="" name="AutomatedTestForm[freq_value]" id="freq-value-new" hidden></input></td>';
    columns += '<td></td></tr>';
    
    if($("#test-grid tr[data-key]").length == 0) {
    	$("#test-grid tbody tr").hide();
    }
    $('#test-grid tbody').append(columns);
    domains = JSON.parse($("#domains").text());
    initEndPointSelects("src", domains, "new");
    initEndPointSelects("dst", domains, "new");
    initFrequencySelect("new");
}

function cancelAdd() {
	enableMainButtons();
	$("#test-grid img.edit-button").attr("disabled", false);
	
	$('#test-grid tr[data-key=' + '"new"]').remove();
	if($("#test-grid tr[data-key]").length == 0) {
    	$("#test-grid tbody tr").show();
    }
}

function cancelEdit(row) {
	enableMainButtons();
	$("#test-grid img.edit-button").attr("disabled", false);
	
	rowId = $(row).parent().parent().parent().attr('data-key');
	oldRow = $("#original-" + rowId).html();
	$(row).parent().parent().parent().replaceWith(oldRow);
}

function enableMainButtons() {
	$("#refresh-button").attr("disabled", false);
	$("#add-button").attr("disabled", false);
}

function disableMainButtons() {
	disableAutoRefresh(true);
	$("#add-button").attr("disabled", "disabled");
}

function edit(row) {
	disableMainButtons();
	$("#test-grid img.edit-button").attr("disabled", "disabled");
	
	rowId = $(row).parent().parent().parent().attr('data-key');
	rowChilds = $(row).parent().parent().parent().children();
	
	domains = JSON.parse($("#domains").text());
	$(rowChilds[14]).html('<label id="original-' + rowId + '" hidden></label>');
	$(row).parent().parent().parent().clone().appendTo( "#original-" + rowId );
	$(rowChilds[0]).html('<a href="#"><img onclick="save(this)" src="'+baseUrl+'/images/ok.png"/></a>');
	$(rowChilds[1]).html('<a href="#"><img onclick="cancelEdit(this)" src="'+baseUrl+'/images/clear.png"/></a>');
	$(rowChilds[12]).html('<select class="freq-type" name="AutomatedTestForm[freq_type]" id="freq-type-' + rowId + '"></select></td>');
	$(rowChilds[13]).html('<input type="text" name="AutomatedTestForm[freq_value]" value="' + getData('freq-value', rowId) + '" id="freq-value-' + rowId + '" hidden></input>');
	
    initEndPointSelects("src", domains, rowId);
    initEndPointSelects("dst", domains, rowId);
    fillDeviceSelect("src", getData("src-domain", rowId), rowId, true);
	fillPortSelect("src", getData("src-device", rowId), rowId, true);
	fillDeviceSelect("dst", getData("dst-domain", rowId), rowId, true);
	fillPortSelect("dst", getData("dst-device", rowId), rowId, true);
	fillProviderSelect(getData('src-domain', rowId), getData('dst-domain', rowId), rowId, true);
    initFrequencySelect(rowId, true);
}

function prepareRefreshButton() {
	refresher = setInterval(updateGridView, 5000);
	
	$("#refresh-button").click(function(){
		if ($("#refresh-button").val() == "false") {
			enableAutoRefresh();
		} else {
			disableAutoRefresh();
		}
	});
}

function disableAutoRefresh(disableButton) {
	$("#loader-img").hide();
	if (disableButton) 
		$("#refresh-button").attr("disabled", "disabled");

	$("#refresh-button").val('false');
	clearInterval(refresher);
	$("#refresh-button").text(tt("Enable auto refresh"));
}

function enableAutoRefresh() {
	$("#deleteButton").hide();
	$("#loader-img").show();
	
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
	//Verify if all the mandatory fields are completed. If they're ed, the informations will be saved. 
	//The mandatory fields are: Source -> Domain and device, Destin -> Domain and device and
	//Frequency -> Type and String
	if(createValidate()) {
		$.ajax({
	        type: "POST",
	        url: baseUrl + '/circuits/automated-test/create',
	        data: $("#automated-test-form").serialize(),
	        success: function (response) {
	        	enableAutoRefresh();
	        	$("#add-button").attr("disabled", false);
	        },
	    });
	} else {
		alert("Invalid input");
	}
}

function createValidate() {
	console.log($("#src-domain-new").val(), 
			$("#src-device-new").val(),
			$("#src-port-new").val(),
			$("#dst-domain-new").val(),
			$("#dst-device-new").val(),
			$("#dst-port-new").val(),
			$("#provider-select-new").val(),
			$("#freq-value-new").val(),
			$("#freq-type-new").val());
	if ($("#src-domain-new").val() == "null" || 
			$("#src-device-new").val() == "null" ||
			$("#src-port-new").val() == "null" ||
			$("#dst-domain-new").val() == "null" || 
			$("#dst-device-new").val() == "null" ||
			$("#dst-port-new").val() == "null" ||
			$("#provider-select-new").val() == "null" ||
			$("#freq-value-new").val() == "" ||
			$("#freq-type-new").val() == "null") {
		return false;
	}
	return true;
}

function save(row) {
	rowId = $(row).parent().parent().parent().attr('data-key');
	if (updateValidate(rowId)) {
		$.ajax({
	        type: "POST",
	        url: baseUrl + '/circuits/automated-test/update?id=' + rowId,
	        data: $("#automated-test-form").serialize(),
	        success: function (response) {
	        	enableAutoRefresh();
	        	$("#add-button").attr("disabled", false);
	        },
	    });
	} else {
		alert("Invalid input");
	}
}

function updateValidate(row) {
	if ($("#freq-value-" + rowId).val() == "" || 
			$("#freq-type-" + rowId).val() == "null") return false;
	
	return true;
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
        	enableAutoRefresh();
        },
	});
}

function initFrequencySelect(rowId, currentEnabled) {
	$("#freq-type-" + rowId).append('<option value="null">' + tt("select") + '</option>');
	
	$("#freq-type-" + rowId).append('<option value="daily">' + $("#daily-label").text() + '</option>');
	$("#freq-type-" + rowId).append('<option value="weekly">' + $("#weekly-label").text() + '</option>');
	$("#freq-type-" + rowId).append('<option value="monthly">' + $("#monthly-label").text() + '</option>');
	if (currentEnabled != null) {
		$("#freq-type-" + rowId).val(getData("freq-type", rowId).toLowerCase());
	}
	
	$(".hourPicker").timepicker({
		timeFormat: "H:i",
        step: 30,
	});
	
	$("#freq-type-" + rowId).on('change', function() {
		initFrequencyDialog(this.value, rowId);
	});
}

function getData(object, rowId) {
	return $('#test-grid tr[data-key="' + rowId + '"] td.' + object)[0].getAttribute('data');
}

function fillProviderSelect(srcDomainId, dstDomainId, rowId, currentEnabled) {
	clearSelect("provider", "select", rowId);
	if (srcDomainId != "null" && dstDomainId != "null") {
		$("#provider-select-" + rowId).append('<option value="null">' + tt("loading") + '</option>');
		$.ajax({
			url: baseUrl+'/topology/provider/get-by-domains',
			dataType: 'json',
			data: {
				domains: JSON.stringify([srcDomainId, dstDomainId]),
			},
			success: function(response){
				clearSelect("provider", "select", rowId);
				$("#provider-select-" + rowId).append('<option value="null">' + tt("select") + '</option>');
				enableSelect("provider", "select", rowId);
				for (var i = 0; i < response.length; i++) {
					$("#provider-select-" + rowId).append('<option value="' + response[i].id + '">' + 
							response[i].nsa.split(":")[3] + " - " + 
							response[i].type.charAt(0) + response[i].type.slice(1).toLowerCase() + '</option>');
			    }
				if (currentEnabled != null) {
					$("#provider-select-" + rowId).val(getData("provider", rowId));
				}
			}
		});
	} else {
		disableSelect("provider", "select", rowId);
	}
}

function fillDomainSelect(endPointType, domains, rowId) {
	clearSelect(endPointType, "domain", rowId);
	$("#"+ endPointType + "-domain-" + rowId).append('<option value="null">' + tt("select") + '</option>');
	for (var i = 0; i < domains.length; i++) {
		$("#"+ endPointType + "-domain-" + rowId).append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
	}
	if (rowId != "new") {
		$("#"+ endPointType + "-domain-" + rowId).val(getData(endPointType + "-domain", rowId));
	}
}

function fillDeviceSelect(endPointType, domainId, rowId, currentEnabled) {
	clearSelect(endPointType, "device", rowId);
	if (domainId != "null" && domainId != null) {
		$("#"+ endPointType + "-device-" + rowId).append('<option value="null">' + tt("loading") + '</option>');
		$.ajax({
			url: baseUrl+'/topology/device/get-by-domain',
			dataType: 'json',
			data: {
				id: domainId,
			},
			success: function(response){
				clearSelect(endPointType, "device", rowId);
				$("#"+ endPointType + "-device-" + rowId).append('<option value="null">' + tt("select") + '</option>');
				enableSelect(endPointType, "device", rowId);
				for (var i = 0; i < response.length; i++) {
					$("#"+ endPointType + "-device-" + rowId).append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
			    }
				if (currentEnabled != null) {
					$("#"+ endPointType + "-device-" + rowId).val(getData(endPointType + "-device", rowId));
				}
			}
		});
	} else {
		disableSelect(endPointType, "device", rowId);
	}
}

function fillPortSelect(endPointType, deviceId, rowId, currentEnabled) {
	clearSelect(endPointType, "port", rowId);
	if (deviceId != "null" && deviceId != null) {
		$("#"+ endPointType + "-port-" + rowId).append('<option value="null">' + tt("loading") + '</option>');
		$.ajax({
			url: baseUrl+'/topology/urn/get-by-device',
			dataType: 'json',
			data: {
				id: deviceId,
				cols: JSON.stringify(['id','port']),
			},
			success: function(response){
				clearSelect(endPointType, "port", rowId);
				$("#"+ endPointType + "-port").append('<option value="null">' + tt("select") + '</option>');
				enableSelect(endPointType, "port", rowId);
				for (var i = 0; i < response.length; i++) {
					var port = response[i].port;
					if (response[i].port == "") {
						port = tt("no name");
					}
					$("#"+ endPointType + "-port-" + rowId).append('<option value="' + response[i].id + '">' + port + '</option>');
			    }
				if (currentEnabled != null) {
					$("#"+ endPointType + "-port-" + rowId).val(getData(endPointType + "-port", rowId));
				}
			}
		});
	} else {
		disableSelect(endPointType, "port", rowId);
	} 
}

function initEndPointSelects(endPointType, domains, rowId) {
	fillDomainSelect(endPointType, domains, rowId);
	
	$('#' + endPointType + '-domain-' + rowId).on('change', function() {
		fillDeviceSelect(endPointType, this.value, rowId);
		fillPortSelect(endPointType, null, rowId);
		fillProviderSelect($('#src-domain-' + rowId).val(), $('#dst-domain-' + rowId).val(), rowId);
	});
	
	$('#' + endPointType + '-device-' + rowId).on('change', function() {
		fillPortSelect(endPointType, this.value, rowId);
	});
}

function clearSelect(endPointType, object, rowId) {
	$('#' + endPointType + '-' + object + '-' + rowId).children().remove();
}

function disableSelect(endPointType, object, rowId) {
	$('#' + endPointType + '-' + object + '-' + rowId).prop('disabled', true);
}

function enableSelect(endPointType, object, rowId) {
	$('#' + endPointType + '-' + object + '-' + rowId).prop('disabled', false);
}

function initFrequencyDialog(type, rowId){	
	$('#'+type+'-form').dialog({
		width: 300,
		modal: true,
		buttons: [{
			text: tt("Save"),
			click: function() {
	        	if (type == 'daily') {
	        		var time = $('#dailyTime').val();
	        		var res = time.split(":");
	        		
	        		var min = res[1];
	        		var hour = res[0];
	        		$('#freq-value-' + rowId).val(min+' '+hour+' * * *');
	        		
	        		$(this).dialog('close');
	        	}
	        	if (type == 'weekly') {
	        		var time = $('#weeklyTime').val();
	        		var res = time.split(":");
	        		
	        		var min = res[1];
	        		var hour = res[0];
	        		var days = '';
	        		
	        		var countDays = $('.weekDays:checked').size();
	        		var counter = 1;
	        		
	        		if (countDays == 0) {
	        			alert('Preencha os dias da semana desejados');
	        		}
	        		else {
		        		$('.weekDays:checked').each(function() {
		        			if (counter < countDays)
		        				days += this.value+',';
		        			else 
		        				days += this.value;
		        			
		        			counter++;
		        		});
	        		}

	        		$('#freq-value-' + rowId).val(min+' '+hour+' * * '+days);
	        		
	        		$(this).dialog('close');
	        	}
	        	if (type == 'monthly') {
	        		var time = $('#monthlyTime').val();
	        		var res = time.split(":");
	        		
	        		var min = res[1];
	        		var hour = res[0];
	        		
	        		var day = $('#month-day-freq-select').val();
	        		
	        		$('#freq-value-' + rowId).val(min+' '+hour+' '+day+' * *');
	        		
	        		$(this).dialog('close');
	        	}
	        }},{
	        text: tt("Cancel"),
	        click: function() {
	          $(this).dialog('close');
	        }
	      }],
	});
}
