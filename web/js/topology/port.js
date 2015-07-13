$(document).ready(function() {
	$('#delete_button').click(deletePorts);

	$.getJSON(baseUrl + "/topology/port/get-domains-id", 
			function(data) {
				var first = true;
				
		    	$.each(data, function(key, val) {
	    			document.getElementById("add_button".concat(val)).addEventListener("click", function(){newPortLine(val)}, false);
	    			if(selected_domain != null && selected_domain != val){
				    	$("#collapsable".concat(val)).slideUp(1);
			    		$("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/plus.gif");
		    		}
		    		else{
		    			if(first == false){
			    			$("#collapsable".concat(val)).slideUp();
		    			    $("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/plus.gif");
			   			}
			   			else first = false;
		    		}
	    			
		    		document.getElementById("collapseExpand".concat(val)).addEventListener("click", function(){
	    				if ($("#collapsable".concat(val)).css("display") == "none") {
	    				    $("#collapsable".concat(val)).slideDown();
	    				    $("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/minus.gif" );
	    				} else {
	    				    $("#collapsable".concat(val)).slideUp();
	    				    $("#collapseExpand".concat(val)).attr("src", baseUrl+"/images/plus.gif");
	    				}
		    		});
		    	});
			}
	);
});

/////////////////////// DELETE MANY PORTS //////////////////////
function deletePorts(){
	$("#message").html(tt('The selected ports will be deleted.<br>Do you confirm?'));
	$("#dialog").dialog({
		buttons: [
			{
				text: tt("Yes"),
				click: function() {
					$.getJSON(baseUrl + "/topology/port/get-domains-id", 
						function(data) {
						var ids = [];
						$.each(data, function(key, val) {
							var item = document.getElementById("grid".concat(val));
							if(item!=null){
					    		var keys = $(item).yiiGridView('getSelectedRows');
					    		if(keys.length>0){
					    			$.each(keys, function(index, element){
					    				ids.push(element);
					    			})
					    		}
							}
						});
		    			$.ajax({
	    					type: "POST",
	    					url: baseUrl + "/topology/port/delete",
	    					data: { itens : ids },
	    					cache: false,
	    					success: function(html){
	    						window.location= baseUrl.concat("/topology/port");
	    					}
	    				});	
					});
				}
			},
			{
				  text: tt("No"),
			    click: function() {
			  	  $(this).dialog( "close" );
			    }
			},
        ]
	});
	$("#dialog").dialog("open");
}

/////////////////////// DELETE PORT //////////////////////
function deletePort(obj){
	$("#message").html(tt('Delete this port?'));
	$("#dialog").dialog({
		buttons: [
			{
				text: tt("Yes"),
				click: function() {
					$.ajax({
						type: "POST",
						url: baseUrl + "/topology/port/delete-one",
						data: "id=".concat(obj),
						cache: false,
						success: function(html) {
							if(html){
								$.pjax.defaults.timeout = false;
								$.pjax.reload({container:'#pjaxContainer'.concat(html)});
								document.getElementById("add_button".concat(html)).style.display = 'inherit';
							}
							else {
								$("#dialog").dialog("open");
								$("#message").html(tt('This operation is not allowed'));
								$("#dialog").dialog({
									buttons: [
										{
											text: "Ok",
										    click: function() {
										  	  $(this).dialog( "close" );
										    }
										},
									]
								});
							}
						}
					});
					$(this).dialog( "close" );
				}
			},
			{
				  text: tt("No"),
			    click: function() {
			  	  $(this).dialog( "close" );
			    }
			},
        ]
	});
	$("#dialog").dialog("open");
}

/////////////////////// EDIT PORT //////////////////////
function editPort(obj, id){
	$.ajax({
		type: "POST",
		url: baseUrl + "/topology/port/can-update",
		data: "id=".concat(id),
		cache: false,
		success: function(html) {
			if(html){
				$.ajax({
					type: "POST",
					url: baseUrl + "/topology/port/get-domain-id",
					data: "portId=".concat(id),
					cache: false,
					success: function(html) {
						var domId = html;
						if(document.getElementById("editLine".concat(domId))!=null){
							return;
						};
						var a = obj; // get element anchor
					    var td = $(a).parent(); // get parent dari element anchor = td
					    var tr = $(td).parent(); // get element tr
					
						var columns = '<tr class="edit"+ id="editLine'+domId+'">';
						columns += '<td><input type="text" size="3" id="editId' + domId + '"/></td>';
						columns += '<td align="center" width="15px"><img title="'+tt('Confirm')+'" class="edit" alt="'+tt('Confirm')+'" border="0" id="editConfirm' + domId + '" src="'+baseUrl+'/images/ok.png"/></td>';
						columns += '<td align="center" width="15px"><img title="'+tt('Cancel')+'" class="edit" alt="'+tt('Cancel')+'" border="0" id="editDelete' + domId + '" src="'+baseUrl+'/images/clear.png"/></td>';
						columns += '<td align="center"><select id="editNetwork' + domId + '"/></td>';
						columns += '<td align="center"><select id="editDevice' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editName' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="60" id="editUrn' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editVlan' + domId + '" placeholder = "1-2,5-6"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editMax_capacity' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editMin_capacity' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editGranularity' + domId + '"/></td></tr>';
					
					    $(tr).after(columns);  // add tr to table

					    $(tr).hide();

					    //Puts the ID into a invisible camp
					    document.getElementById("editId".concat(domId)).value = id;
					    document.getElementById("editId".concat(domId)).style.display = 'none';

					    $.getJSON(baseUrl + "/topology/port/get-port?id="+id, 
								function(data) {
					    			document.getElementById("editName".concat(domId)).value = data.name;
					    			document.getElementById("editUrn".concat(domId)).value = data.urn;
					    			document.getElementById("editMax_capacity".concat(domId)).value = data.max_capacity;
					    			document.getElementById("editMin_capacity".concat(domId)).value = data.min_capacity;
					    			document.getElementById("editGranularity".concat(domId)).value = data.granularity;
								}
						);
					    
					    $.getJSON(baseUrl + "/topology/port/get-vlan?id="+id, 
								function(data) {
					    			var string = "";
					    			for (var i = 0; i < data.length; i++) { 
					    			    string = string.concat(data[i].value);
					    			    if(i < data.length-1) string = string.concat(",");
					    			}
					    			document.getElementById("editVlan".concat(domId)).value = string;
								}
						);
						
						$(document.getElementById("editDelete".concat(domId))).click(function() {
							var item = document.getElementById("editLine".concat(domId));
							item.remove(item);
							$(tr).show();
						});
						
						$(document.getElementById("editConfirm".concat(domId))).click(function() {
							var dataString = saveUpdate(domId, id);
							if(dataString){
								$("#message").html(tt('Update this port?'));
								$("#dialog").dialog({
									buttons: [
										{
											text: tt('Yes'),
										    click: function() {
										    	$(this).dialog("close");
												$.ajax({
													type: "POST",
													url: baseUrl + "/topology/port/update",
													data: dataString,
													cache: false,
													success: function(html) {
														if(html == "error") {
															$("#dialog").dialog("open");
															$("#message").html(tt('Save port failed, please check the camps'));
															$("#dialog").dialog({
																
																buttons: [
																	{
																		text: "Ok",
																	    click: function() {
																	  	  $(this).dialog( "close" );
																	    }
																	},
																]
															});
														}
														else{
															$.pjax.defaults.timeout = false;
															$.pjax.reload({container:'#pjaxContainer'.concat(domId)});
															document.getElementById("add_button".concat(domId)).style.display = 'inherit';
														}
													}
												});
										    }
										},
										{
											text: tt("No"),
										    click: function() {
										  	  $(this).dialog( "close" );
										    }
										},
									]
								});
								$("#dialog").dialog("open");
							}
						});

						$.getJSON(baseUrl + "/topology/port/get-networks-new?domainId="+domId, 
								function(data) {
							    	var selectBox = document.getElementById("editNetwork".concat(domId));
							    	$.each(data, function(key, val) {
							        	var newOption = document.createElement('option');
							        	newOption.text = val;
							            newOption.value = key;
						
							            // For standard browsers
							            try { selectBox.add(newOption, null); }
							            // For Microsoft Internet Explorer and other non-standard browsers.
							            catch (ex) { selectBox.add(newOption); }
							    	});
							    	$.getJSON(baseUrl + "/topology/port/get-port-network?id="+id, 
										function(data) {
								    		for(var i=0;i<selectBox.options.length;i++){
								                if (selectBox.options[i].innerHTML == data) {
								                	selectBox.selectedIndex = i;
								                    break;
								                }
								            }
										}
									);
								}
						);
						
						$.getJSON(baseUrl + "/topology/port/get-devices-new?domainId="+domId, 
								function(data) {
							    	var selectBox = document.getElementById("editDevice".concat(domId));
							    	$.each(data, function(key, val) {
							        	var newOption = document.createElement('option');
							        	newOption.text = val;
							            newOption.value = key;
						
							            // For standard browsers
							            try { selectBox.add(newOption, null); }
							            // For Microsoft Internet Explorer and other non-standard browsers.
							            catch (ex) { selectBox.add(newOption); }
							    	});
							    	$.getJSON(baseUrl + "/topology/port/get-port-device?id="+id, 
										function(data) {
								    		for(var i=0;i<selectBox.options.length;i++){
								                if (selectBox.options[i].innerHTML == data) {
								                	selectBox.selectedIndex = i;
								                    break;
								                }
								            }
										}
									);
								}
						);
						 
					}
				});
			}
			else {
				$("#dialog").dialog("open");
				$("#message").html(tt('This operation is not allowed'));
				$("#dialog").dialog({
					buttons: [
						{
							text: "Ok",
						    click: function() {
						    	$(this).dialog( "close" );
						    }
						},
					]
				});
			}
		}
	});
	
}

/////////////////////// NEW PORT LINE //////////////////////
function newPortLine(obj){	
	$.ajax({
		type: "POST",
		url: baseUrl + "/topology/port/can-create",
		data: "id=".concat(obj),
		cache: false,
		success: function(html) {
			if(html){
				if(document.getElementById("newLine".concat(obj))==null){
					
					document.getElementById("add_button".concat(obj)).style.display = 'none';
					
					var container = document.getElementById("grid".concat(obj));
					var table = $(container).children('table');
					var tbody = table.children('tbody');
					
					var columns = '<tr id="newLine'+obj+'"><td></tb>';
					columns += '<td align="center" width="15px"><img title="'+tt('Confirm')+'" class="edit" alt="'+tt('Confirm')+'" border="0" id="confirm' + obj + '" src="'+baseUrl+'/images/ok.png"/></td>';
					columns += '<td align="center" width="15px"><img title="'+tt('Confirm')+'" class="edit" alt="'+tt('Confirm')+'" border="0" id="delete' + obj + '" src="'+baseUrl+'/images/clear.png"/></td>';
					columns += '<td align="center" width="100px"><select id="network' + obj + '"/></td>';
					columns += '<td align="center" width="100px"><select id="device' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="6" id="name' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="60" id="urn' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="6" id="vlan' + obj + '" placeholder = "1-2,5-6"/></td>';
					columns += '<td align="center"><input type="text" size="6" id="max_capacity' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="6" id="min_capacity' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="6" id="granularity' + obj + '"/></td></tr>';
					
					table.append(columns);
					
					$(document.getElementById("delete".concat(obj))).click(function() {
						var item = document.getElementById("newLine".concat(obj));
						item.remove(item);
						document.getElementById("add_button".concat(obj)).style.display = 'inherit';
					});
					
					$(document.getElementById("confirm".concat(obj))).click(function() {
						var dataString = saveNew(obj);
						if(dataString){
							$("#message").html(tt('Save this port?'));
							$("#dialog").dialog({
								
								buttons: [
									{
										text: tt('Yes'),
									    click: function() {
									  	  $(this).dialog( "close" );
											$.ajax({
												type: "POST",
												url: baseUrl + "/topology/port/create",
												data: dataString,
												cache: false,
												success: function(html) {
													if(html == "error"){
														$("#dialog").dialog("open");
														$("#message").html(tt('Save port failed, please check the camps'));
														$("#dialog").dialog({
															
															buttons: [
																{
																	text: "Ok",
																    click: function() {
																  	  $(this).dialog( "close" );
																    }
																},
															]
														});
													}
													else{
														$.pjax.defaults.timeout = false;
														$.pjax.reload({container:'#pjaxContainer'.concat(obj)});
														document.getElementById("add_button".concat(obj)).style.display = 'inherit';
													}
												}
											});
									    }
									},
									{
										text: tt("No"),
									    click: function() {
									  	  $(this).dialog( "close" );
									    }
									},
								]
							});
							$("#dialog").dialog("open");
						}
					});
					
					$.getJSON(baseUrl + "/topology/port/get-networks-new?domainId="+obj, 
							function(data) {
						    	var selectBox = document.getElementById("network".concat(obj));
						    	$.each(data, function(key, val) {
						        	var newOption = document.createElement('option');
						        	newOption.text = val;
						            newOption.value = key;
					
						            // For standard browsers
						            try { selectBox.add(newOption, null); }
						            // For Microsoft Internet Explorer and other non-standard browsers.
						            catch (ex) { selectBox.add(newOption); }
						    	});
							}
					);
					
					$.getJSON(baseUrl + "/topology/port/get-devices-new?domainId="+obj, 
							function(data) {
						    	var selectBox = document.getElementById("device".concat(obj));
						    	$.each(data, function(key, val) {
						        	var newOption = document.createElement('option');
						        	newOption.text = val;
						            newOption.value = key;
					
						            // For standard browsers
						            try { selectBox.add(newOption, null); }
						            // For Microsoft Internet Explorer and other non-standard browsers.
						            catch (ex) { selectBox.add(newOption); }
						    	});
							}
					);
				}
			}
			else {
				$("#dialog").dialog("open");
				$("#message").html(tt('This operation is not allowed'));
				$("#dialog").dialog({
					buttons: [
						{
							text: "Ok",
						    click: function() {
						    	$(this).dialog( "close" );
						    }
						},
					]
				});
			}
		}
	});

}

/////////////////////// SAVE NEW /////////////////////
function saveNew(obj){
	var dataString = "";
	
    var networkSelected = document.getElementById("network".concat(obj));
	if(networkSelected.value > 0){ //Testa se selecionou network
		dataString += "network=".concat(networkSelected.options[networkSelected.selectedIndex].text)
    } else {
    	document.getElementById("network".concat(obj)).focus();
		$("#message").html(tt('Please select a network'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
    }
	
	var deviceSelected = document.getElementById("device".concat(obj));
	if(deviceSelected.value>0){ //Testa se selecionou device
	    dataString += "&device=".concat(deviceSelected.options[deviceSelected.selectedIndex].text);
	} else {
    	document.getElementById("device".concat(obj)).focus();
		$("#message").html(tt('Please select a device'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
    }
	
	var name = document.getElementById("name".concat(obj)).value;
	if (name != ""){
	    dataString += "&name=".concat(name);
	} else {
		document.getElementById("name".concat(obj)).focus();
		$("#message").html(tt('Please insert a name'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
	
	var description = document.getElementById("urn".concat(obj)).value;
	if(description != ""){
		dataString += "&urn=".concat(description);
	} else {
		document.getElementById("urn".concat(obj)).focus();
		$("#message").html(tt('Please insert a URN'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
    
	//Na Vlan valida a sintaxe digitada. Sintaxe esperada:1 ou 1-2 ou 1-2,3-4.
	var vlan = document.getElementById("vlan".concat(obj)).value;
    if (vlan != ""){
    	var vlans = vlan.split(",");
    	var vlansReady = new Array();
    	for (i = 0; i < vlans.length; i++) {
    		var stringAux = vlans[i].split("");
    		for (j = 0; j < stringAux.length; j++) {
    			//Testa se é um número ou o traço
    			if(stringAux[j]=='1' || stringAux[j]=='2' || stringAux[j]=='3' || stringAux[j]=='4' ||
    			   stringAux[j]=='5' || stringAux[j]=='6' || stringAux[j]=='7' || stringAux[j]=='8' ||
    			   stringAux[j]=='9' || stringAux[j]=='0' || stringAux[j]=='-'){
    				//Testa se tem número depois do traço
    				if(stringAux[j]=='-' && j == stringAux.length-1){
    					document.getElementById("vlan".concat(obj)).focus();
						$("#message").html(tt('In Vlan: Missing argument after \"-\".<br>Sintax samples:<br>200<br>200-300<br>200-300,800-990'));
						$("#dialog").dialog({
							
							buttons: [
								{
									text: "Ok",
								    click: function() {
								  	  $(this).dialog( "close" );
								    }
								},
							]
						});
						$("#dialog").dialog("open");
	    				return false;
    				}
    			}
    			else {
    				document.getElementById("vlan".concat(obj)).focus();
    				var msgAux = tt('In Vlan: \"').concat(stringAux[j]).concat(tt('\" is not a valid character.<br>Sintax samples:<br>200<br>200-300<br>200-300,800-990'));
					$("#message").html(msgAux);
					$("#dialog").dialog({
						
						buttons: [
							{
								text: "Ok",
							    click: function() {
							  	  $(this).dialog( "close" );
							    }
							},
						]
					});
					$("#dialog").dialog("open");
    				return false;
    			}
    		}
    	}
    	dataString += "&vlan=".concat(vlan);
    } else {
		document.getElementById("vlan".concat(obj)).focus();
		$("#message").html(tt('Please insert a Vlan'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}

    var max_capacity = document.getElementById("max_capacity".concat(obj)).value;
    if (!isNaN(max_capacity)){
	    dataString += "&max_capacity=".concat(max_capacity);
    } else {
		document.getElementById("max_capacity".concat(obj)).focus();
		$("#message").html(tt('Please insert a valid value for max capacity'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
    
    var min_capacity = document.getElementById("min_capacity".concat(obj)).value;
    if(!isNaN(min_capacity)){
    	dataString += "&min_capacity=".concat(min_capacity);
    } else {
		document.getElementById("min_capacity".concat(obj)).focus();
		$("#message").html(tt('Please insert a valid value for min capacity'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
    
    var granularity = document.getElementById("granularity".concat(obj)).value;
    if(!isNaN(granularity)){
	    dataString += "&granularity=".concat(granularity);
    } else {
		document.getElementById("granularity".concat(obj)).focus();
		$("#message").html(tt('Please insert a valid value for granularity'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
    
    return dataString;
    
}

/////////////////////// SAVE UPDATE //////////////////////
function saveUpdate(obj, id){
	
	var dataString = "id=".concat(id);
	
    var networkSelected = document.getElementById("editNetwork".concat(obj));
	if(networkSelected.value > 0){ //Testa se selecionou network
		dataString += "&network=".concat(networkSelected.options[networkSelected.selectedIndex].text)
    } else {
    	document.getElementById("editNetwork".concat(obj)).focus();
		$("#message").html(tt('Please select a network'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
    }
	
	var deviceSelected = document.getElementById("editDevice".concat(obj));
	if(deviceSelected.value>0){ //Testa se selecionou device
	    dataString += "&device=".concat(deviceSelected.options[deviceSelected.selectedIndex].text);
	} else {
    	document.getElementById("editDevice".concat(obj)).focus();
		$("#message").html(tt('Please select a device'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
    }
	
	var name = document.getElementById("editName".concat(obj)).value;
	if (name != ""){
	    dataString += "&name=".concat(name);
	} else {
		document.getElementById("editName".concat(obj)).focus();
		$("#message").html(tt('Please insert a name'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
	
	var urn = document.getElementById("editUrn".concat(obj)).value;
	if(urn != ""){
		dataString += "&urn=".concat(urn);
	} else {
		document.getElementById("editUrn".concat(obj)).focus();
		$("#message").html(tt('Please insert a URN'));
		$("#dialog").dialog({
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		$("#dialog").dialog("open");
		return false;
	}
    
	//Na Vlan valida a sintaxe digitada. Sintaxe esperada:1 ou 1-2 ou 1-2,3-4.
	var vlan = document.getElementById("editVlan".concat(obj)).value;
    if (vlan != ""){
    	var vlans = vlan.split(",");
    	var vlansReady = new Array();
    	for (i = 0; i < vlans.length; i++) {
    		var stringAux = vlans[i].split("");
    		for (j = 0; j < stringAux.length; j++) {
    			//Testa se é um número ou o traço
    			if(stringAux[j]=='1' || stringAux[j]=='2' || stringAux[j]=='3' || stringAux[j]=='4' ||
    			   stringAux[j]=='5' || stringAux[j]=='6' || stringAux[j]=='7' || stringAux[j]=='8' ||
    			   stringAux[j]=='9' || stringAux[j]=='0' || stringAux[j]=='-'){
    				//Testa se tem número depois do traço
    				if(stringAux[j]=='-' && j == stringAux.length-1){
    					document.getElementById("editVlan".concat(obj)).focus();
    					$("#message").html(tt('In Vlan: Missing argument after \"-\".<br>Sintax samples:<br>200<br>200-300<br>200-300,800-990'));
    					$("#dialog").dialog({
    						
    						buttons: [
    							{
    								text: "Ok",
    							    click: function() {
    							  	  $(this).dialog( "close" );
    							    }
    							},
    						]
    					});
    					$("#dialog").dialog("open");
	    				return false;
    				}
    			}
    			else {
    				document.getElementById("editVlan".concat(obj)).focus();
    		    	var msgAux = tt('In Vlan: \"').concat(stringAux[j]).concat(tt('\" is not a valid character.<br>Sintax samples:<br>200<br>200-300<br>200-300,800-990'));
					$("#message").html(msgAux);
    				$("#dialog").dialog({
    					
    					buttons: [
    						{
    							text: "Ok",
    						    click: function() {
    						  	  $(this).dialog( "close" );
    						    }
    						},
    					]
    				});
    				$("#dialog").dialog("open");
					return false;
    			}
    		}
    	}
    	dataString += "&vlan=".concat(vlan);
    } else {
		document.getElementById("editVlan".concat(obj)).focus();
    	$("#dialog").dialog("open");
		$("#message").html(tt('Please insert a Vlan'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		return false;
	}

    var max_capacity = document.getElementById("editMax_capacity".concat(obj)).value;
    if (!isNaN(max_capacity)){
	    dataString += "&max_capacity=".concat(max_capacity);
    } else {
		document.getElementById("editMax_capacity".concat(obj)).focus();
    	$("#dialog").dialog("open");
		$("#message").html(tt('Please insert a valid value for max capacity'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		return false;
	}
    
    var min_capacity = document.getElementById("editMin_capacity".concat(obj)).value;
    if(!isNaN(min_capacity)){
    	dataString += "&min_capacity=".concat(min_capacity);
    } else {
		document.getElementById("editMin_capacity".concat(obj)).focus();
    	$("#dialog").dialog("open");
		$("#message").html(tt('Please insert a valid value for min capacity'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog( "close" );
				    }
				},
			]
		});
		return false;
	}
    
    var granularity = document.getElementById("editGranularity".concat(obj)).value;
    if(!isNaN(granularity)){
	    dataString += "&granularity=".concat(granularity);
    } else {
		document.getElementById("editGranularity".concat(obj)).focus();
    	$("#dialog").dialog("open");
		$("#message").html(tt('Please insert a valid value for granularity'));
		$("#dialog").dialog({
			
			buttons: [
				{
					text: "Ok",
				    click: function() {
				  	  $(this).dialog("close");
				    }
				},
			]
		});
		return false;
	}
    
	return dataString;
}