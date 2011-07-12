function initializeTimer() {
    $(".hourPicker").autocomplete({
        source: horas
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
        }
    });
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

    if ($("#repeat_chkbox").attr("checked")) {
        $("#recurrence").slideDown();
        setFreq();
        setUntilType();
    }
    else {
        $("#recurrence").slideUp();
        $("#interval_type").empty();
        $("#short_desc").empty();
        $("#until_desc").empty();
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
    var value = $("#freq").val();

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
    var time_id = "#" + where + "Time";

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