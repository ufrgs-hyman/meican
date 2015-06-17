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
	    url: baseUrl + '/circuits/authorization/get-number-auths',
	    success: function(data) {
	    	if(data>0)$("#numberAuths").html("<div class='full'><span >"+data+"</span></div></li>");
	    	else $("#numberAuths").html("<div class='empty'><span >"+data+"</span></div></li>");
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
	Timer in menu bar
	================================================================================================= */

function changePasswordUser() {
	$('#changePasswordForm').slideToggle();
}

$(document).ready(function() {
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

	var speed = 300;
    var containerWidth = $('.feedback-panel').outerWidth();
    var containerHeight = $('.feedback-panel').height();
    var tabWidth = $('a.feedback-link').outerWidth();
    var feedback_descrbs = {
        idea: fbtt('Describe your idea'),
        question: 'Describe your question',
        praise: 'Describe your praise',
        problem: 'Describe your problem'
    };
	
	$('.feedback_link, #MainOverlay, #closeButtonFeedback').click(function() {
		$('.feedback-panel').css('top', '10px' );
        if ($('.feedback-panel').hasClass('open')) {
            $('.feedback-panel').slideUp(this.speed).removeClass('open');
        } 
        else {
            $('.feedback-panel').slideDown(this.speed).addClass('open');
        }		
	});
	
	$('#feedback-tabs li').click(function (){
        $('#feedback-tabs li').removeClass('active');
        $(this).addClass('active');
        $('#topic_style').val($(this).attr('class').split(' ')[0]);
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
        $('#topic_emotitag_feeling').val($(this).attr('class'));
        $('#emotion_select').toggle();
        return false;
    });
    
    $('#emotion_selected').click(function(){
        $('#emotion_select').toggle();
        return false;
    });
    
    $('#feedback-panel form').submit(function() {
    	$('#topic_submit').attr("disabled", true);
    	if(!$('#topic_additional_detail').val()){
    		alert(fbtt('Please, enter a message.'));
    		$('#topic_submit').removeAttr("disabled");
    	}
    	else {
	    	$.ajax({
	            type: 'POST',
	            url: baseUrl + "/init/support/send-email",
	            data: $(this).serialize(),
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

function deleteButtonSwitch() {
	if($(':checkbox:checked').length > 0) { 
		$('#deleteButton').show();
	}
	else {
		$('#deleteButton').hide();
	}
}