function initializeTimer() {
    $(".hourPicker").timePicker({
        show24Hours: true,
        separator: ':',
        step: 30
    });
   
    $(".hourPicker").change(function() {
        clearFlash();
        validateTime($(this).attr("id"));
        calcDuration();
        if (!($("#repeat_chkbox").attr("checked"))) {
            refreshSummary();
        }
        if (($("#initialTime").val()) < ($("#finalTime").val())) {
            $("#confirmation_initialDate").html("Date: " + $("#initialDate").val());
            $("#confirmation_initialTime").html("Time: " + $("#initialTime").val());
            $("#confirmation_finalDate").html("Date: " + $("#finalDate").val());
            $("#confirmation_finalTime").html("Time: " + $("#finalTime").val());
            $("#confirmation_duration").html($("#duration").html());  
        } else {
        }
    });        

    $.datepicker.setDefaults($.datepicker.regional[language]);
    var dates = $("#initialDate, #finalDate").datepicker({
        dateFormat: date_format,
        showWeek: false,
        changeMonth: true,
        changeYear: true,
        minDate: $("#initialDate").val(),
        duration: 'normal',
        onSelect: function( selectedDate ) {
            var option = this.id == "initialDate" ? "minDate" : "maxDate",
            instance = $( this ).data( "datepicker" ),
            date = $.datepicker.parseDate(
            instance.settings.dateFormat ||
                $.datepicker._defaults.dateFormat,
            selectedDate, instance.settings );
            dates.not( this ).datepicker( "option", option, date );
            calcDuration();
        }
    });
    calcDuration();
    refreshSummary();
    $("#confirmation_initialDate").html("Date: " + $("#initialDate").val());
    $("#confirmation_initialTime").html("Time: " + $("#initialTime").val());
    $("#confirmation_finalDate").html("Date: " + $("#finalDate").val());
    $("#confirmation_finalTime").html("Time: " + $("#finalTime").val());
    $("#confirmation_duration").html($("#duration").html());            
}

function refreshSummary() {
    if ($("#initialTime").val() < ($("#finalTime").val())) {
        var summary_string = active_string + " " + $("#initialDate").val() + " " + at_string + " " + $("#initialTime").val() + " " + until_string + 
            " " + $("#finalDate").val() + " " + at_string + " " + $("#finalTime").val();
                     
        $("#summary").html(summary_string);
        $("#confirmation_summary").html(summary_string);
    } else {
        $("#summary").html("");
    }
}

function showRecurrenceBox() {
    $("#untilDate").datepicker({
        dateFormat: date_format,
        showWeek: false,
        changeMonth: true,
        changeYear: true,
        minDate: $("#finalDate").val(),
        onSelect: function( selectedDate ) {
            setUntilType();
            instance = $( this ).data( "datepicker" ),
            date = $.datepicker.parseDate(
            instance.settings.dateFormat ||
                $.datepicker._defaults.dateFormat,
            selectedDate, instance.settings );
            $("#initialRecurrence").datepicker("option", "maxDate", date);
        }
    });
    
    $("#initialRecurrence").datepicker({
        dateFormat: date_format,
        showWeek: false,
        changeMonth: true,
        changeYear: true,
        minDate: $("#initialDate").val(),
        onSelect: function( selectedDate ) {
            instance = $( this ).data( "datepicker" ),
            date = $.datepicker.parseDate(
            instance.settings.dateFormat ||
                $.datepicker._defaults.dateFormat,
            selectedDate, instance.settings );
            $("#untilDate").datepicker("option", "minDate", date);
        }
    });
    
    if ($("#initialRecurrence").val() == "") {
        $("#initialRecurrence").val($("#initialDate").val());
    }
    
    if ($("#untilDate").val() == "") {
        $("#untilDate").val($("#initialRecurrence").val());
    }

    if ($("#repeat_chkbox").attr("checked")) {
        $("#auxDiv").show();
        $("#recurrence").show();
        setFreq();
        setUntilType();
        $("#rec_initialTime").html($("#initialTime").val());
        $("#rec_finalTime").html($("#finalTime").val());
        $("#rec_duration").html($("#duration").html());        
    }
    else {
        $("#auxDiv").hide();
        $("#recurrence").hide();
        $("#recurrence-edit").hide();
        $("#interval_type").empty();
        $("#short_desc").empty();
        $("#until_desc").empty();
        $("#recurrence_summary").empty();
        refreshSummary();
        clearWeekConf();
    }
}

function setSummary(sing_string, plural_string, opt_string) {
    var aditional_string = null;

    if (opt_string)
        aditional_string = ' ' + opt_string;
    else
        aditional_string = ',';

    if ($("#interval").val() == 1) {
        $("#interval_type").html(sing_string);
        $("#short_desc").html(repeat_every_string + ' ' + sing_string + aditional_string);
    } else {
        $("#interval_type").html(plural_string);
        $("#short_desc").html(repeat_every_string + ' ' + $("#interval").val() + ' ' + plural_string + aditional_string);
    }
}

function clearWeekConf() {
    var weekdays = ["#Sunday","#Monday","#Tuesday","#Wednesday","#Thursday","#Friday","#Saturday"];

    $("#weekdays").hide();

    for (var i in weekdays) {
        $(weekdays[i]).removeAttr("checked");
        $(weekdays[i] + "_desc").empty();
    }
}

/**
 * Sets the frequency type - DAILY, WEEKLY, MONTHLY
 */
function setFreq() {
    var value = $("input[name='freq']:checked").val();
    if (typeof value == 'undefined') {
        value = "DAILY";
        $("input[name='freq']").filter('[value=DAILY]').attr('checked', true);
    }
    switch (value) {
        case "DAILY":
            clearWeekConf();
            setSummary(day_string, days_string);
            break;
        case "WEEKLY":
            $("#weekdays").show();
            if (today) {
                $("#" + today).attr("checked",true);
                checkWeekDay(today);
            }
            setSummary(week_string, weeks_string, on_string);
            break;
        case "MONTHLY":
            clearWeekConf();
            setSummary(month_string, months_string);
            break;
        default:
            clearWeekConf();
            break;
    }
}

/**
 * Sets the type of end rule of recurrence - until (define a date) or count (# of occurrences)
 */
function setUntilType() {
    if ($("#recur_radio").attr("checked")) {
        $("#nr_occurr").removeAttr("disabled");
        $("#untilDate").attr("disabled", "disabled");
        if ($("#nr_occurr").val() == 1) {
            $("#until_desc").html($("#nr_occurr").val() + ' ' + time_string);
            //$("#short_desc").empty();
            //$("#until_desc").html("Only once");
        } else
            $("#until_desc").html($("#nr_occurr").val() + ' ' + times_string);
    }
    else if ($("#date_radio").attr("checked")) {
        $("#untilDate").removeAttr("disabled");
        $("#nr_occurr").attr("disabled", "disabled");
        $("#until_desc").html(until_string + ' ' + $("#untilDate").val());
    } else {
        $("#untilDate").attr("disabled", "disabled");
        $("#nr_occurr").attr("disabled", "disabled");
    }
}

function checkWeekDay(day_name) {
    var desc_id = "#" + day_name + "_desc";
    if ($("#" + day_name).attr("checked")) {
        var title = $("#" + day_name).attr("title");
        $(desc_id).html(title + ",");
    } else {
        $(desc_id).empty();
    }
}

function validateTime(where) {
    var time_id = "#" + where;
    
    /** 
     * @todo: melhorar regexpr, procurar internet
     */
    var valid_time = /^[0-2][0-9]:[0-5][0-9]$/;
    //var valid_time = /^([0-1][0-9])|([2][0-3]):[0-5][0-9]$/;
    if (valid_time.test($(time_id).val())) {
        return true;
    } else {
        setFlash(invalid_time_string + ": " + $(time_id).val());
        return false;
    }
}

function getCheckedDays() {
    var weekdays = ["#Sunday","#Monday","#Tuesday","#Wednesday","#Thursday","#Friday","#Saturday"];

    var j=0;
    var checkedDays = new Array();

    for (var i in weekdays) {
        if ($(weekdays[i]).attr("checked")) {
            checkedDays[j] = $(weekdays[i]).val();
            j++;
        }
    }

    return checkedDays;
}

function saveTimer(timer_id) {
    setFlash("");

    var name = $("#name").val();
    if (!name) {
        setFlash(set_name_string);
        return;
    }
    
    if (!(validateTime("initial") && validateTime("final"))) {
        return;
    }

    if ($("#repeat_chkbox").attr("checked")) {
        var freq = $("#freq").val();

        if ($("#date_radio").attr("checked")) {
            var until = $("#untilDate").val();
        } else if ($("#recur_radio").attr("checked"))
            var count = $("#nr_occurr").val();
        else {
            setFlash(end_rule_string);
            return;
        }

        var interval = $("#interval").val();

        var week_str = "";
        if (freq == "WEEKLY") {
            var byday = getCheckedDays();
            if (byday.length == 0) {
                setFlash(select_day_string);
                return;
            }
            byday = byday.toString();

            var weekdays = ["#Sunday_desc","#Monday_desc","#Tuesday_desc","#Wednesday_desc","#Thursday_desc","#Friday_desc","#Saturday_desc"];

            for (var i in weekdays) {
                if ($(weekdays[i]).html())
                    week_str += $(weekdays[i]).html() + " ";
            }
        }

        var sum_desc = $("#short_desc").html() + " ";
        sum_desc += week_str;
        sum_desc += $("#until_desc").html();
        $("#recurrence_summary").html(sum_desc);
    }
    
    $.post("main.php?app=circuits&controller=timers&action=update", {
        tmr_id: timer_id,
        name: name,
        start_date: $("#initialDate").val(),
        start_time: $("#initialTime").val(),
        finish_date: $("#finalDate").val(),
        finish_time: $("#finalTime").val(),
        freq: freq,
        until_date: until,
        count: count,
        interval: interval,
        byday: byday,
        summary: sum_desc
    }, function(data) {
        loadHtml(data);
    });
}

function fillRecurrenceBox() {
    if (recurrence) {
        if (recurrence.freq == "WEEKLY") {
            today = null;

            var byday = recurrence.byday.split(",");
            for (var i in byday) {
                var weekday = "";
                switch (byday[i]) {
                    case "SU":
                        weekday = "Sunday";
                        break;
                    case "MO":
                        weekday = "Monday";
                        break;
                    case "TU":
                        weekday = "Tuesday";
                        break;
                    case "WE":
                        weekday = "Wednesday";
                        break;
                    case "TH":
                        weekday = "Thursday";
                        break;
                    case "FR":
                        weekday = "Friday";
                        break;
                    case "SA":
                        weekday = "Saturday";
                        break;
                    default:
                        break;
                }
                $("#" + weekday).attr("checked",true);
                checkWeekDay(weekday);
            }
        }

        if (recurrence.count) {
            $("#recur_radio").attr("checked",true);
            $("#date_radio").removeAttr("checked");
        } else if (recurrence.until) {
            $("#date_radio").attr("checked",true);
            $("#recur_radio").removeAttr("checked");
        }

        $("#repeat_chkbox").attr("checked",true);
        showRecurrenceBox();
    }
}

function calcDuration(){
    var duration = "";
    var idate, fdate = [];
    var itime, ftime = [];
    
    idate = $("#initialDate").val().split("/");
    fdate = $("#finalDate").val().split("/");
    
    itime = $("#initialTime").val().split(":");
    ftime = $("#finalTime").val().split(":");
    

    if ((($("#initialTime").val() != "") && ($("#finalTime").val() != "")) &&
        ($("#initialDate").val() != "") && ($("#finalDate").val() != "")) {

        var start = new Date(idate[2], idate[1], idate[0], itime[0], itime[1], 0, 0);
        var end = new Date(fdate[2], fdate[1], fdate[0], ftime[0], ftime[1], 0, 0);

        var difference = end - start;
        
        if (difference < 0) {
            setFlash(flash_timerInvalid);
            tab2_valid = false;
            validateTab3();
            $("#confirmation_summary").html("");
            $("#confirmation_initialTime").html("");
            $("#confirmation_finalTime").html("");
            $("#confirmation_duration").html("");
            $("#duration").html("");
            return;
        } else if (difference == 0){
            setFlash(flash_invalidDuration);
            tab2_valid = false;
            validateTab3();
            $("#confirmation_summary").html("");
            $("#confirmation_initialTime").html("");
            $("#confirmation_finalTime").html("");
            $("#confirmation_duration").html("");          
            $("#duration").html("");
            return
        }
        clearFlash();
        tab2_valid = true;
        validateTab3();
        var total_minutes = Math.round(difference/(1000*60)); //diferenca em minutos
        
        var total_hours = parseInt((total_minutes/60));
        var minutes = total_minutes - (total_hours*60);
        
        var total_days = parseInt((total_hours/24));
        var hours = total_hours - (total_days*24);


        if (total_days > 0) {
            if (hours > 0) {
                if (minutes > 0) {
                    if (total_days == 1) {
                        duration += total_days + " " + day_string + ", ";
                    } else {
                        duration += total_days + " " + days_string + ", ";
                    }                    
                } else {
                    if (total_days == 1) {
                        duration += total_days + " " + day_string + " " + and_string + " ";
                    } else {
                        duration += total_days + " " + days_string + " " + and_string + " ";
                    }                    
                }
            } else {
                if (minutes > 0) {
                    if (total_days == 1) {
                        duration += total_days + " " + day_string + " " + and_string + " ";
                    } else {
                        duration += total_days + " " + days_string + " " + and_string + " ";
                    }                                        
                } else {
                    
                    if (total_days == 1) {
                        duration += total_days + " " + day_string;
                    } else {
                        duration += total_days + " " + days_string;
                    }                
                }
            }
        } 
        
        if (hours > 0) {
            if (minutes > 0) {
                if (hours == 1) {
                    duration += hours + " " + hour_string + " " + and_string +" ";
                } else {
                    duration += hours + " " + hours_string + " " + and_string +" ";
                }
            } else {
                if (hours == 1) {
                    duration += hours + " " + hour_string;
                } else {
                    duration += hours + " " + hours_string;
                }                
            }
        }        
        
        if (minutes > 0) {
            if (minutes == 1) {
                duration += minutes + " " + minute_string;
            } else {
                duration += minutes + " " + minutes_string;
            }
        }        
    } 
    
    $("#duration").html(duration); 
}