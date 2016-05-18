/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap = new LMap('canvas');
var meicanGraph = new VGraph("canvas");
var meicanTopo = [];
var mode = 'map';
var path = [];
var currentEvent = null;
var lsidebar;

$(document).ready(function() {
    meicanMap.show('dev');
    $(".sidebar-toggle").remove();
    $(".sidebar-mini").addClass("sidebar-collapse");

    loadDomains();
    initScheduleTab();
    initPathTab();
    initConfirmTab();

    $("#home").on("click",'.next-btn', function() {
        lsidebar.open("path");
    });

    $("#path").on("click",'.next-btn', function() {
        lsidebar.open("requirements");
    });

    $("#requirements").on("click",'.next-btn', function() {
        lsidebar.open("schedule");
        initCalendar();
    });

    $("#schedule").on("click",'.next-btn', function() {
        lsidebar.open("confirm");
    });
    
    lsidebar = L.control.lsidebar('lsidebar').addTo(meicanMap._map);
    lsidebar.open("home");
    initEditPointSelects();
});

function initConfirmTab() {
    $("#confirm").on("click",'.next-btn', function() {
        var reservationForm = $( "#reservation-form" ).clone();
        var events = $('#calendar').fullCalendar('clientEvents');
        for (var i = 0; i < events.length; i++) {
            $( '<input name="ReservationForm[events][start][]" value="' + events[i].start.toISOString() + '" hidden>' ).appendTo( reservationForm );
            $( '<input name="ReservationForm[events][finish][]" value="' + events[i].end.toISOString() + '" hidden>' ).appendTo( reservationForm );
        };
        $.ajax({
            type: "POST",
            url: baseUrl + '/circuits/reservation/request',
            data: reservationForm.serialize(),
            success: function (resId) {
                if (resId>0) {
                    $.ajax({
                        type: "POST",
                        url: baseUrl + '/circuits/reservation/confirm', 
                        data: {
                            id: resId,
                        }
                    });
                    window.location.href = baseUrl + '/circuits/reservation/view?id=' + resId;
                } else if(resId==-1){
                    //showError(tt("You are not allowed to create a reservation involving these selected domains."));
                } else {
                    //showError(tt("Error proccessing your request. Contact your administrator."));
                }
            },
            error: function() {
                //showError(tt("Error proccessing your request. Contact your administrator."));
            }
        });
    });
}

function showPointModal(pointElement, pointOrder, nodeId) {
    $('#point-form').yiiActiveForm('resetForm');
    $('#point-advanced-form').yiiActiveForm('resetForm');
    clearSelect("pointform-network");
    clearSelect("pointform-device");
    clearSelect("pointform-port");
    clearSelect("pointform-vlan");
    $("#pointform-vlan_text").val('');
    $('#pointform-urn').val('');

    if(pointOrder != null) {
        pointElement = $(".point")[pointOrder];
        console.log(pointOrder, pointElement);
    }
    setPointModalMode($(pointElement).find(".mode-input").val());

    if(nodeId) {
        var marker = meicanMap.getNode(nodeId);
        $("#pointform-domain").val(marker.options.domainId);
        fillNetworkSelect(marker.options.domainId);
        fillDeviceSelect(marker.options.domainId, null, marker.options.id.replace('dev',''));
        fillPortSelect(marker.options.id.replace('dev',''), $($(".point")[pointOrder]).find('.port-input').val());
        $("#point-modal").find('.point-order').text(pointOrder); 
    } else {
        $("#pointform-domain").val($(pointElement).find('.dom-l').attr('data'));
        fillNetworkSelect($(pointElement).find('.dom-l').attr('data'));
        fillDeviceSelect($(pointElement).find('.dom-l').attr('data'), null, $(pointElement).find('.dev-l').attr('data'));
        fillPortSelect($(pointElement).find('.dev-l').attr('data'), $(pointElement).find('.port-input').val()); 
        fillVlanSelect($(pointElement).find('.port-input').val(), $(pointElement).find('.vlan-input').val()); 

        $("#pointform-vlan_text").val($(pointElement).find('.vlan-input').val());
        $('#pointform-urn').val($(pointElement).find('.urn-input').val());

        //subtrai um no index pois os elementos sao de mesmo tipo mas o primeiro e ultimo sao de classes diferentes
        $("#point-modal").find('.point-order').text($(pointElement).index() - 1); 
    }
    
    $("#point-modal").modal("show");
}

function setPointModalMode(mode) {
    console.log(mode);
    if (mode == 'normal' || mode == "") {
        $($('#point-modal').find("[data-toggle=tab]")[0]).tab('show');
    } else {
        $($('#point-modal').find("[data-toggle=tab]")[1]).tab('show');
    }
}

function initPathTab() {
    $("#add-point").click(function() {
        addPoint();
        return false;
    });

    $("#point-modal").on('click','.save-btn',function() {
        if ($("#point-modal").find(".tab-pane.active").attr("id") == "normal") {
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-domain');
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-device');
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-port');
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-vlan');

        } else {
            $("#point-advanced-form").yiiActiveForm("validateAttribute", 'pointform-urn');
            $("#point-advanced-form").yiiActiveForm("validateAttribute", 'pointform-vlan_text');
        }

        setTimeout(function() {
            if($("#point-modal").find(".tab-pane.active").find(".has-error").length > 0) {
                console.log("tem erro")
                return false;
            }

            setPoint(
                null,
                $("#point-modal").find('.point-order').text(),
                $("#point-modal").find(".tab-pane.active").attr("id"),
                $("#pointform-domain").val(),
                $("#pointform-domain option:selected").text(),
                $("#pointform-network").val(),
                $("#pointform-network").val() == '' ? 'none' : $("#pointform-network option:selected").text(),
                $("#pointform-device").val(),
                $("#pointform-device option:selected").text(),
                $("#pointform-port").val(),
                $("#pointform-port option:selected").text(),
                $("#pointform-urn").val(),
                $("#pointform-vlan").val(),
                $("#pointform-vlan_text").val()
            );                

            $("#point-modal").modal('hide');

        }, 200);
        
        return false;
    });

    $("#point-modal").on('click','.cancel-btn',function() {
        $("#point-modal").modal('hide');
        return false;
    });

    $('#canvas').on('lmap.nodeClick', function(e, marker) {
        marker.setPopupContent('Domain: <b>' + meicanMap.getDomain(marker.options.domainId).name + 
            '</b><br>Device: <b>' + marker.options.name + '</b><br><br>'+
            '<div data-node="' + marker.options.id + '">'+
              '<button class="set-source">From here</button>'+
              '<button class="add-waypoint">Add waypoint</button>'+
              '<button class="set-destination">To here</button>'+
            '</div>');
        /*marker.setPopupContent('Domain: cipo.rnp.br<br>Device: POA<br><br><div class="btn-group">'+
            '<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'+
              'Options <span class="fa fa-caret"></span>'+
            '</button>'+
            '<ul data-marker="' + marker.options.id + '" class="dropdown-menu">'+
              '<li><a class="set-source" href="#">From here</a></li>'+
              '<li><a class="add-waypoint" href="#">Add waypoint</a></li>'+
              '<li><a class="set-destination" href="#">To here</a></li>'+
            '</ul>'+
          '</div>');*/
    });

    $('#canvas').on('vgraph.nodeClick', function(e, nodeId) {
        console.log('sdas');
        meicanGraph.showPopup(nodeId, 'Domain: cipo.rnp.br<br>Device: POA<br><br>'+
            '<div data-node="' + nodeId + '">'+
              '<button class="set-source">From here</button>'+
              '<button class="add-waypoint">Add waypoint</button>'+
              '<button class="set-destination">To here</button>'+
            '</div>');
    });

    $("#canvas").on("click",'.set-source', function() {
        lsidebar.open("path");
        //setSourcePoint($(this).parent().attr('data-node'));
        closePopups();
        showPointModal(null, 0, $(this).parent().attr('data-node'));
        return false;
    });

    $("#canvas").on("click",'.set-destination', function() {
        lsidebar.open("path");
        //setDestinationPoint($(this).parent().attr('data-node'));
        closePopups();
        showPointModal(null, $('.point').length - 1, $(this).parent().attr('data-node'));
        return false;
    });

    $("#canvas").on("click",'.add-waypoint', function() {
        lsidebar.open("path");
        closePopups();
        addWayPoint($(this).parent().attr('data-node'));
        return false;
    });

    $("#path").on('click','.fa-arrow-up', function() {
        var index = $(this).parent().parent().parent().parent().parent().index();
        if(index > 1)
            $($(".point")[index - 1]).insertBefore($($(".point")[index - 2]));
        return false;
    });

    $("#path").on('click','.fa-arrow-down', function() {
        var index = $(this).parent().parent().parent().parent().parent().index();
        if(index < $(".point").length)
            $($(".point")[index - 1]).insertAfter($($(".point")[index]));
        return false;
    });

    $("#path").on('click','.fa-trash', function() {
        $(this).parent().parent().parent().parent().parent().remove();
        return false;
    });

    $("#path").on('click','.fa-pencil', function() {
        showPointModal($(this).parent().parent().parent().parent().parent());
        return false;
    });

    $("#path").on('click','.fa-minus', function() {
        $(this).removeClass('fa-minus');
        $(this).addClass('fa-plus');
        $(this).parent().parent().parent().parent().find('.timeline-body').slideUp();
        return false;
    });

    $("#path").on('click','.fa-plus',function() {
        $(this).removeClass('fa-plus');
        $(this).addClass('fa-minus');
        $(this).parent().parent().parent().parent().find('.timeline-body').slideDown();
        return false;
    });
    
    $("#canvas").css("height", $(window).height() - 50);
    if($(window).width() < 768) {
        $("#canvas").css("width", $(window).width() - 40);
    } else {
        $("#canvas").css("width", $(window).width() - 51);
    }

    $( window ).resize(function() {
        $("#canvas").css("height", $(window).height() - 50);
        if($(window).width() < 768) {
            $("#canvas").css("width", $(window).width() - 40);
        } else {
            $("#canvas").css("width", $(window).width() - 51);
        }
    })
    
    /*$( "#graph-canvas" ).on( "nodeClick", function(event, nodeId) {
        meicanMap.showMarker(nodeId);
    });
    $( "#map-canvas" ).on( "markerClick", function(event, markerId) {
        meicanGraph.showNode(markerId);
    });*/
        
    
}

function initScheduleTab() {
    $("#lsidebar").on("click",'.schedule-tab', function() {
        initCalendar();
    });

    $("#schedule-modal").on('click', '.save-btn', function() {
        if(currentEvent == null) {
            var events = [];
            events.push({
                title: 'VC',
                start: moment($('#datetime-range').val().split(' - ')[0], "DD/MM/YYYY HH:mm").toISOString(),
                end: moment($('#datetime-range').val().split(' - ')[1], "DD/MM/YYYY HH:mm").toISOString()
            });

            $('#calendar').fullCalendar('addEventSource', events );
        } else {
            currentEvent.start = moment($('#datetime-range').val().split(' - ')[0], "DD/MM/YYYY HH:mm").toISOString()
            currentEvent.end = moment($('#datetime-range').val().split(' - ')[1], "DD/MM/YYYY HH:mm").toISOString()
            $('#calendar').fullCalendar('updateEvent', currentEvent );
        }

        $("#schedule-modal").modal("hide");
    });

    $("#schedule-modal").on('click', '.remove-btn', function() {
        $('#calendar').fullCalendar('removeEvents', currentEvent._id);
        $("#schedule-modal").modal("hide");
    });

    $("#schedule-modal").on('click', '.cancel-btn', function() {
        $("#schedule-modal").modal("hide");
    });

    $('#datetime-range').daterangepicker({
        timePicker: true,
        timePickerIncrement: 1,
        timePicker24Hour: true,
        linkedCalendars: false,
        startDate: moment().format("DD/MM/YYYY HH:mm"),
        endDate: moment().add(1, 'hours').format("DD/MM/YYYY HH:mm"),
        autoApply: "true",
        "opens": "right",
        "locale": {
            "format": "DD/MM/YYYY HH:mm",
            "separator": " - ",
            "applyLabel": I18N.t("Apply"),
            "cancelLabel": I18N.t("Cancel"),
            "fromLabel": I18N.t("From"),
            "toLabel": I18N.t("To"),
            "customRangeLabel": I18N.t("Custom"),
            "daysOfWeek": [
                I18N.t("Su"),
                I18N.t("Mo"),
                I18N.t("Tu"),
                I18N.t("We"),
                I18N.t("Th"),
                I18N.t("Fr"),
                I18N.t("Sa")
            ],
            "monthNames": [
                I18N.t("January"),
                I18N.t("February"),
                I18N.t("March"),
                I18N.t("April"),
                I18N.t("May"),
                I18N.t("June"),
                I18N.t("July"),
                I18N.t("August"),
                I18N.t("September"),
                I18N.t("October"),
                I18N.t("November"),
                I18N.t("December")
            ],
        },
    });

    $(".daterangepicker").find('.ranges').remove();
}

function initCalendar() {
    if($("#calendar").attr('loaded') === "false") {
        $("#calendar").attr("loaded", 'true');
        $('#calendar').fullCalendar({
            defaultView: 'agendaWeek',
            height: 480,
            timezone: 'local',
            dayClick: function(date) {
                currentEvent = null;
                $("#schedule-modal").find(".remove-btn").hide();
                $("#schedule-modal").modal("show");
                $('#datetime-range').data('daterangepicker').setStartDate(date.format("DD/MM/YYYY HH:mm"));
                if(date._ambigTime)
                    $('#datetime-range').data('daterangepicker').setEndDate(date.add(24, 'hours').format("DD/MM/YYYY HH:mm"));
                else
                    $('#datetime-range').data('daterangepicker').setEndDate(date.add(1, 'hours').format("DD/MM/YYYY HH:mm"));
            },
            eventClick: function(event) {
                currentEvent = event;
                $("#schedule-modal").find(".remove-btn").show();
                $("#schedule-modal").modal("show");
                $('#datetime-range').data('daterangepicker').setStartDate(moment(event.start).format("DD/MM/YYYY HH:mm"));
                if(event.allDay)
                    $('#datetime-range').data('daterangepicker').setEndDate((moment(event.start).add(24, 'hours')).format("DD/MM/YYYY HH:mm"));
                else
                    $('#datetime-range').data('daterangepicker').setEndDate(moment(event.end).format("DD/MM/YYYY HH:mm"));
            },
            lang: 'en-us',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: [],
            editable: true,
            eventLimit: true,
        });
    }
}

function closePopups() {
    if(mode != 'map') {
        meicanGraph.closePopups();
    } else {
        meicanMap.closePopups();
    }
}

function prepareConfirmModal() {
    $("#confirm-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        appendTo: "#reservation-form",
        buttons: [{
            id:"confirm-button",
            text: tt('Yes'),
            click: function() {
                $("#confirm-button").attr("disabled", "disabled");
                if (validateForm()) {
                    $.ajax({
                        type: "POST",
                        url: baseUrl + '/circuits/reservation/request',
                        data: $("#reservation-form").serialize(),
                        success: function (resId) {
                            if (resId>0) {
                                $.ajax({
                                    type: "POST",
                                    url: baseUrl + '/circuits/reservation/confirm', 
                                    data: {
                                        id: resId,
                                    }
                                });
                                window.location.href = baseUrl + '/circuits/reservation/view-circuit?id=' + resId;
                            } else if(resId==-1){
                                showError(tt("You are not allowed to create a reservation involving these selected domains."));
                            } else {
                                showError(tt("Error proccessing your request. Contact your administrator."));
                            }
                        },
                        error: function() {
                            showError(tt("Error proccessing your request. Contact your administrator."));
                        }
                    });
                }
            }
        },{
            id:"cancel-button",
            text: tt('No'),
            click: function() {
                $("#confirm-dialog").dialog( "close" );
            }
        }],
        close: function() {
            $("#error-confirm-dialog").hide();
            $("#error-confirm-dialog").html("");
            $("#confirm-button").attr("disabled", false);
        },
    });
    
    $("#request-button").click(function() {
        $("#confirm-dialog").dialog("open");
        return false;
    });
}

function setSourcePoint(nodeId) {
    setPointByNode(0, nodeId);
}

function setDestinationPoint(nodeId) {
    setPointByNode($('.point').length - 1, nodeId);
}

function addWayPoint(nodeId) {
    addPoint($('.point').length - 1, nodeId);
}

function setPointByNode(position, nodeId) {
    console.log(position, nodeId);
    //var node = meicanMap.getMarker(nodeId);
    //var node = meicanGraph.getNode(nodeId);
    if(mode == 'map') {
        var marker = meicanMap.getMarker(nodeId);
        setPoint(
            position, 
            meicanMap.getDomain(marker.options.domainId).name,
            'none',
            marker.options.name,
            marker.options.id.replace('dev',''),
            'none',
            null,
            null,
            'auto');
        
    } else {
        var node = meicanGraph.getNode(nodeId);
    }

    drawPath();
}

function setPoint(pointElement, pointOrder, pointMode, domId, dom, netId, net, devId, dev, portId, port, urn, vlan, vlanAdvanced) {
    if(pointOrder != null) {
        pointElement = $(".point")[pointOrder];
    }

    $(pointElement).find('.mode-input').val(pointMode);

    if(pointMode == 'normal') {
        $(pointElement).find(".point-advanced").hide();
        $(pointElement).find(".point-normal").show();

        $(pointElement).find('.dom-l').attr('data',domId);
        $(pointElement).find('.dom-l').text(dom);
        $(pointElement).find('.net-l').attr('data',netId);
        $(pointElement).find('.net-l').text(net);
        $(pointElement).find('.dev-l').attr('data', devId);
        $(pointElement).find('.dev-l').text(dev);
        $(pointElement).find('.port-input').val(portId);
        $(pointElement).find('.port-l').text(port);

        $(pointElement).find('.vlan-l').text(vlan);
        $(pointElement).find('.vlan-input').val(vlan);
    } else {
        $(pointElement).find(".point-normal").hide();
        $(pointElement).find(".point-advanced").show();

        $(pointElement).find('.dom-l').text(urn.split(':')[3]);
        console.log(urn.split(':')[3])

        $(pointElement).find('.urn-input').val(urn);
        $(pointElement).find('.urn-l').text(urn);

        $(pointElement).find('.vlan-l').text(vlanAdvanced);
        $(pointElement).find('.vlan-input').val(vlanAdvanced);
    }
    
    $(pointElement).find('.timeline-body').slideDown();
    var element = $(pointElement).find('.fa-plus'); 
    element.removeClass('fa-plus');
    element.addClass('fa-minus');
    drawPath();
}

function addPoint(position, nodeId) {
    if(position) {
        $($(".point")[position]).before(buildPoint());
    } else {
        $("#destination-client").before(buildPoint());
    }
}

function buildPoint() {
    return '<li class="point">'+
        '<i class="fa fa-map-marker bg-gray"></i>'+
        '<div class="timeline-item">'+
            '<h3 class="timeline-header">'+
                '<label data="" class="point-info dom-l">none</label>'+
                '<div class="pull-right">'+
                    '<a href="#" class="text-muted"><i class="fa fa-minus"></i></a>'+
                    '<a href="#" class="text-muted" style="margin-left: 3px;"><i class="fa fa-arrow-up"></i></a>'+
                    '<a href="#" class="text-muted" style="margin-left: 3px;"><i class="fa fa-arrow-down"></i></a>'+
                '</div>'+
          '</h3>'+
        '<div class="timeline-body">'+
            '<div class="point-normal">'+
              'Network: <label data="" class="point-info net-l">none</label><br>'+
              'Device: <label data="" class="point-info dev-l">none</label><br>'+
              'Port: <label class="point-info port-l">none</label><br>'+
              '<input class="port-input" type="hidden" name="ReservationForm[path][port][]">'+
            '</div>'+
            '<div class="point-advanced" hidden>'+
              'URN: <label class="point-info urn-l">none</label><br>'+
              '<input class="urn-input" type="hidden" name="ReservationForm[path][urn][]">'+
            '</div>'+
            'VLAN: <label class="point-info vlan-l">Auto</label>'+
            '<input class="vlan-input" type="hidden" name="ReservationForm[path][vlan][]">'+
            '<input class="mode-input" type="hidden" name="ReservationForm[path][mode][]">'+
            '<div class="pull-right">'+
                '<a href="#" class="text-muted"><i class="fa fa-pencil"></i></a>'+
                '<a href="#" class="text-muted" style="margin-left: 3px;"><i class="fa fa-trash"></i></a>'+
            '</div>'+
        '</div>'+
    '</li>';
}

function drawPath() {
    meicanMap.removeLinks();

    if ($(".point").length > 1) {
        var path = [];
        for (var i = 0; i < $(".point").length; i++) {
            path.push('dev' + $($(".point")[i]).find('.dev-l').attr('data'));
        };
        meicanMap.addLink(path);
    }
}

/*$("#viewer-mode-select").selectmenu({
    select: function( event, ui ) {
        switch(ui.item.value) {
            case "mg-s" : 
                meicanLMap.hide();
                meicanGraph.hide();
                meicanGMap.show('s', $("#node-type-select").val());
                break;
            case "ml-osm" :
                meicanGMap.hide();
                meicanGraph.hide();
                meicanLMap.show("osm", $("#node-type-select").val());
                break;
            case "ml-mq" :
                meicanGMap.hide();
                meicanGraph.hide();
                meicanLMap.show("mq");
                break;
            case "gv" : 
                meicanLMap.hide();
                meicanGMap.hide();
                meicanGraph.show();
                break;    
        }
    }
});    

$("#node-type-select").selectmenu({
    select: function( event, ui ) {
        switch($("#viewer-mode-select").val()) {
            case "ml-osm":
                meicanLMap.show('osm', ui.item.value);
                break;
        }

        switch(ui.item.value) {
            case "net" : 
                loadNetworks();
                break;  
            case "dev" : 
                loadDevices();
                break;
            case "port" : 
                loadPorts();
                break;  
            case "prov" : 
                loadProviders();
                break;   
        }
    }
});

$("#save-button").on("click", function(){
    meicanGraph._graph.storePositions();
    $.ajax({
        type: "POST",
        url: baseUrl + '/topology/viewer/save-graph',
        data: {
            mode: $("#node-type-select").val(),
            nodes: meicanGraph._nodes.get({
                filter: function (item) {
                    return item.type == $("#node-type-select").val();
                }
            })
        },
        success: function (response) {
        },
        error: function() {
        }
    });
});*/

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            meicanTopo['dom'] = response;
            meicanMap.setDomains(response);
            meicanGraph.setDomains(response);
            //meicanLMap.setDomains(response);
            //meicanGMap.setDomains(response);
            //meicanMap.setDomains(response);
            /*
            meicanGraph.addNodes(response, "dom", true);
            meicanGraph.fit();*/
            loadDevices();
        }
    });
}

function loadDevices() {
    if(meicanTopo['dev']) return;
    $.ajax({
        url: baseUrl+'/topology/device/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude','graph_x', 'graph_y', 'domain_id'])
        },
        success: function(response) {
            meicanTopo['dev'] = response;
            for (var i = 0; i < response.length; i++) {
                meicanMap.addNode(
                    'dev' + response[i].id,
                    response[i].name,
                    'dev',
                    response[i].domain_id,
                    response[i].latitude,
                    response[i].longitude);
            };
            //loadDeviceLinks();
        }
    });
}

function loadNetworks() {
    if(meicanTopo['net']) return;
    $.ajax({
        url: baseUrl+'/topology/network/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude', 'domain_id'])
        },
        success: function(response) {
            meicanTopo['net'] = true;
            meicanGraph.addNodes(response, 'net');
            for (var i = 0; i < response.length; i++) {
                meicanMap.addMarker(response[i], 'net');
            };
            loadNetworkLinks();
        }
    });
}

function loadDomainLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-domain-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanTopo['doml'] = response;
            //meicanGraph.addLinks(response, 'dom');            
        }
    });
}

function loadNetworkLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-network-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanGraph.addLinks(response, 'net');
            for (var src in response) {
                for (var i = 0; i < response[src].length; i++) {
                    meicanMap.addLink('net'+src,'net'+response[src][i], 'net');
                }
            }           
        }
    });
}

function loadDeviceLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-device-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanTopo['dev']['links'] = response;
            /*meicanGraph.addLinks(response, 'dev');
            for (var src in response) {
                for (var i = 0; i < response[src].length; i++) {
                    meicanMap.addLink('dev'+src,'dev'+response[src][i], 'dev');
                }
            }   */        
        }
    });
}

function fillDomainSelect() {
    var selectId = "pointform-domain";
    clearSelect(selectId);
    $("#" + selectId).append('<option value="">' + I18N.t('loading') + '</option>');
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        data: {
            cols: JSON.stringify(['id','name']),
        },
        dataType: 'json',
        success: function(domains){
            clearSelect(selectId);
            $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
            for (var i = 0; i < domains.length; i++) {
                $("#" + selectId).append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
            }
            enableSelect(selectId);
        },
    });
}

function fillNetworkSelect(domainId, networkId, initDisabled) {
    var selectId = "pointform-network";
    disableSelect(selectId);
    clearSelect(selectId);
    if (domainId != "" && domainId != null) {
        $("#" + selectId).append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/network/get-by-domain',
            data: {
                id: domainId,
            },
            dataType: 'json',
            success: function(response){
                clearSelect(selectId);
                $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
                if (!initDisabled) enableSelect(selectId);
                for (var i = 0; i < response.length; i++) {
                    $("#" + selectId).append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                }
                if (networkId != null) {
                    $("#" + selectId).val(networkId);
                }
            }
        });
    } 
}

function fillDeviceSelect(domainId, networkId, deviceId, initDisabled) {
    var selectId = "pointform-device";
    disableSelect(selectId);
    clearSelect(selectId);
    parent = null;
    if (networkId != "" && networkId != null) {
        parent = [];
        parent[0] = "network";
        parent[1] = networkId;
    } else if (domainId != "" && domainId != null) {
        parent = [];
        parent[0] = "domain";
        parent[1] = domainId;
    } 

    if (parent) {
        $("#" + selectId).append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/device/get-by-' + parent[0],
            dataType: 'json',
            data: {
                id: parent[1],
            },
            success: function(response){
                clearSelect(selectId);
                $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
                if (!initDisabled) enableSelect(selectId);
                for (var i = 0; i < response.length; i++) {
                    if (response[i].name == "") response[i].name = "default";
                    $("#" + selectId).append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                }
                if (deviceId != null && deviceId != "") {
                    $("#" + selectId).val(deviceId);
                }
            }
        });
    } 
}

function fillPortSelect(deviceId, portId) {
    var selectId = "pointform-port";
    disableSelect(selectId);
    clearSelect(selectId);
    if (deviceId != "" && deviceId != null) {
        $("#" + selectId).append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/circuits/reservation/get-port-by-device',
            dataType: 'json',
            data: {
                id: deviceId,
                cols: JSON.stringify(['id','name']),
            },
            success: function(response){
                clearSelect(selectId);
                $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
                enableSelect(selectId);
                for (var i = 0; i < response.length; i++) {
                    var name = response[i].name;
                    if (response[i].port == "") {
                        name = I18N.t("default");
                    }
                    $("#" + selectId).append('<option value="' + response[i].id + '">' + name + '</option>');
                }
                if (portId != null && portId != "") $("#" + selectId).val(portId);
            }
        });
    } 
}

function fillVlanSelect(portId, vlan) {
    var selectId = "pointform-vlan";
    disableSelect(selectId);
    clearSelect(selectId);
    if (portId != "" && portId != null) {
        $("#" + selectId).append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/port/get-vlan-range',
            dataType: 'json',
            data: {
                id: portId,
            },
            success: function(response){
                clearSelect(selectId);
                $("#" + selectId).append('<option value="auto">Auto</option>');
                if(response) {
                    var ranges = response.split(",");
                    for (var i = 0; i < ranges.length; i++) {
                        var interval = ranges[i].split("-");
                        if (interval.length > 1)
                            $("#" + selectId).append('<option value="' + ranges[i] + '">' + ranges[i] + '</option>');
                    }

                    for (var i = 0; i < ranges.length; i++) {
                        var interval = ranges[i].split("-");
                        var low = parseInt(interval[0]);
                        var high = low;
                        if (interval.length > 1) {
                            high = parseInt(interval[1]);
                            for (var j = low; j < high+1; j++) {
                            $("#" + selectId).append('<option value="' + j + '">' + j + '</option>');
                        }
                        } else {
                            $("#" + selectId).append('<option value="' + low + '">' + low + '</option>');
                        }
                        
                        if (vlan != null && vlan != "") {
                            $("#" + selectId).val(vlan);
                        }
                    }
                    enableSelect(selectId);
                }
            }
        });
    }
}

function clearSelect(object) {
    $('#' + object).children().remove();
}

function disableSelect(object) {
    $('#' + object).prop('disabled', true);
}

function enableSelect(object) {
    if ($('#' + object).val() != null && $('#' + object) != "null") {
        $('#' + object).prop('disabled', false);
    }
}

function initEditPointSelects() {
    fillDomainSelect();
    
    $('#pointform-domain').on('change', function() {
        fillNetworkSelect(this.value);
        fillDeviceSelect(this.value);
        fillPortSelect();
        fillVlanSelect();
    });
    
    $('#pointform-network').on('change', function() {
        fillDeviceSelect($('#pointform-domain').val(), this.value);
        fillPortSelect();
        fillVlanSelect();
    });
    
    $('#pointform-device').on('change', function() {
        fillPortSelect(this.value);
        fillVlanSelect();
    });
    
    $('#pointform-port').on('change', function() {
        fillVlanSelect(this.value);
    });
}
