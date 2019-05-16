/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

function timerAuths() {
	$.ajax({
	    type: "GET",
	    url: baseUrl + '/notify/service/get-count',
	    success: function(number) {
            if(number>0) $("#not_number").removeClass("label-primary").addClass("label-warning");
	    	else $("#not_number").removeClass("label-warning").addClass("label-primary");
	    	if(number != 0)
	    		$("#not_number").html(number);
	    	t = setTimeout(function() {
	    		timerAuths()
	    	}, 60000);
	    },
	});
}

var lastDate;
var count;
var waitingNext;

$(document).ready(function() {
    if (typeof disabledNotify !== 'undefined') return;

	timerAuths();
	 
	$("#not_toggle_button").click(function(){
		if($("#not_body").is(":hidden")){
			$("#not_content").html("");
			count = 0;
			$("#not_body").show();
			$("#not_loader").show();
			$.ajax({
				type: "POST",
				url: baseUrl + "/notify/service/get-all",
				cache: false,
				success: function(html) {					
					//Get the number of not displayed notifications
					$.ajax({
					    type: "GET",
					    url: baseUrl + '/notify/service/get-count',
					    success: function(number) {
					    	if(number>0) $("#not_number").removeClass("label-primary").addClass("label-warning");
					    	else $("#not_number").removeClass("label-warning");
					    	$("#not_number").html("");
					    }
					});
					
					//Get the number of pending authorizations
					$.ajax({
						type: "POST",
						url: baseUrl + "/notify/service/get-count",
						cache: false,
						success: function(number) {
							$("#authN").html(number);
						}
					});
					
			    	var info = JSON.parse(html);
			    	lastDate = info.date;
			    	if(info.more == true) count = 6;
			    	else count = -1;
			    	
					$("#not_loader").hide();
					$("#not_content").html(info.array);
					
					$('.slimScrollBar').slimScroll({ scrollTo: '0' });

					waitingNext = false;
				}
			});
		}
		else $("#not_body").hide();
		
		return false;
	});
	
	$("#not_content").slimScroll().bind('slimscroll', function(e, pos){
		if(pos == "bottom" && count>0 && !waitingNext){
	    	waitingNext = true;
        	$("#notification_loader").show();
        	$.ajax({
				type: "POST",
				url: baseUrl + "/notify/service/get-all",
				data: "date=".concat(lastDate),
				cache: false,
				success: function(html) {
					
					var info = JSON.parse(html);
			    	lastDate = info.date;
					
			    	if(info.more == true) count += 6;
			    	else count = -1;
			    	
					$("#notification_loader").hide();
					$("#not_content").append(info.array);
					waitingNext = false;
					
					$('.slimScrollBar').slimScroll({ scrollTo: '0' });
				}
			});
	    }
	});

	//Document Click hiding the popups 
	$(document).click(function(event){
	    /*if (!$("#feedback_panel").is(event.target) && $("#feedback_panel").has(event.target).length === 0){
	    	if(!$("#feedback_panel").is(":hidden")){
	    		document.getElementById("feedback_link").className='hidden';
	    		$("#feedback_panel").hide();
	    	}
	    }*/
		
	    if (!$("#not_body").is(event.target) && $("#not_body").has(event.target).length === 0){
	    	if(!$("#not_body").is(":hidden")){
	    		$("#not_body").hide();
	    	}
		}
	});
	
});