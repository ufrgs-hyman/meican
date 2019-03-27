/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Maurício Quatrin Guerreiro
 */

var meicanMap = new LMap('canvas');
var meicanGraph = new VGraph("canvas");
var meicanTopo = [];
var mode = 'map';
var path = [];
var currentEvent = null;
var lsidebar;

$(document).ready(function() {
    $(".sidebar-toggle").hide();
    $(".sidebar-mini").addClass("sidebar-collapse");

    initNodes();
    
    initScheduleTab();
    initPathTab();
    initConfirmTab();

    $("#home").on("click",'.next-btn', function() {
        lsidebar.open("path");
    });

    $("#path").on("click",'.next-btn', function() {
        if(validatePath())
            lsidebar.open("requirements");
        return false;
    });

    $("#requirements").on("click",'.next-btn', function() {
        if(validateRequirements()) {
            lsidebar.open("schedule");
            initCalendar();
        }
    });

    $("#schedule").on("click",'.next-btn', function() {
        if(validateSchedule())
            lsidebar.open("confirm");
    });
    
    lsidebar = L.control.lsidebar('lsidebar').addTo(meicanMap._map);
    lsidebar.open("home");
    
});

function validatePath() {
    if (isValidPath()) return true;
    else {
        MAlert.show(
            'Path invalid!', 
            'Please, verify if your path is completelly filled. Two points are required.',
            'danger');
        return false;
    }
}

function isValidPath() {
    for (var i = 0; i < $('.point').length; i++) {
        if ($('.point').eq(i).find('.urn-input').val() == "") {
            return false;
        } else return true;
    };

    return false;
}

function validateRequirements() {
    if(($("#reservationform-bandwidth").val() == "") ||
        $("#requirements").find(".field-reservationform-bandwidth").hasClass("has-error")) {
        MAlert.show(
            'Bandwidth invalid!', 
            'The value must be must be no less than 1.',
            'danger');
        return false;
    }
    return true;
}

function validateSchedule() {
    var events = $('#calendar').fullCalendar('clientEvents');
    if(events.length < 1) {
        MAlert.show(
            'Circuit duration invalid!', 
            'Please, check your start and end time.',
            'danger');
        return false;
    }  
    return true;
}

function validateName() {
    if(($("#reservationform-name").val() == "") ||
        $("#confirm").find(".field-reservationform-name").hasClass("has-error")) {
        MAlert.show(
            'Name invalid!', 
            'Please, check your circuit name.',
            'danger');
        return false;
    }
    return true;
}

function initConfirmTab() {
    $("#confirm").on("click",'.next-btn', function() {
        if(validatePath() && validateRequirements() && validateSchedule() && validateName()) {
            $(this).attr('disabled','disabled');
            MAlert.show(
                'Request received!', 
                'Please, wait a moment while we process your request.',
                'success');
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
        }
    });
}

function showPointModal(pointElement, pointOrder, nodeId) {
    $('#point-form').yiiActiveForm('resetForm');
    $('#point-advanced-form').yiiActiveForm('resetForm');
    disableSelect("pointform-network");
    disableSelect("pointform-location");
    disableSelect("pointform-port");
    disableSelect("pointform-vlan");
    $("#pointform-vlan_text").val('');
    $('#pointform-urn').val('');

    setPointModalMode(pointElement ? $(pointElement).find(".mode-input").val() : 'normal');

    if(pointOrder != null) {
        pointElement = $(".point")[pointOrder];
        console.log(pointOrder, pointElement);
    }

    if(nodeId) {
        var node = meicanMap.getNode(nodeId);
        if (node.options.ports.length > 1) {
            $("#pointform-domain").val(node.options.ports[0].network.domain.id);
            fillNetworkSelect(node.options.ports[0].network.domain.id);
        } else {
            $("#pointform-domain").val(node.options.ports[0].network.domain.id);
            fillNetworkSelect(node.options.ports[0].network.domain.id, node.options.ports[0].network.id);
            fillPortSelect(node.options.ports[0].network.id, node.options.ports[0].id);
            fillVlanSelect(node.options.ports[0].id);
        }
        
        $("#point-modal").find('.point-order').text(pointOrder); 
    } else {
        urn = $(pointElement).find('.urn-input').val();
        for (var i = meicanTopo['ports'].length - 1; i >= 0; i--) {
            if (meicanTopo['ports'][i]['urn'] == urn) {
                port = meicanTopo['ports'][i];
                $("#pointform-domain").val(port.network.domain.id);
                fillNetworkSelect(port.network.domain.id, port.network.id);
                fillPortSelect(port.network.id, port.id); 
                fillVlanSelect(port.id, $(pointElement).find('.vlan-input').val()); 
            }
        }
        
        $("#pointform-vlan_text").val($(pointElement).find('.vlan-input').val());
        $('#pointform-urn').val($(pointElement).find('.urn-input').val());

        //subtrai um no index pois os elementos sao de mesmo tipo mas o primeiro e ultimo sao de classes diferentes
        $("#point-modal").find('.point-order').text($(pointElement).index() - 1); 
        if($("#pointform-network").val() == null){
            $("#pointform-domain")[0].selectedIndex = 0;
        }
    }
    
    $("#point-modal").modal("show");
}

function setPointModalMode(mode) {
    if (mode == 'advanced') {
        $($('#point-modal').find("[data-toggle=tab]")[1]).tab('show');
    } else {
        $($('#point-modal').find("[data-toggle=tab]")[0]).tab('show');
    }
}

function initPathTab() {
    meicanMap.show();

    $("#add-point").click(function() {
        addPoint();
        return false;
    });

    $("#point-modal").on('click','.save-btn',function() {
        if ($("#point-modal").find(".tab-pane.active").attr("id") == "normal") {
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-domain');
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-port');
            $("#point-form").yiiActiveForm("validateAttribute", 'pointform-vlan');

        } else {
            $("#point-advanced-form").yiiActiveForm("validateAttribute", 'pointform-urn');
            $("#point-advanced-form").yiiActiveForm("validateAttribute", 'pointform-vlan_text');
        }

        setTimeout(function() {
            if($("#point-modal").find(".tab-pane.active").find(".has-error").length > 0) {
                console.log("has error")
                return false;
            }

            setPoint(
                null,
                $("#point-modal").find('.point-order').text(),
                $("#point-modal").find(".tab-pane.active").attr("id"),
                $("#pointform-port").val(),
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

    $('#canvas').on('lmap.nodeClick', function(e, node) {
        node.setPopupContent('Domain: <b>' + node.options.ports[0].network.domain.name + 
            '</b><br><br><div data-node="' + node.options.id + '">'+
              '<button class="btn btn-sm btn-default set-source">From here</button>'+
              ' <button class="btn btn-sm btn-default add-waypoint">Add waypoint</button>'+
              ' <button class="btn btn-sm btn-default set-destination">To here</button>'+
            '</div>');
    });

    $('#canvas').on('vgraph.nodeClick', function(e, nodeId) {
        meicanGraph.showPopup(nodeId, 'Domain: cipo.rnp.br<br>Device: POA<br><br>'+
            '<div data-node="' + nodeId + '">'+
              '<button class="btn btn-sm btn-default set-source">From here</button>'+
              ' <button class="btn btn-sm btn-default add-waypoint">Add waypoint</button>'+
              ' <button class="btn btn-sm btn-default set-destination">To here</button>'+
            '</div>');
    });

    $("#canvas").on("click",'.set-source', function() {
        lsidebar.open("path");
        closePopups();
        showPointModal(null, 0, $(this).parent().attr('data-node'));
        return false;
    });

    $("#canvas").on("click",'.set-destination', function() {
        lsidebar.open("path");
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
        drawPath();
        return false;
    });

    $("#path").on('click','.fa-arrow-down', function() {
        var index = $(this).parent().parent().parent().parent().parent().index();
        if(index < $(".point").length)
            $($(".point")[index - 1]).insertAfter($($(".point")[index]));
        drawPath();
        return false;
    });

    $("#path").on('click','.fa-trash', function() {
        if($(".point").length > 2) {
            $(this).parent().parent().parent().parent().parent().remove();
            drawPath();
        } else MAlert.show(
            'Invalid action!',
            'Minimum two points are required for a valid circuit.',
            'warning');
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
            defaultView: 'month',
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

function addWayPoint(nodeId) {
    var position = $('.point').length - 1;
    addPoint(position);
    showPointModal(null, position, nodeId);
}

function setPointByNode(position, nodeId) {
    console.log(position, nodeId);
    //var node = meicanMap.getMarker(nodeId);
    //var node = meicanGraph.getNode(nodeId);
    if(mode == 'map') {
        var marker = meicanMap.getNode(nodeId);
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

function getPort(id) {
    for (var i = meicanTopo['ports'].length - 1; i >= 0; i--) {
        if (id == meicanTopo['ports'][i].id)
            return meicanTopo['ports'][i];
    }
}

function setPoint(pointElement, pointOrder, pointMode, portId, urn, vlan, vlanAdvanced) {
    if(pointOrder != null) {
        pointElement = $(".point")[pointOrder];
    }

    $(pointElement).find('.mode-input').val(pointMode);

    if(pointMode == 'normal') {
        $(pointElement).find(".point-advanced").hide();
        $(pointElement).find(".point-normal").show();

        port = getPort(portId);
        console.log(port);
        $(pointElement).find('.dom-l').text(port.network.domain.name);
        $(pointElement).find('.net-l').text(port.network.name);
        $(pointElement).find('.port-l').text(port.name);

        $(pointElement).find('.urn-input').val(port.urn);

        $(pointElement).find('.vlan-l').text(vlan);
        $(pointElement).find('.vlan-input').val(vlan);
    } else {
        $(pointElement).find(".point-normal").hide();
        $(pointElement).find(".point-advanced").show();

        var domLabel = urn.split(':')[3];
        $(pointElement).find('.dom-l').text(domLabel ? domLabel : "unknown");
        console.log(domLabel);

        $(pointElement).find('.urn-input').val(urn);
        $(pointElement).find('.urn-l').text(urn.split(':').slice(3).join(':'));

        $(pointElement).find('.vlan-l').text(vlanAdvanced);
        $(pointElement).find('.vlan-input').val(vlanAdvanced);
    }
    
    $(pointElement).find('.timeline-body').slideDown();
    var element = $(pointElement).find('.fa-plus'); 
    element.removeClass('fa-plus');
    element.addClass('fa-minus');
    drawPath();
}

function addPoint(position) {
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
                '<label class="point-info dom-l">none</label>'+
                '<div class="pull-right">'+
                    '<a href="#" class="text-muted"><i class="fa fa-minus"></i></a>'+
                    '<a href="#" class="text-muted" style="margin-left: 3px;"><i class="fa fa-arrow-up"></i></a>'+
                    '<a href="#" class="text-muted" style="margin-left: 3px;"><i class="fa fa-arrow-down"></i></a>'+
                '</div>'+
          '</h3>'+
        '<div class="timeline-body">'+
            '<div class="point-normal">'+
              'Network: <label class="point-info net-l">none</label><br>'+
              'Port: <label class="point-info port-l">none</label><br>'+
            '</div>'+
            '<div class="point-advanced" hidden>'+
              'URN: <label class="point-info urn-l">none</label><br>'+
              '<input class="urn-input" type="hidden" name="ReservationForm[path][urn][]">'+
            '</div>'+
            'VLAN: <label class="point-info vlan-l">none</label>'+
            '<input class="vlan-input" type="hidden" name="ReservationForm[path][vlan][]">'+
            '<input class="mode-input" type="hidden" value="normal">'+
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
            path.push($($(".point")[i]).find('.urn-input').val());
        };

        for (var i = 0; i < path.length - 1; i++) {
            meicanMap.addLink(path[i], path[i+1]);
        };
    }
}

function initNodes() {
    loadDomains();
}

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-location',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            meicanTopo['domains'] = response;
            meicanMap.setTopology(meicanTopo);
            for (var i = meicanTopo['domains'].length - 1; i >= 0; i--) {
                meicanTopo['domains'][i]['providers'] = [];
            }
            
            loadProviders();

        }
    });
}

function loadProviders() {
    $.ajax({
        url: baseUrl+'/topology/provider/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude', 'domain_id'])
        },
        success: function(response) {
            meicanTopo['providers'] = response;
            for (var i = meicanTopo['providers'].length - 1; i >= 0; i--) {
                for (var k = meicanTopo['domains'].length - 1; k >= 0; k--) {
                    if (meicanTopo['providers'][i]['domain_id'] == meicanTopo['domains'][k]['id']) {
                        meicanTopo['domains'][k]['providers'].push(meicanTopo['providers'][i]);
                    }
                }
            }
            loadNetworks();
        }
    });
}

function loadNetworks() {
    $.ajax({
        url: baseUrl+'/topology/network/get-all',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanTopo['networks'] = response;
            loadLocation();
            loadPorts();
            for (var i = meicanTopo['networks'].length - 1; i >= 0; i--) {
                for (var k = meicanTopo['domains'].length - 1; k >= 0; k--) {
                    if (meicanTopo['networks'][i]['domain_id'] == meicanTopo['domains'][k]['id']) {
                        meicanTopo['networks'][i]['domain'] = meicanTopo['domains'][k];
                    }
                }
            }
        }
    });
}

//------------------------------------------------------------------------------------------------------
function loadLocation(){
    $.ajax({
        url: baseUrl+'/topology/port/get-location?dir=BI',
        method: "GET",        
        success: function(response) {
            temp = response;
            meicanTopo['location'] = [];
            for (var i = temp.length - 1; i >= 0; i--) {
                if(temp[i].lat != null && temp[i].lng != null){
                    meicanTopo['location'].push(temp[i]);
                }
            }
        }
    });
}

//------------------------------------------------------------------------------------------------------

function loadPorts() {
    $.ajax({
        url: baseUrl+'/topology/port/json?dir=BI',
        method: "GET",        
        success: function(response) {
            meicanTopo['ports'] = response;
            for (var i = meicanTopo['ports'].length - 1; i >= 0; i--) {
                for (var k = meicanTopo['networks'].length - 1; k >= 0; k--) {
                    if (meicanTopo['ports'][i]['network_id'] == meicanTopo['networks'][k]['id']) {
                        meicanTopo['ports'][i]['network'] = meicanTopo['networks'][k];
                    }
                }
                if (meicanTopo['ports'][i].lat != null) {
                    meicanTopo['ports'][i].lat = parseFloat(meicanTopo['ports'][i].lat);
                    meicanTopo['ports'][i].lng = parseFloat(meicanTopo['ports'][i].lng);
                }
            }
            initEditPointSelects();
            for (var i = meicanTopo['ports'].length - 1; i >= 0; i--) {
                meicanMap.addNode(
                    meicanTopo['ports'][i]
                );
            }
            meicanMap.prepareLabels();
        }
    });
}

function fillDomainSelect() {
    var selectId = "pointform-domain";
    clearSelect(selectId);
    $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
    len = meicanTopo['domains'].length;
    for (var i = 0; i < len; i++) {
        $("#" + selectId).append('<option value="' + meicanTopo['domains'][i].id + '">' + meicanTopo['domains'][i].name + '</option>');
    }
    enableSelect(selectId);
}

function fillNetworkSelect(domainId, networkId) {
    var selectId = "pointform-network";
    disableSelect(selectId);
    if (domainId != "" && domainId != null) {
        $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
        len = meicanTopo['networks'].length;
        for (var i = 0; i < len; i++) {
            if (meicanTopo['networks'][i].domain_id == domainId)
                $("#" + selectId).append(
                '<option value="' + 
                meicanTopo['networks'][i].id + '">' + 
                meicanTopo['networks'][i].name + 
                ' (' + meicanTopo['networks'][i]['urn'] + ')' +
                '</option>');
        }
        if (networkId != null) {
            $("#" + selectId).val(networkId);
        }

        var lenNetwork = ($("#" + selectId)[0].length - 1);
        if (lenNetwork == 1){
            $("#" + selectId)[0].selectedIndex = 1;    
        }

        if(hasLocation($("#" + selectId).val())){
            disableSelect("pointform-port");
            fillLocationSelect($("#" + selectId).val());
        }else{
            disableSelect("pointform-location");            // Caso não tenha um Location
            fillPortSelect($("#" + selectId).val()); 
        }
        enableSelect(selectId);
    } 
}

//------------------------------------------------------------------------------------------------------
function fillLocationSelect(networkId, locationId) {
    var selectId = "pointform-location";
    disableSelect(selectId);
    if (networkId != "" && networkId != null) {
        $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
        len = meicanTopo['location'].length;
        for (var i = 0; i < len; i++) {
            if(meicanTopo['location'][i].network_id == networkId){
                $("#" + selectId).append('<option value="' + meicanTopo['location'][i].name + '">' + meicanTopo['location'][i].name +'</option>');
            }    
        }
        if (locationId != null && locationId != "") 
            $("#" + selectId).val(locationId);


        enableSelect(selectId);
    } 
}

function hasLocation(networkId){
    if (networkId != "" && networkId != null) {
        len = meicanTopo['location'].length;
        for (var i = 0; i < len; i++) {
            if(meicanTopo['location'][i].network_id == networkId){
                return 1;
            }
        }
    }
    return 0;
}
//------------------------------------------------------------------------------------------------------

function fillPortSelect(networkId, portId, locationName) {
    var selectId = "pointform-port";
    disableSelect(selectId);
    if (networkId != "" && networkId != null) {
        $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
        len = meicanTopo['ports'].length;
        for (var i = 0; i < len; i++) {
            if (meicanTopo['ports'][i].network_id == networkId) {
                lid = meicanTopo['ports'][i].urn.replace(meicanTopo['ports'][i].network.urn + ':', '');
                $("#" + selectId).append('<option value="' + 
                meicanTopo['ports'][i].id + '">' + 
                meicanTopo['ports'][i].name +
                (lid == meicanTopo['ports'][i].name ? '' : ' (' + lid + ')') +
                '</option>');
            }
        }
        if (portId != null && portId != "") 
            $("#" + selectId).val(portId);

        enableSelect(selectId);
    }
    if(locationName != "" && locationName != null){
        $("#" + selectId).append('<option value="">' + I18N.t('select') + '</option>');
        len = meicanTopo['ports'].length;
        for (var i = 0; i < len; i++) {
            if (meicanTopo['ports'][i].name == locationName) {
                lid = meicanTopo['ports'][i].urn.replace(meicanTopo['ports'][i].network.urn + ':', '');
                $("#" + selectId).append('<option value="' +                //VERIFICAR FORMA CORRETA DE EXIBIÇÃO
                meicanTopo['ports'][i].id + '">' + 
                meicanTopo['ports'][i].name +
                (lid == meicanTopo['ports'][i].name ? '' : ' (' + lid + ')') +
                '</option>');
            }
        }
        if (portId != null && portId != "") 
            $("#" + selectId).val(portId);

        enableSelect(selectId);
    } 
}

function fillVlanSelect(portId, vlan) {
    console.log('fill');
    var selectId = "pointform-vlan";
    disableSelect(selectId);
    if (portId != "" && portId != null) {
        for (var i = meicanTopo['ports'].length - 1; i >= 0; i--) {
            if (meicanTopo['ports'][i].id == portId) {
                var ranges = meicanTopo['ports'][i].vlan_range.split(",");
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
            }
            
        }
        enableSelect(selectId);
    }
}

function clearSelect(object) {
    $('#' + object).children().remove();
}

function disableSelect(object) {
    clearSelect(object);
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
        fillVlanSelect();
    });
    
    $('#pointform-network').on('change', function() {
        if(hasLocation(this.value)){
            disableSelect("pointform-port");
            fillLocationSelect(this.value)
        }else{
            disableSelect("pointform-location");
            fillPortSelect(this.value);
        }
        fillVlanSelect();
    });

    $('#pointform-location').on('change', function() {
        fillPortSelect(null, null, this.value);
    });
    
    $('#pointform-port').on('change', function() {
        fillVlanSelect(this.value);
    });
}
