$(document).ready(function() {
	$('#delete_button').click(deleteURNs);
	$("input[type='checkbox']").click(checkClick);

	$.getJSON(baseUrl + "/topology/urn/get-domains-id", 
			function(data) {
				var first = true;
				
		    	$.each(data, function(key, val) {
	    			document.getElementById("add_button".concat(val)).addEventListener("click", function(){newURNLine(val)}, false);
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

/////////////////////// checkClick ////////////
function checkClick(){

}


/////////////////////// SAVE ALL URNS //////////////////////
function saveURNs(){
	if(confirm("Save all URNs?")){
		$.getJSON(baseUrl + "/topology/urn/get-domains-id", 
				function(data) {
					$.each(data, function(key, val) {
						var errorControl=false;
						var dataString1;
						var item1 = document.getElementById("newLine".concat(val));
						if(item1!=null){
			    			dataString1 = saveNew(val);
			    			if(!dataString1) errorControl=true;
			    		}
			    		var dataString2;
			    		var item2 = document.getElementById("editLine".concat(val));
			    		if(item2!=null){
			    			var id = document.getElementById("editId".concat(val)).value;
			    			dataString2 = saveUpdate(val, id);
			    			if(!dataString2) errorControl=true;
			    		} 

			    		if(!errorControl && (item1!=null || item2!=null)){
			    			if(dataString1){
				    			$.ajax({
									type: "POST",
									url: baseUrl + "/topology/urn/create",
									data: dataString1,
									cache: false,
								});
			    			}
			    			if(dataString2){
				    			$.ajax({
									type: "POST",
									url: baseUrl + "/topology/urn/update",
									data: dataString2,
									cache: false,
									async:false,
								});
			    			}
			    			$.pjax.defaults.timeout = false;
			    			$.pjax.reload({container:'#pjaxContainer'.concat(val)});
			    			document.getElementById("add_button".concat(val)).style.display = 'inherit';
			    		}

			    	});
			    	
				}
		);
	}
}

/////////////////////// DELETE MANY URNS //////////////////////
function deleteURNs(){
	$("#message").html(tt('The selected URNs will be deleted.<br>Do you confirm?'));
	$("#dialog").dialog({
		buttons: [
			{
				text: tt("Yes"),
				click: function() {
					$.getJSON(baseUrl + "/topology/urn/get-domains-id", 
							function(data) {
						$.each(data, function(key, val) {
							var item = document.getElementById("grid".concat(val));
							if(item!=null){
				    			var keys = $(item).yiiGridView('getSelectedRows');
				    			if(keys.length>0){
				    				$.each(keys, function(index, element){
				    					$.ajax({
				    						type: "POST",
				    						url: baseUrl + "/topology/urn/delete",
				    						data: "id=".concat(element).concat("&show=true"),
				    						cache: false,
				    					});
				    				})
				    			}
							}
						});
						window.location= baseUrl.concat("/topology/urn");
					}
					);
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

/////////////////////// DELETE URN //////////////////////
function deleteURN(obj){
	$("#message").html(tt('Delete this URN?'));
	$("#dialog").dialog({
		buttons: [
			{
				text: tt("Yes"),
				click: function() {
					$.ajax({
						type: "POST",
						url: baseUrl + "/topology/urn/delete",
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

/////////////////////// EDIT URN //////////////////////
function editURN(obj, id){
	$.ajax({
		type: "POST",
		url: baseUrl + "/topology/urn/can-update",
		data: "id=".concat(id),
		cache: false,
		success: function(html) {
			if(html){
				$.ajax({
					type: "POST",
					url: baseUrl + "/topology/urn/get-domain-id",
					data: "urnId=".concat(id),
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
						columns += '<td align="center"><select disabled id="editDevice' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editPort' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="60" id="editName' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editVlan' + domId + '" placeholder = "1-2,5-6"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editMax_capacity' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editMin_capacity' + domId + '"/></td>';
						columns += '<td align="center"><input type="text" size="6" id="editGranularity' + domId + '"/></td></tr>';
					
					    $(tr).after(columns);  // add tr to table

					    $(tr).hide();

					    //Puts the ID into a invisible camp
					    document.getElementById("editId".concat(domId)).value = id;
					    document.getElementById("editId".concat(domId)).style.display = 'none';

					    $.getJSON(baseUrl + "/topology/urn/get-urn?id="+id, 
								function(data) {
					    			document.getElementById("editPort".concat(domId)).value = data.port;
					    			document.getElementById("editName".concat(domId)).value = data.value;
					    			document.getElementById("editMax_capacity".concat(domId)).value = data.max_capacity;
					    			document.getElementById("editMin_capacity".concat(domId)).value = data.min_capacity;
					    			document.getElementById("editGranularity".concat(domId)).value = data.granularity;
								}
						);
					    
					    $.getJSON(baseUrl + "/topology/urn/get-vlan?id="+id, 
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
								$("#message").html(tt('Update this URN?'));
								$("#dialog").dialog({
									buttons: [
										{
											text: tt('Yes'),
										    click: function() {
										    	$(this).dialog( "close" );
												$.ajax({
													type: "POST",
													url: baseUrl + "/topology/urn/update",
													data: dataString,
													cache: false,
													success: function(html) {
														if(html == "error") {
															$("#dialog").dialog("open");
															$("#message").html(tt('Save URN failed, please check the camps'));
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
															$.pjax.reload({container:'#pjaxContainer'.concat(html)});
															document.getElementById("add_button".concat(html)).style.display = 'inherit';
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

					   
						$.getJSON(baseUrl + "/topology/urn/get-networks-new-row?domainId="+domId, 
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
								}
						);
						 
						document.getElementById("editNetwork".concat(domId)).addEventListener("change", function() {
								var netName = this.options[this.selectedIndex].text;
						        $.getJSON(baseUrl + "/topology/urn/get-devices-new-row?networkName="+netName+"&domainId="+domId, 
							    		function(data) {
								        	var selectBox = document.getElementById("editDevice".concat(domId));
							        	    var i;
							        	    for(i=selectBox.options.length-1;i>=0;i--){
							        	    	selectBox.remove(i);
							        	    }
							    	 
							        	    if(data.length>1){
								        	    $.each(data, function(key, val) {
								        	    	document.getElementById("editDevice".concat(domId)).disabled = false;
								        	    	var newOption = document.createElement('option');
										            newOption.text = val;
										            newOption.value = key;
								
										            // For standard browsers
										            try { selectBox.add(newOption, null); }
										            // For Microsoft Internet Explorer and other non-standard browsers.
										            catch (ex) { selectBox.add(newOption); }
								        	    });
							        	    }
							        	    else document.getElementById("editDevice".concat(domId)).disabled = true;
								        }
						        );
						 });
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

/////////////////////// NEW URN LINE //////////////////////
function newURNLine(obj){	
	$.ajax({
		type: "POST",
		url: baseUrl + "/topology/urn/can-create",
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
					columns += '<td align="center" width="40px"><select disabled id="device' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="3" id="port' + obj + '"/></td>';
					columns += '<td align="center"><input type="text" size="60" id="name' + obj + '"/></td>';
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
							$("#message").html(tt('Save this URN?'));
							$("#dialog").dialog({
								
								buttons: [
									{
										text: tt('Yes'),
									    click: function() {
									  	  $(this).dialog( "close" );
											$.ajax({
												type: "POST",
												url: baseUrl + "/topology/urn/create",
												data: dataString,
												cache: false,
												success: function(html) {
													if(html == "error"){
														$("#dialog").dialog("open");
														$("#message").html(tt('Save URN failed, please check the camps'));
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
					
					$.getJSON(baseUrl + "/topology/urn/get-networks-new-row?domainId="+obj, 
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
					
					document.getElementById("network".concat(obj)).addEventListener("change", function() {
							var netName = this.options[this.selectedIndex].text;
					        $.getJSON(baseUrl + "/topology/urn/get-devices-new-row?networkName="+netName+"&domainId="+obj, 
						    		function(data) {
							        	var selectBox = document.getElementById("device".concat(obj));
						        	    var i;
						        	    for(i=selectBox.options.length-1;i>=0;i--){
						        	    	selectBox.remove(i);
						        	    }
						    	 
						        	    if(data.length>1){
							        	    $.each(data, function(key, val) {
							        	    	document.getElementById("device".concat(obj)).disabled = false;
							        	    	var newOption = document.createElement('option');
									            newOption.text = val;
									            newOption.value = key;
							
									            // For standard browsers
									            try { selectBox.add(newOption, null); }
									            // For Microsoft Internet Explorer and other non-standard browsers.
									            catch (ex) { selectBox.add(newOption); }
							        	    });
						        	    } else document.getElementById("device".concat(obj)).disabled = true;
							        }
					        );
					});
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
		var deviceSelected = document.getElementById("device".concat(obj));
		if(deviceSelected.value>0){ //Testa se selecionou device
			dataString += "network=".concat(networkSelected.options[networkSelected.selectedIndex].text)
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
	
	var port = document.getElementById("port".concat(obj)).value;
	if (port != ""){
	    dataString += "&port=".concat(port);
	} else {
		document.getElementById("port".concat(obj)).focus();
		$("#message").html(tt('Please insert a port'));
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
	
	var description = document.getElementById("name".concat(obj)).value;
	if(description != ""){
		dataString += "&value=".concat(description);
	} else {
		document.getElementById("name".concat(obj)).focus();
		$("#message").html(tt('Please insert a description'));
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
		var deviceSelected = document.getElementById("editDevice".concat(obj));
		if(deviceSelected.value>0){ //Testa se selecionou device
			dataString += "&network=".concat(networkSelected.options[networkSelected.selectedIndex].text)
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
	
	var port = document.getElementById("editPort".concat(obj)).value;
	if (port != ""){
	    dataString += "&port=".concat(port);
	} else {
		document.getElementById("editPort".concat(obj)).focus();
		$("#message").html(tt('Please insert a port'));
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
	
	var description = document.getElementById("editName".concat(obj)).value;
	if(description != ""){
		dataString += "&value=".concat(description);
	} else {
		document.getElementById("editName".concat(obj)).focus();
		$("#message").html(tt('Please insert a description'));
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