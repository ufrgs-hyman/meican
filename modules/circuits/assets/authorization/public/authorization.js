var eventsPending, eventsConfirmed;

$(document).ready(function() {
	
	$(document).on('pjax:end',   function() {
		$(".btn-accept").click(function() {
			var id = $(this).attr("id");
			accept(id);
		});
			
		$(".btn-reject").click(function() {
			var id = $(this).attr("id");
			reject(id);
		});
	});
	
	$(".btn-accept").click(function() {
		var id = $(this).attr("id");
		accept(id);
	});
	
	$(".btn-reject").click(function() {
		var id = $(this).attr("id");
		reject(id);
	});
	
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
	
	$('#calendar').fullCalendar({
		
		//defaultView : 'agendaWeek',
		
		lang: language,
		
		allDaySlot: false,

		contentHeight: 'auto',
		theme: false,
		
		eventSources: [
		               {
		                 events: jsonEvents,
		                 color: '#27567C',
		                 textColor: '#FFFFFF',
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
	
	$.getJSON(baseUrl + "/circuits/authorization/get-others?domainTop="+domain+"&reservationId="+reservationId+"&type="+2,
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
	var element;
	for(var i in jsonEvents) if(jsonEvents[i].id == id) element = jsonEvents[i];
	$('#calendar').fullCalendar('gotoDate', element.start);
	$(".fc-state-highlight").removeClass("fc-state-highlight");
	$("[data-date='" + element.start + "']").addClass("fc-state-highlight");
}

/////////////////////// ACCEPT //////////////////////
function accept(id){
	$.getJSON(baseUrl + "/circuits/authorization/is-answered?id="+id,
		function(data) {
			if(data==0){
				$("#auth-accept-modal").modal("show");

				$("#auth-accept-modal").on("click", "#cancel-btn", function (){
		            $("#auth-accept-modal").modal("hide");
		            return false;
		        });
				
				$("#auth-accept-modal").on("click", "#accept-btn", function (){ 
					var params = "id=".concat(id);
					var message = $("#auth-accept-message").val();
	                if (message && message != "") params += "&message=".concat(message);
			    	$.ajax({
			    		type: "GET",
			    		url: baseUrl + "/circuits/authorization/accept",
			    		data: params,
			    		cache: false,
			    		success: function(html) {
			    			$.pjax.defaults.timeout = false;
			    			$.pjax.reload({container:'#pjaxContainer'});
			    			$("#auth-accept-modal").modal("hide");
			    			$(".btn-accept").click(function() {
			    				var id = $(this).attr("id");
			    				accept(id);
			    			});
			    			
			    			$(".btn-reject").click(function() {
			    				var id = $(this).attr("id");
			    				reject(id);
			    			});
			    		}
			    	});
				});
			}
		}
	);
}

/////////////////////// REJECT //////////////////////
function reject(id){
	$.getJSON(baseUrl + "/circuits/authorization/is-answered?id="+id,
		function(data) {
			if(data==0){

				$("#auth-reject-modal").modal("show");

				$("#auth-reject-modal").on("click", "#cancel-btn", function (){
		            $("#auth-reject-modal").modal("hide");
		            return false;
		        });
				
				$("#auth-reject-modal").on("click", "#reject-btn", function (){ 
			    	  var params = "id=".concat(id);
			    	  var message = $("#auth-reject-message").val();
	                  if (message && message != "") params += "&message=".concat(message);
			    	  $.ajax({
			    		  type: "GET",
			    		  url: baseUrl + "/circuits/authorization/reject",
			    		  data: params,
			    		  cache: false,
			    		  success: function(html) {
			    			  $.pjax.defaults.timeout = false;
			    			  $.pjax.reload({container:'#pjaxContainer'});
			    			  $("#auth-reject-modal").modal("hide");
			    		  }
			    	  });
				});
			}
		}
	);
}

/////////////////////// ACCEPT ALL //////////////////////
function acceptAll(id, domain){
	$("#all-accept-modal").modal("show");

	$("#all-accept-modal").on("click", "#cancel-btn", function (){
        $("#all-accept-modal").modal("hide");
        return false;
    });
	
	$("#all-accept-modal").on("click", "#accept-btn", function (){
		var params = "id=".concat(id).concat("&domainTop=").concat(domain);
		var message = $("#all-accept-message").val();
        if (message && message != "") params += "&message=".concat(message);
	    $.ajax({
	    	type: "GET",
	    	url: baseUrl + "/circuits/authorization/accept-all",
	    	data: params,
	    	cache: false,
	    	success: function(html) {
	    		$.pjax.defaults.timeout = false;
	    		$.pjax.reload({container:'#pjaxContainer'});
	    		$("#all-accept-modal").modal("hide");
	    	}
	    });
	});
}

/////////////////////// REJECT ALL //////////////////////
function rejectAll(id, domain){
	$("#all-reject-modal").modal("show");

	$("#all-reject-modal").on("click", "#cancel-btn", function (){
        $("#all-reject-modal").modal("hide");
        return false;
    });
	
	$("#all-reject-modal").on("click", "#reject-btn", function (){
		var params = "id=".concat(id).concat("&domainTop=").concat(domain);
		var message = $("#all-reject-message").val();
        if (message && message != "") params += "&message=".concat(message);
	    $.ajax({
	    	type: "GET",
	    	url: baseUrl + "/circuits/authorization/reject-all",
	    	data: params,
	    	cache: false,
	    	success: function(html) {
	    		$.pjax.defaults.timeout = false;
	    		$.pjax.reload({container:'#pjaxContainer'});
	    		$("#all-reject-modal").modal("hide");
	    	}
	    });
	});
}