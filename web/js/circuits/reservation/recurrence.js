$(document).ready(function() {
	initializationOfView();

	$('#recurrence_enabled').on('change', function() {
		if($("#recurrence_enabled").is(":checked")) {
			$("#recurrence").slideDown(500, function(){
		        $(window).trigger('resize');
		    });
		} else {
			$("#recurrence").slideUp(500, function(){
		        $(window).trigger('resize');
		    });
		}
	});
});

function initializationOfView(){
	var currentdate = new Date();	
	if(currentdate.getHours()==23 && currentdate.getMinutes() >= 30) currentdate.setMinutes(currentdate.getMinutes()+(59-currentdate.getMinutes()));
	else currentdate.setMinutes(currentdate.getMinutes()+30);
	  	  
	var hours = currentdate.getHours(),
	  	minutes = currentdate.getMinutes();
	var day = currentdate.getDate(),
		month = currentdate.getMonth()+1,
		year = currentdate.getFullYear();
	  
	var startTime = pad(hours) + ':' + pad(minutes);
	$("#start-time").val(startTime);
	$("#start-date").val(pad(day) + '/' + pad(month) + '/' + pad(year));
	
	$("#rec-start-date").val(pad(day) + '/' + pad(month) + '/' + pad(year));
	$("#rec-finish-date").val(pad(day) + '/' + pad(month) + '/' + pad(year));
	
	proposedDate = currentdate;
	proposedDate.setHours(currentdate.getHours()+1);
	
	hours = proposedDate.getHours();
	minutes = proposedDate.getMinutes();

	day = proposedDate.getDate();
	month = proposedDate.getMonth()+1;
	year = proposedDate.getFullYear();

	$("#finish-time").val(pad(hours) + ':' + pad(minutes));
	$("#finish-date").val(pad(day) + '/' + pad(month) + '/' + pad(year));

	$("#duration").html(diffDate($("#start-time").val(),$("#finish-time").val(),$("#start-date").val(),$("#finish-date").val()));
	  
	/* Inicializating Summary */
	var startTime = $("#start-time").val();
	var startDate = $("#start-date").val();
	var finishTime = $("#finish-time").val();
	var finishDate = $("#finish-date").val();
		
	summary = tt("Active from") + " " + startDate + " " + tt("at") + " " + startTime;
	summary = summary + " " + tt("until") + " " + finishDate + " " + tt("at") + " " + finishTime;
	  
	$("#summary").html(summary);
	
	/** Radioboxes "Everyday","Weekly" and "Monthly" **/
	$("input[name$='ReservationForm[rec_type]']").click(function() {
		var value = $(this).val();
		var intervalValue = $('#rec-interval').val();
			
		switch(value){
		case 'D':
			$("#recurrence-weekdays").hide();
			if(intervalValue > 1)
				$('#interval_type').html(tt('days'));
			else
				$('#interval_type').html(tt('day'));
			break;
			case 'W':
				$("#recurrence-weekdays").show();
				if(intervalValue > 1)
					$('#interval_type').html(tt('weeks'));
				else
					$('#interval_type').html(tt('week'));
			break;
			case 'M':
				$("#recurrence-weekdays").hide();
				if(intervalValue > 1)
					$('#interval_type').html(tt('months'));
				else
					$('#interval_type').html(tt('month'));
			break;
		}
	});
	  
	/** Radioboxes from Range of recurrence **/
	$("#rec-finish-occur-limit-radio").click(function() {
		$('#rec-finish-occur-limit').removeAttr("disabled");
		$('#rec-finish-date').attr("disabled", "disabled");
	});
	
	$("#rec-finish-date-radio").click(function() {
		$('#rec-finish-occur-limit').attr("disabled", "disabled");
		$('#rec-finish-date').removeAttr("disabled");
	});
	  
	/** Dropbox changes the label in its right side **/
	$("#rec-interval").change(function() {
		var intervalValue = $(this).val();
		var intervalType = $("input[name='ReservationForm[rec_type]']:checked").val();
		
		switch(intervalType){
			case 'D':
				if(intervalValue > 1)
					$('#interval_type').html(tt('days'));
				else
					$('#interval_type').html(tt('day'));
			break;
			case 'W':
				if(intervalValue > 1)
					$('#interval_type').html(tt('weeks'));
				else
					$('#interval_type').html(tt('week'));
			break;
			case 'M':
				if(intervalValue > 1)
					$('#interval_type').html(tt('months'));
				else
					$('#interval_type').html(tt('month'));
			break;
			default:
				$('#interval_type').html(tt('error'));
			break;
		}
	});
	  
	/** Summary javascript treatment **/
	$("input[name$='ReservationForm[rec_type]'], #recurrence_enabled, #rec-interval, #rec-finish-date, input[name$='ReservationForm[rec_finish_type]']," +
			" #recurrence-weekdays input, #rec-finish-occur-limit, #start-time, #start-date, #finish-time, #finish-date").change(function(e){
		if($('#recurrence_enabled').is(":checked")){
			var summary = tt("Repeat every") + " ";
	  		var intervalType = $("input[name='ReservationForm[rec_type]']:checked").val();
	  		var intervalValue = $("#rec-interval").val();
	  		
	  		switch(intervalType){
				case 'D':
					if(intervalValue > 1)
						summary = summary + intervalValue + " " + tt("days") + ", ";
					else
						summary = summary + tt("day") + ", ";
				break;
				case 'W':
					if(intervalValue > 1)
						summary = summary + intervalValue + " " + tt("weeks");
					else
						summary = summary + tt("week");

					var weekdaysId = $("#recurrence-weekdays input:checked").map(function(i, el) { return $(el).attr("id"); }).get();
					
					if(weekdaysId.length != 0){
						summary = summary + " " + tt("on") + " ";
						
						for(cont = 0; cont < weekdaysId.length; cont++){
							summary = summary + tt(weekdaysId[cont]) + ", ";
						}
					}
					else summary = summary + ", ";
				break;
				case 'M':
					if(intervalValue > 1)
						summary = summary + intervalValue + " " + tt("months") + ", ";
					else
						summary = summary + tt("month") + ", ";
				break;
				default:
					alert('error');
				break;
	  		}
	  		
	  		if($("input[name$='ReservationForm[rec_finish_type]']:checked").val() === 'occur-limit'){
	  			repeatedTimes = $('#rec-finish-occur-limit').val();
	  			
	  			if(repeatedTimes > 1)
	  				summary = summary + repeatedTimes + " " + tt("times");
	  			else
	  				summary = summary + repeatedTimes + " " + tt("time");
	  			
	  		}else{
	  			untilDate = $("#rec-finish-date").val();
	  			summary = summary + tt("until") + " " + untilDate;
	  		}
	  	}
	  	else{
	  		var startTime = $("#start-time").val();
	  		var startDate = $("#start-date").val();
	  		var finishTime = $("#finish-time").val();
	  		var finishDate = $("#finish-date").val();
	  		
	  		summary = tt("Active from") + " " + startDate + " " + tt("at") + " " + startTime;
	  		summary = summary + " " + tt("until") + " " + finishDate + " " + tt("at") + " " + finishTime;
	  	}
	  	
	  	$("#summary").html(summary);
	});
	
	var dates = $("#start-date, #finish-date").datepicker({
					dateFormat: 'dd/mm/yy',
					prevText: tt('Previous'),
					nextText: tt('Next'),
					monthNames: tt(['January','February','March','April','May','June',
					             'July','August','September','October','November','December']),
					monthNamesShort: tt(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					                  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']),
					dayNames: tt(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
					dayNamesShort: tt(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']),
					dayNamesMin: tt(['Su','Mo','Tu','We','Th','Fr','Sa']),
			        showWeek: false,
			        changeMonth: true,
			        changeYear: true,
			        minDate: $("#start-date").val(),
			        duration: 'normal',
			        onSelect: function( selectedDate ) {
			        	var option;
			        	if (this.id == "start-date") {
			        		$("#rec-start-date").val($("#start-date").val());
			        		option = "minDate";
			        	} else {
			        		if (!isValidDurationDate($("#finish-date").val(), $("#rec-finish-date").val())) {
			        			$("#rec-finish-date").val($("#finish-date").val());
			        		}
			        		$("#rec-finish-date").datepicker('option', "minDate", $("#finish-date").val());
			        		option = "maxDate";
			        	}
			        	
			            var instance = $( this ).data( "datepicker" );
			            var date = $.datepicker.parseDate(
			            instance.settings.dateFormat ||
			                $.datepicker._defaults.dateFormat,
			            selectedDate, instance.settings );
			            dates.not( this ).datepicker( "option", option, date );
			            
			            $('#'+this.id).trigger("change");
			    }});
	
	$("#rec-finish-date").datepicker({
		dateFormat: 'dd/mm/yy',
		prevText: tt('Previous'),
		nextText: tt('Next'),
		monthNames: tt(['January','February','March','April','May','June',
		             'July','August','September','October','November','December']),
		monthNamesShort: tt(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
		                  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']),
		dayNames: tt(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
		dayNamesShort: tt(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']),
		dayNamesMin: tt(['Su','Mo','Tu','We','Th','Fr','Sa']),
        showWeek: false,
        changeMonth: true,
        changeYear: true,
        minDate: $("#finish-date").val(),
        duration: 'normal',
    });
	
	
	$("#start-date, #start-time, #finish-date, #finish-time").change(function(e){
		if(e.target.value != ""){
			var message = diffDate($("#start-time").val(),$("#finish-time").val(),$("#start-date").val(),$("#finish-date").val());
			  
			if(message != null)
				$("#duration").html(message);
			else{
				if(e.target.id == 'finish-date' || e.target.id == 'finish-time'){
					var finishTime = $("#finish-time").val().split(":");
					var finishDate = $("#finish-date").val().split("/");
					
					var intYear = parseInt(finishDate[2]);
					var intMonth = parseInt(finishDate[1]) - 1;
					var intDay = parseInt(finishDate[0]);
					var intHour = parseInt(finishTime[0]) - 1;
					var intMinutes = parseInt(finishTime[1]);
					
					startDate = new Date(intYear, intMonth, intDay, intHour, intMinutes, 0, 0);
					
					var year = startDate.getFullYear();
					var month = startDate.getMonth()+1;
					var day = startDate.getDate();
					var hours = startDate.getHours();
					var minutes = startDate.getMinutes();
							  
					$("#start-time").val(pad(hours) + ":" + pad(minutes));
					//$("#initialDate").val(pad(day) + "/" + pad(month) + "/" + initialDate.getFullYear());
					
				}else if(e.target.id == 'start-date' || e.target.id == 'start-time'){
					
					var startTime = $("#start-time").val().split(":");
					var startDate = $("#start-date").val().split("/");
					
					var intYear = parseInt(startDate[2]);
					var intMonth = parseInt(startDate[1]) - 1;
					var intDay = parseInt(startDate[0]);
					var intHour = parseInt(startTime[0]) + 1;
					var intMinutes = parseInt(startTime[1]);
					
					finishDate = new Date(intYear, intMonth, intDay, intHour, intMinutes, 0, 0);
					
					var year = finishDate.getFullYear();
					var month = finishDate.getMonth()+1;
					var day = finishDate.getDate();
					var hours = finishDate.getHours();
					var minutes = finishDate.getMinutes();
					
					$("#finish-time").val(pad(hours) + ":" + pad(minutes));
					$("#finish-date").val(pad(day) + "/" + pad(month) + "/" + finalDate.getFullYear());
				}
				
				message = diffDate($("#start-time").val(),$("#finish-time").val(),$("#start-date").val(),$("#finish-date").val());
				$("#duration").html(message);
			}
		} 
	});
}
	
/** This function takes 4 strings (2 time-strings and 2 date-strings) as arguments and calculates the difference between them 
*  Returns a string when the second date is bigger than the first date and Returns NULL when the first is bigger than the second one**/
function diffDate(firstTime, secondTime, firstDate, secondDate){
	var daysInMonths = [31,28,31,30,31,30,31,31,30,31,30,31];
	var firstHourSplit = firstTime.split(":"),
		secondHourSplit = secondTime.split(":"),
		firstDateSplit = firstDate.split("/"),
		secondDateSplit = secondDate.split("/");
		
	var initialMinute = parseInt(firstHourSplit[1]),
		initialHour = parseInt(firstHourSplit[0]),
		initialDay = parseInt(firstDateSplit[0]),
		initialMonth = parseInt(firstDateSplit[1]),
		initialYear = parseInt(firstDateSplit[2]);
		
	var finalMinute = parseInt(secondHourSplit[1]),
		finalHour = parseInt(secondHourSplit[0]),
		finalDay = parseInt(secondDateSplit[0]),
		finalMonth = parseInt(secondDateSplit[1]),
		finalYear = parseInt(secondDateSplit[2]);
		
	var diffminutes = parseInt(secondHourSplit[1]) - parseInt(firstHourSplit[1]),
		diffhours = parseInt(secondHourSplit[0]) - parseInt(firstHourSplit[0]);
			
	var	diffdays = parseInt(secondDateSplit[0]) - parseInt(firstDateSplit[0]),
		diffmonths = parseInt(secondDateSplit[1]) - parseInt(firstDateSplit[1]),
		diffyears = parseInt(secondDateSplit[2]) - parseInt(firstDateSplit[2]);
	
	
		
	/** This veryfies if the second date is before of the first date **/
	if(!(diffyears < 0 || (diffyears === 0 && diffmonths < 0) || (diffyears === 0 && diffmonths === 0 && diffdays < 0) || (diffyears === 0 && diffmonths === 0 && diffdays === 0 && diffhours < 0) || (diffyears === 0 && diffmonths === 0 && diffdays === 0 && diffhours === 0 && diffminutes < 0))){
			
	if(diffminutes < 0){
		diffminutes = 60 + diffminutes;
		diffhours = diffhours - 1;
	}
	if(diffhours < 0){
		diffhours = 24 + diffhours;
		diffdays = diffdays - 1;
	}
	if(diffdays < 0){
		/** This IF calculates if the month is February and if the year is a Leap Year**/
		if(initialMonth == 2 && ((initialYear) % 4 === 0)){
			if((initialYear) % 100 === 0){
				if((initialYear) % 400 === 0) diffdays = daysInMonths[initialMonth - 1] + diffdays + 1;
				else diffdays = daysInMonths[initialMonth - 1] + diffdays;
			}
			else diffdays = daysInMonths[initialMonth - 1] + diffdays + 1;
		}
		else diffdays = daysInMonths[initialMonth - 1] + diffdays;
		diffmonths = diffmonths - 1;
	}
	if(diffmonths < 0){
		diffmonths = 12 + diffmonths;
		diffyears = diffyears - 1;
	}
	
	diffmonths = diffmonths + (diffyears * 12);
	while(diffmonths>0){
		initialMonth++;
		diffmonths--;
		if(initialMonth>12){
			initialMonth = 1;
			initialYear++;
		}
		/** This IF calculates if the month is February and if the year is a Leap Year**/
		if(initialMonth == 2 && ((initialYear) % 4 === 0)){
			if((initialYear) % 100 === 0){
				if((initialYear) % 400 === 0) diffdays = diffdays + daysInMonths[((initialMonth-1) % 12)] + 1;
				else diffdays = diffdays + daysInMonths[((initialMonth-1) % 12)];
			}
			else diffdays = diffdays + daysInMonths[((initialMonth-1) % 12)] + 1;
		}
		else {
			/** This IF calculates if the month is February and if the year is a Leap Year**/
			if(initialMonth-1 == 2 && ((initialYear) % 4 === 0)){
				if((initialYear) % 100 === 0){
					if((initialYear) % 400 === 0) diffdays++;
				}
				else diffdays++
			}
			if(initialMonth - 2 < 0) diffdays = diffdays + daysInMonths[11];
			else diffdays = diffdays + daysInMonths[((initialMonth-2) % 12)];
		}
	}
	
	/*for(initialMonth = initialMonth + 1; initialMonth < (finalMonth + (diffyears * 12)); initialMonth++)
		/** This IF calculates if the month is February and if the year is a Leap Year**/
		/*if(initialMonth % 12 === 1 && ((initialYear + Math.floor(initialMonth / 12)) % 4 === 0 && !((initialYear + Math.floor(initialMonth / 12)) % 100)))
			diffdays = diffdays + daysInMonths[((initialMonth - 1) % 12)] + 1;
		else
			diffdays = diffdays + daysInMonths[((initialMonth - 1) % 12)];*/
		
	/** Creating the message **/
	var message = tt("Duration") + ": ";
	if(diffdays > 1){
		message = message + diffdays + " " + tt("days");
	if(diffhours !== 0 || diffminutes !== 0)
		message = message + ", ";
	}else if(diffdays !== 0){
		message = message + diffdays + " " + tt("day");
	if(diffhours !== 0 || diffminutes !== 0)
		message = message + ", ";
	}
		
	if(diffhours > 1){
		message = message + diffhours + " " + tt("hours");
	if(diffminutes !== 0)
		message = message + " " + tt("and") + " ";
	}else if(diffhours !== 0){
		message = message + diffhours + " " + tt("hour");
	if(diffminutes !== 0)
		message = message + " " + tt("and") + " ";
	}
		
	if(diffminutes > 1){
		message = message + diffminutes + " " + tt("minutes");
	}else if (diffminutes !== 0){
		message = message + diffminutes + " " + tt("minute");
	}
		
	return message;
	
	}else{
		return false;
	}
			
}

function isValidDurationDate(firstDate, secondDate){
	var daysInMonths = [31,28,31,30,31,30,31,31,30,31,30,31];
	
	var firstDateSplit = firstDate.split("/");
		secondDateSplit = secondDate.split("/");
		
	var	initialDay = parseInt(firstDateSplit[0]),
		initialMonth = parseInt(firstDateSplit[1]),
		initialYear = parseInt(firstDateSplit[2]);
		
	var	finalDay = parseInt(secondDateSplit[0]),
		finalMonth = parseInt(secondDateSplit[1]),
		finalYear = parseInt(secondDateSplit[2]);
		
	var	diffdays = parseInt(secondDateSplit[0]) - parseInt(firstDateSplit[0]),
		diffmonths = parseInt(secondDateSplit[1]) - parseInt(firstDateSplit[1]),
		diffyears = parseInt(secondDateSplit[2]) - parseInt(firstDateSplit[2]);
		
	/** This veryfies if the second date is before of the first date **/
	if(!(diffyears < 0 || (diffyears === 0 && diffmonths < 0) || (diffyears === 0 && diffmonths === 0 && diffdays < 0) || (diffyears === 0 && diffmonths === 0 && diffdays === 0) || (diffyears === 0 && diffmonths === 0 && diffdays === 0))){
			
	if(diffdays < 0){
		diffdays = daysInMonths[initialMonth - 1] + diffdays;
		diffmonths = diffmonths - 1;
	}
	if(diffmonths < 0){
		diffmonths = 12 + diffmonths;
		diffyears = diffyears - 1;
	}
			
	for(initialMonth = initialMonth; initialMonth < (finalMonth + (diffyears * 12)); initialMonth++)
		/** This IF calculates if the month is February and if the year is a Leap Year**/
		if(initialMonth % 12 === 1 && ((initialYear + Math.floor(initialMonth / 12)) % 4 === 0 && !((initialYear + Math.floor(initialMonth / 12)) % 100)))
			diffdays = diffdays + daysInMonths[((initialMonth - 1) % 12)] + 1;
		else
			diffdays = diffdays + daysInMonths[((initialMonth - 1) % 12)];
		
		return true;
		
		}else{
			return false;
		}
}

/** This function corrects the 2 digits formats for integers in general **/
function pad(d) {
	return (d < 10) ? '0' + d.toString() : d.toString();
}
	
	
