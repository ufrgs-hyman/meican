var eventsPending, eventsConfirmed;

$(document).ready(function() {
	
	document.getElementById("checkPending").addEventListener("change",
		function(){
			if(document.getElementById('checkPending').checked){
				$('#calendar').fullCalendar('addEventSource', eventsPending);
				$('#calendar').fullCalendar('refetchEvents');
			}
			else{
				$('#calendar').fullCalendar('removeEventSource', eventsPending);
				$('#calendar').fullCalendar('refetchEvents');
			}
		}
	);
	
	document.getElementById("checkConfirmed").addEventListener("change",
		function(){
			if(document.getElementById('checkConfirmed').checked){
				$('#calendar').fullCalendar('addEventSource', eventsConfirmed);
				$('#calendar').fullCalendar('refetchEvents');
			}
			else{
				$('#calendar').fullCalendar('removeEventSource', eventsConfirmed);
				$('#calendar').fullCalendar('refetchEvents');
			}
		}
	);
	
	//CASO SEJA NECESSÃRIO ALTERAR PARA AGENDA
	/*document.getElementById("checkAgenda").addEventListener("change",
		function(){
			if(document.getElementById('checkAgenda').checked){
				$('#calendar').fullCalendar('changeView', 'agendaWeek');
			}
			else{
				$('#calendar').fullCalendar('changeView', 'month');
			}
		}
	);*/
	
	$('#calendar').fullCalendar({
		
		//defaultView : 'agendaWeek',
		
		allDaySlot: false,
		
		//height: 600,
		contentHeight: 'auto',
		theme: true,
		
		eventSources: [
		               {
		                 events: jsonEvents,
		                 color: '#27567C',
		                 textColor: '#D8E7F3',
		                 borderColor: '#000000',
		               },
		             ],

	    displayEventEnd: true,
	    
	    timeFormat: 'H:mm',
	    titleFormat: 'YYYY, MMMM',
	    	
	});

	$('#calendar').fullCalendar('gotoDate', jsonEvents[0].start);

	$.getJSON(baseUrl + "/circuits/authorization/get-others?domainTop="+domain+"&reservationId="+reservationId+"&type="+1,
		function(data) {
			eventsConfirmed = {
	             events: data,
	             color: '#D8E7F3',
	             textColor: '#27567C',
	            };
			$('#calendar').fullCalendar('addEventSource', eventsConfirmed);
			$('#calendar').fullCalendar('refetchEvents');
		}
	);
	
	$.getJSON(baseUrl + "/circuits/authorization/get-others?id="+domain+"&reservationId="+reservationId+"&type="+2,
		function(data) {
			eventsPending = {
				events: data,
		        color: '#EFEFEF',
		        textColor: '#27567C',
		    };
			$('#calendar').fullCalendar('addEventSource', eventsPending);
			$('#calendar').fullCalendar('refetchEvents');
		}
	);
})

/////////////////////// TO DATE //////////////////////
function toDate(id){
	$('#calendar').fullCalendar('gotoDate', jsonEvents[id].start);
	$(".fc-state-highlight").removeClass("fc-state-highlight");
	$("[data-date='" + jsonEvents[id].start + "']").addClass("fc-state-highlight");
}

/////////////////////// ACCEPT //////////////////////
function accept(id){
	$.getJSON(baseUrl + "/circuits/authorization/is-answered?id="+id,
		function(data) {
			if(data==0){
				$("#MessageImg").attr("src", baseUrl+"/images/hand_good.png");
			    $("#MessageLabel").html(tt("Request will be accepted. If you want, provide a message:"));
			
			    $("#Message").val('');
				$("#dialog").dialog("open");
				
				$("#dialog").dialog({
					buttons: [
			          {
			        	  text: "Ok",
					      click: function() {
					    	  var params = "id=".concat(id);
					    	  var message = $("#Message").val();
			                  if (message && message != "") params += "&message=".concat(message);
					    	  $.ajax({
					    		  type: "GET",
					    		  url: baseUrl + "/circuits/authorization/accept",
					    		  data: params,
					    		  cache: false,
					    		  success: function(html) {
					    			  $.pjax.defaults.timeout = false;
					    			  $.pjax.reload({container:'#pjaxContainer'});
					    		  }
					    	  });
					    	  $(this).dialog("close");
					      }
			          },
			          {
			        	  text: tt("Cancel"),
					      click: function() {
					    	  $(this).dialog( "close" );
					      }
			          },
			          
			       ]
				});
			}
	});
}

/////////////////////// REJECT //////////////////////
function reject(id){
	$.getJSON(baseUrl + "/circuits/authorization/is-answered?id="+id,
			function(data) {
				if(data==0){
					$("#MessageImg").attr("src", baseUrl+"/images/hand_bad.png");
				    $("#MessageLabel").html(tt("Request will be rejected. If you want, provide a message:"));
				
				    $("#Message").val('');
					$("#dialog").dialog("open");
					
					$("#dialog").dialog({
						buttons: [
				          {
				        	  text: "Ok",
						      click: function() {
						    	  var params = "id=".concat(id);
						    	  var message = $("#Message").val();
				                  if (message && message != "") params += "&message=".concat(message);
						    	  $.ajax({
						    		  type: "GET",
						    		  url: baseUrl + "/circuits/authorization/reject",
						    		  data: params,
						    		  cache: false,
						    		  success: function(html) {
						    			  $.pjax.defaults.timeout = false;
						    			  $.pjax.reload({container:'#pjaxContainer'});
						    		  }
						    	  });
						    	  $(this).dialog("close");
						      }
				          },
				          {
				        	  text: tt("Cancel"),
						      click: function() {
						    	  $(this).dialog( "close" );
						      }
				          },
				          
				       ]
					});
				}
	});
}

/////////////////////// ACCEPT ALL //////////////////////
function acceptAll(id, domain){
	$("#MessageImg").attr("src", baseUrl+"/images/hand_good.png");
    $("#MessageLabel").html(tt("All requests will be accepted. If you want, provide a message:"));

    $("#Message").val('');
	$("#dialog").dialog("open");
	
	$("#dialog").dialog({
		buttons: [

          {
        	  text: "Ok",
		      click: function() {
		    	  var params = "id=".concat(id).concat("&domainTop=").concat(domain);
		    	  var message = $("#Message").val();
                  if (message && message != "") params += "&message=".concat(message);
		    	  $.ajax({
		    		  type: "GET",
		    		  url: baseUrl + "/circuits/authorization/accept-all",
		    		  data: params,
		    		  cache: false,
		    		  success: function(html) {
		    			  $.pjax.defaults.timeout = false;
		    			  $.pjax.reload({container:'#pjaxContainer'});
		    		  }
		    	  });
		    	  $(this).dialog("close");
		      }
          },
          {
        	  text: tt("Cancel"),
		      click: function() {
		    	  $(this).dialog( "close" );
		      }
          },
       ]
	});
}

/////////////////////// REJECT ALL //////////////////////
function rejectAll(id, domain){
	$("#MessageImg").attr("src", baseUrl+"/images/hand_bad.png");
    $("#MessageLabel").html(tt("All requests will be rejected. If you want, provide a message:"));

    $("#Message").val('');
	$("#dialog").dialog("open");
	
	$("#dialog").dialog({
		dialogClass: "no-close",
		buttons: [
          {
        	  text: "Ok",
		      click: function() {
		    	  var params = "id=".concat(id).concat("&domainTop=").concat(domain);
		    	  var message = $("#Message").val();
                  if (message && message != "") params += "&message=".concat(message);
		    	  $.ajax({
		    		  type: "GET",
		    		  url: baseUrl + "/circuits/authorization/reject-all",
		    		  data: params,
		    		  cache: false,
		    		  success: function(html) {
		    			  $.pjax.defaults.timeout = false;
		    			  $.pjax.reload({container:'#pjaxContainer'});
		    		  }
		    	  });
		    	  $(this).dialog("close");
		      }
          },
          {
        	  text: tt("Cancel"),
		      click: function() {
		    	  $(this).dialog( "close" );
		      }
          },
       ]
	});
}