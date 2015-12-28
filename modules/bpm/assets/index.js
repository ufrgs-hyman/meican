/////////////////////// DELETE //////////////////////
function deleteWorkflow(id){
	$("#message").html(tt("Delete this Workflow?"));
	$("#dialog").dialog({
		buttons: [
          {
        	  text: tt("Yes"),
		      click: function() {
		    	  $(this).dialog("close");
		    	  $.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
		    			function(data) {
		    				if(!data){
		    					$.ajax({
		    						type: "GET",
		    						url: baseUrl + "/bpm/workflow/delete",
		    						data: "id=".concat(id),
		    						cache: false,
		    					});
		    				}
		    				else {
		    					$("#message").html(tt("This Workflow is enabled for domain ")+data+tt(". This domain will not have an enabled workflow. Do you confirm?"));
		    					$("#dialog").dialog({
		    					    buttons: [
		    						    {
		    						    	text: tt("Yes"),
		    						    	click: function() {
		    						    		$(this).dialog("close");
		    						    		$.ajax({
		    						    			type: "GET",
		    						    			url: baseUrl + "/bpm/workflow/delete",
		    						    			data: "id=".concat(id),
		    						    			cache: false,
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

/////////////////////// UPDATE //////////////////////
function update(id){
	$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
		function(data) {
			if(!data){
				window.location="../workflow/update?id="+id;
			}
			else {
				$("#message").html(tt("Only disabled Workflows can be edited."));
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
			}
		}
	);
}

/////////////////////// DISABLE //////////////////////
function disableWorkflow(id){
	$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
		function(data) {
			if(data){
				$("#message").html(tt("This Workflow is enabled for domain ")+data+tt(". This domain will not have an enabled workflow. Do you confirm?"));
				$("#dialog").dialog({
					buttons: [
		              {
			              text: tt("Yes"),
					      click: function() {
					    	  $(this).dialog("close");
					    	  $.ajax({
									type: "GET",
									url: baseUrl + "/bpm/workflow/disable",
									data: "id=".concat(id),
									cache: false,
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
		}
	);
}

/////////////////////// ENABLE //////////////////////
function enableWorkflow(id){
	$.getJSON(baseUrl + "/bpm/workflow/is-active?id="+id, 
		function(data) {
			if(!data){
				$.ajax({
					type: "GET",
					url: baseUrl + "/bpm/workflow/active",
					data: "id=".concat(id),
					cache: false,
				}); 
			}
		}
	);
}

