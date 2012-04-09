var request = {};

(function() {
    var actionUrl = null;
    var response = null;
    var bandwidth = null;
    var gris = null;
    
    this.resizefn = function() {
        
        console.debug('ress');
        if ($('#res_mapCanvas'))
            $('#res_mapCanvas').css('width', $('#subtab-points').offset().left-$('#tabs-2').offset().left );
    };
    
    this.reply = function(response, availableBandwidth) {
        //var message = prompt();
        if (response=="accept"){
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_good.png");
            $("#MessageLabel").html("Request will be accepted, please provide a message: ");
        }else{
            $("#MessageImg").attr("src", baseUrl+"webroot/img/hand_bad.png");
            $("#MessageLabel").html("Request will be rejected, please provide a message: ");
        }
        $("#Message").val('');
        $("#MessageBandwidth").html('Bandwidth available: '+availableBandwidth+' Mbps. <br/>Bandwidth requested: '+this.bandwidth+' Mbps.');
        this.response = response;
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
            buttons: {
                "Ok": function() {
                    var message = $("#Message").val();
                    if (message && message != "")
                        $.navigate({
                            type: "POST",
                            url: url,
                            data: {
                                response: request.response, 
                                message: message
                            }
                        });
                    $(this).dialog("close");
                },
                "Cancel": function() {
                    $(this).dialog("close");
                }
            }
        });
    };
    
    this.buildCalendar = function(){
        
        var finishfn = function(){
            $(window).unbind('resize');
            $('#main').unbind('pjax:start', finishfn);
            console.debug('ehhh');
        };
        $('#main').bind('pjax:start', finishfn);
        $(window).resize(request.resizefn);
        
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