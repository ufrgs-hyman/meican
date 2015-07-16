/* ====================================================================================================
   Timer in menu bar
   ================================================================================================= */

function timer() {
	 $.ajax({
	        type: "GET",
	        url: baseUrl + '/init/support/get-server-time',
	        dataType: "json",
	        success: function(data) {
	        	updateTime(data);
	        	waitTime();
	        },
	 });
}

function updateTime(data) {
	datetime = data.split(' ');
	date = datetime[0];
	dateArray = date.split("-");
	date = dateArray[2] + "/" + dateArray[1] + "/" + dateArray[0];
	time = datetime[1];
	timeArray = time.split(":");
	time = timeArray[0] + ":" + timeArray[1];
	
	$("#system_date").html(time + " (BRT)" + 
			"<br>" + date);
}

function waitTime() {
	$.ajax({
        type: "GET",
        url: baseUrl + '/init/support/wait-get-server-time',
        dataType: "json",
        success: function(data) {
        	updateTime(data);
        	waitTime();
        },
	});
}

/* ====================================================================================================
Timer of number auths icon
================================================================================================= */

function timerAuths() {
	$.ajax({
	    type: "GET",
	    url: baseUrl + '/notification/notification/get-number-notifications',
	    success: function(data) {
	    	if(data>0)$("#notification_link").html("<div class='full'><span >"+data+"</span></div></li>");
	    	else $("#notification_link").html("<div class='empty'><span >"+data+"</span></div></li>");
	    	t = setTimeout(function() {
	    		timerAuths()
	    	}, 60000);
	    },
	});
}

/* ====================================================================================================
	Flash Box
	=================================================================================================*/
function clearFlash(){
    $('#flash_box').empty();
    window.onscroll = null;
}

/* ====================================================================================================
	
	================================================================================================= */

function changePasswordUser() {
	$('#changePasswordForm').slideToggle();
}

/* ====================================================================================================
	Notification
	================================================================================================= */

var lastDate;
var count;
var waitingNext;

$(document).ready(function() {
	
	$("#notification_link").click(function(){
		if(!$("#feedback_panel").is(":hidden")){
			$("#feedback_panel").hide();
			document.getElementById("feedback_link").className='hidden';
		}
		if($("#notification_container").is(":hidden")){
			$("#notification_ul").html("");
			document.getElementById("notification_li").className='show';
			$("#notification_loader").show();
			$.ajax({
				type: "POST",
				url: baseUrl + "/notification/notification/get-notifications",
				cache: false,
				success: function(html) {
					
					//Get the number of not displayed notifications
					$.ajax({
					    type: "GET",
					    url: baseUrl + '/notification/notification/get-number-notifications',
					    success: function(number) {
					    	if(number>0)$("#notification_link").html("<div class='full'><span >"+number+"</span></div></li>");
					    	else $("#notification_link").html("<div class='empty'><span >"+number+"</span></div></li>");
					    }
					});
					
					//Get the number of pending authorizations
					$.ajax({
						type: "POST",
						url: baseUrl + "/notification/notification/get-number-authorizations",
						cache: false,
						success: function(number) {
							$("#authN").html(number);
						}
					});
					
			    	var info = JSON.parse(html);
			    	lastDate = info.date;
			    	if(info.more == true) count = 6;
			    	else count = -1;
			    	
					$("#notification_loader").hide();
					$("#notification_ul").html(info.array);

					waitingNext = false;
				}
			});
		}
		else document.getElementById("notification_li").className='hidden';
		
		$("#notification_container").fadeToggle(300, function(){});
		
		return false;
	});
	
	$('#notification_body').bind('scroll', function() {
        if($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight && count>0 && !waitingNext) {
        	waitingNext = true;
        	$("#notification_loader").show();
        	$.ajax({
				type: "POST",
				url: baseUrl + "/notification/notification/get-notifications",
				data: "date=".concat(lastDate),
				cache: false,
				success: function(html) {
					
					var info = JSON.parse(html);
			    	lastDate = info.date;
					
			    	if(info.more == true) count += 6;
			    	else count = -1;
			    	
					$("#notification_loader").hide();
					$("#notification_ul").append(info.array);
					waitingNext = false;
				}
			});
        }
    })

	//Document Click hiding the popups 
	$(document).click(function(event){
	    if (!$("#feedback_panel").is(event.target) && $("#feedback_panel").has(event.target).length === 0){
	    	if(!$("#feedback_panel").is(":hidden")){
	    		document.getElementById("feedback_link").className='hidden';
	    		$("#feedback_panel").hide();
	    	}
	    }
		
	    if (!$("#notification_container").is(event.target) && $("#notification_container").has(event.target).length === 0){
	    	if(!$("#notification_container").is(":hidden")){
				$("#notification_container").hide();
				document.getElementById("notification_li").className='hidden';
	    	}
		}
	});
	
	//Popup on click
	$(".notifications").click(function(){
	});
	
/* ====================================================================================================
	Menu Dynamic Height
	=================================================================================================*/
	
	$('#menu').css('height', $(window).height());
	
	$(window).bind('resize', function(){
        $('#menu').css('height', $(window).height()-$('#menu').offset().top-$('#system_date').height());
    });
	
	timer();
	timerAuths();
	
	
/* ====================================================================================================
	Feedback
	=================================================================================================*/

    var feedback_descrbs = {
        idea: fbtt('Describe your idea'),
        question: fbtt('Describe your question'),
        praise: fbtt('Describe your praise'),
        problem: fbtt('Describe your problem')
    };
    
    var feedback_felling = {
            sad: fbtt('Sad'),
            indifferent: fbtt('Indifferent'),
            silly: fbtt('Silly'),
            happy: fbtt('Happy')
        };
	
    $("#feedback_li").click(function(){
    	if(!$("#notification_container").is(":hidden")){
			$("#notification_container").hide();
			document.getElementById("notification_li").className='hidden';
		}
    	
		if($("#feedback_panel").is(":hidden")) document.getElementById("feedback_link").className='show';
		else document.getElementById("feedback_link").className='hidden';
			
		$("#feedback_panel").fadeToggle(300, function(){});
		
		return false;
    });
	
    $('#feedback-tabs a').click(function (){
        $('#feedback-tabs li.active').removeClass('active');
        $(this).parent().addClass('active');
        var type = $(this).parent().attr('class').split(' ')[0];
        $('#topic_style').val(type);	
        $('#topic_additional_detail').attr('placeholder', feedback_descrbs[type]);
        return false;
    });
    
    $('#feedback-tabs li.idea a').click();
    
    $('#emotion_select a').click(function(){
        $('#topic_emotitag_feeling').val(feedback_felling[$(this).attr('class')]);
        $('#emotion_select').toggle();
        return false;
    });
    
    $('#emotion_selected').click(function(){
        $('#emotion_select').toggle();
        return false;
    });

/* ====================================================================================================
	Menu
	=================================================================================================*/
    $('#menu ul li h3').parent().find('ul').css('display', 'block');
    
    $('#menu ul li h3').click(function() {
    	if ($(this).parent().find('ul').is(':visible')) {
    		$(this).parent().find('ul').slideUp();
    		$(this).children().find('span').removeClass('ui-icon ui-icon-circle-arrow-s').addClass('ui-icon ui-icon-circle-arrow-e');
    	}
    	else {
    		$(this).parent().find('ul').slideDown();
    		$(this).children().find('span').removeClass('ui-icon ui-icon-circle-arrow-e').addClass('ui-icon ui-icon-circle-arrow-s');
    	}
    });	
    
/* ====================================================================================================
	Control buttons
	=================================================================================================*/
    
    $('.deleteCheckbox').click(function() {
    	deleteButtonSwitch();
    });
    
    $("#main-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        buttons: [{
        	id:"yes-button",
        	text: $("#yes-button-label").text(),
            click: function() {
            	$("#yes-button").attr("disabled", "disabled");
            	submitDeleteForm();
            	$("#main-dialog").dialog( "close" );
        	}
        },{
        	id:"no-button",
        	text: $("#no-button-label").text(),
            click: function() {
            	$("#main-dialog").dialog( "close" );
            }
        }],
        close: function() {
        	$("#yes-button").attr("disabled", false);
        }
    });
    
    $("#deleteButton").click(function() {
    	$("#main-dialog").dialog("open");
    	return false;
    });
});

function sendFeedback(){
	$('#topic_submit').attr("disabled", true);
	if(!$('#topic_additional_detail').val()){
		alert(fbtt('Please, enter a message.'));
		$('#topic_submit').removeAttr("disabled");
	}
	else {
		$.ajax({
	        type: 'POST',
	        url: baseUrl + "/init/support/send-email",
	        data: $("#feedback_form").serialize(),
	        success: function (data) {
	            alert(data);
	            $('.feedback-panel').slideUp(this.speed).removeClass('open');
	            $('#MainOverlay').hide();
	            $('#topic_style').val(null);
	            $('#topic_additional_detail').val(null);
	            $('#topic_emotitag_feeling').val(null);
	            $('#topic_subject').val(null);
	            $('#feedback-tabs li.idea a').click();
	            $('#topic_submit').removeAttr("disabled");
	        },
	        error: function () { 
	            alert(fbtt('Problems to send, try again later'));
	            $('#topic_submit').removeAttr("disabled");
	        }
	    });
	}
}

function deleteButtonSwitch() {
	if($(':checkbox:checked').length > 0) { 
		$('#deleteButton').show();
	}
	else {
		$('#deleteButton').hide();
	}
}