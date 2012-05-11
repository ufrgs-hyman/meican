var request = {};

(function() {
    var actionUrl = null;
    var response = null;
    var bandwidth = null;
    var gris = null;
    
    this.reply = function(response, availableBandwidth) {
        //var message = prompt();
        if (response=="accept"){
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_good.png");
            $("#MessageLabel").html(accept_message + ": ");
        } else {
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_bad.png");
            $("#MessageLabel").html(reject_message + ": ");
        }
        $("#Message").val('');
        $("#MessageBandwidth").html(available_bandwidth_string + ': ' + availableBandwidth + ' Mbps.<br/>' + requested_bandwidth_string + ': '+this.bandwidth+' Mbps.');
        this.response = response;
        $('#Response').html(response);
        $("#dialog-form").dialog("open");
    };

    this.setBandwidth = function(v){
        this.bandwidth = v;
    };
    
    this.setGris = function(gris){
        this.gris = gris;
    };
    
    this.set = function (args){
        for (v in args){
            this[v] = args[v];
        }
    };

    this.setActionUrl = function(url){
        this.actionUrl = url;
        var request = this;
        $("#dialog-form").dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            buttons: [
            {
                text: ok_string,
                click: function() {
                    var message = $("#Message").val();
                    if (message && message != "")
                        $.navigate({
                            type: "POST",
                            url: $('#UrlPost').attr('href'),
                            data: {
                                response: $('#Response').html(), 
                                message: message
                            }
                        });
                    $(this).dialog("close");
                }
            },
            {
                text: cancel_string,
                click: function() {
                    $(this).dialog("close");
                }
            }]
        });
    };
    
    this.buildCalendar = function(){
        
        var $calendar = $('#calendar');
        var id = 10;

        $calendar.weekCalendar({
            buttons: true,
            timeslotsPerHour : 2,
            allowCalEventOverlap : true,
            overlapEventsSeparate: true,
            businessHours : false,/*{start: 8, end: 18, limitDisplay: false },*/
            daysToShow : 7,
            timeslotHeight: 15,
            useShortDayNames: true,
            use24Hour: true,
            height : function($calendar) {
                return 400;
            //return $(window).height() - $("h1").outerHeight() - 1;
            },
            eventAfterRender : function(calEvent, $element) {
                /*if (calEvent.end.getTime() < new Date().getTime()) {
    $event.css("backgroundColor", "#aaa");
    $event.find(".wc-time").css({
       "backgroundColor" : "#999",
       "border" : "1px solid #888"
    });
 }*/
                $element.attr('title', calEvent.title + ": "+
                    calEvent.start.getHours()+":"+calEvent.start.getMinutes()+
                    /*$.datepicker.formatDate('yy-mm-dd', calEvent.start)+*/" - "+
                    calEvent.end.getHours()+":"+calEvent.end.getMinutes()
                    /*$.datepicker.formatDate('yy-mm-dd', calEvent.end)*/); 
                $element.find(".wc-time").remove();
                if (calEvent.hclass == undefined)
                    $element.addClass('cal-old');
                else
                    $element.addClass(calEvent.hclass);
            },
            eventMouseover : function(calEvent, $event) {
            },
            eventMouseout : function(calEvent, $event) {
            },
            noEvents : function() {

            },
            data : function(start, end, callback) {
                callback(getEventData());
            }
        });
        

        function getEventData() {
            var year = new Date().getFullYear();
            var month = new Date().getMonth();
            var day = new Date().getDate();
            var gris = request.gris;
            for (var i=0; i<gris.length; i++){
                gris[i]['start'] = new Date(gris[i]['start']);
                gris[i]['end'] = new Date(gris[i]['end']);
            }
            /* gris = gris.concat([{
                        "id":99,
                        "start": new Date(2011, 9, 22, 10),
                        "end": new Date(2011, 9, 22, 18, 45),
                        "title":"Reservation2",
                        "class": "test2",
                        "status": -1
                    }]);*/
            console.debug(gris);
            return {
                events : gris
            };
        }

    }
}).call(request);