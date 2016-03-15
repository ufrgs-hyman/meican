//var meicanGMap = new MeicanGMap("map-canvas");
var meicanMap = new MeicanLMap('canvas');
var meicanGraph = new MeicanVGraph("canvas");
var meicanTopo = [];
var mode = 'map';
var path = [];
var lsidebar;

$(document).ready(function() {
    meicanMap.show("rnp", 'dev');
    $(".sidebar-mini").addClass("sidebar-collapse");

    $('input[date-range="enabled"]').daterangepicker({
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

    $("#home").on("click",'.next-btn', function() {
        lsidebar.open("path");
    });

    $("#path").on("click",'.next-btn', function() {
        lsidebar.open("requirements");
    });

    $("#requirements").on("click",'.next-btn', function() {
        lsidebar.open("schedule");
    });

    $("#schedule").on("click",'.next-btn', function() {
        lsidebar.open("confirm");
    });

    $("#confirm").on("click",'.next-btn', function() {
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
    
    $("#add-point").click(function() {
        addPoint();
        return false;
    });

    $("#point-modal").on('click','.save-btn',function() {
        setPoint(
            $("#point-modal").find('.point-position').text(),
            $("#dom-select option:selected").text(),
            $("#net-select").val() == '' ? 'none' : $("#net-select option:selected").text(),
            $("#dev-select option:selected").text(),
            $("#dev-select").val(),
            $("#port-select option:selected").text(),
            $("#port-select").val(),
            null,
            $("#vlan-select").val());
        $("#point-modal").modal('hide');
        return false;
    });

    $("#point-modal").on('click','.cancel-btn',function() {
        $("#point-modal").modal('hide');
        return false;
    });

    $("#switch-mode").on('click', function() {
        if(mode != 'map') {
            meicanGraph.hide();
            meicanMap.show("rnp", 'dev');
            for (var i = 0; i < meicanTopo['dev'].length; i++) {
                meicanMap.addMarker(meicanTopo['dev'][i], 'dev');
            }
            mode = 'map';
        } else {
            meicanMap.hide();
            meicanGraph.show();
            meicanGraph.addNodes(meicanTopo['dev'], 'dev', true);
            meicanGraph.addLinks(meicanTopo['dev']['links'], 'dev');
            mode = 'graph';
        }
    });

    $('#canvas').on('markerClick', function(e, marker) {
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

    $('#canvas').on('nodeClick', function(e, nodeId) {
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
        showPointModal(0, $(this).parent().attr('data-node'));
        return false;
    });

    $("#canvas").on("click",'.set-destination', function() {
        lsidebar.open("path");
        //setDestinationPoint($(this).parent().attr('data-node'));
        closePopups();
        showPointModal($('.point').length - 1, $(this).parent().attr('data-node'));
        return false;
    });

    $("#canvas").on("click",'.add-waypoint', function() {
        lsidebar.open("path");
        closePopups();
        addWayPoint($(this).parent().attr('data-node'));
        return false;
    });

    $(".fa-arrow-down").click(function() {
    });

    $("#path").on('click','.fa-trash', function() {
        $(this).parent().parent().parent().parent().parent().remove();
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
        
    loadDomains();

    lsidebar = L.control.lsidebar('lsidebar').addTo(meicanMap.getMap());
    lsidebar.open("home");
    initEditPointSelects();
});

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

function showPointModal(pointPos, nodeId) {
    var marker = meicanMap.getMarker(nodeId);
    $("#point-modal").find('.point-position').text(pointPos);
    $("#dom-select").val(marker.options.domainId);
    fillNetworkSelect(marker.options.domainId);
    fillDeviceSelect(marker.options.domainId, null, marker.options.id.replace('dev',''));
    fillPortSelect(marker.options.id.replace('dev',''), $($(".point")[pointPos]).find('.port-id').val()); 
    fillVlanSelect();   
    $("#point-modal").modal("show");
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

function setPoint(position, dom, net, dev, devId, port, portId, urn, vlan) {
    $($(".point")[position]).find('.dom-l').text(dom);
    $($(".point")[position]).find('.net-l').text(net);
    $($(".point")[position]).find('.dev-l').text(dev);
    $($(".point")[position]).find('.dev-l').attr('data', devId);
    $($(".point")[position]).find('.port-l').text(port);
    $($(".point")[position]).find('.port-id').val(portId);
    $($(".point")[position]).find('.urn').val(urn);
    $($(".point")[position]).find('.vlan').val(vlan);
    $($(".point")[position]).find('.timeline-body').slideDown();
    var element = $($(".point")[position]).find('.fa-plus'); 
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
            '<div class="point-default">'+
              'Network: <label data="" class="point-info net-l">none</label><br>'+
              'Device: <label data="" class="point-info dev-l">none</label><br>'+
              'Port: <label class="point-info port-l">none</label><br>'+
              '<input class="port-id" type="hidden" name="ReservationForm[path][port][]">'+
            '</div>'+
            '<div class="point-advanced" hidden>'+
              'URN: <label class="point-info urn-l">none</label><br>'+
              '<input class="urn" type="hidden" name="ReservationForm[path][urn][]">'+
            '</div>'+
            'VLAN: <label class="point-info vlan-l">Auto</label>'+
            '<input class="vlan" type="hidden" name="ReservationForm[path][vlan][]">'+
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
            //meicanGraph.addNodes(response, 'dev', true);
            for (var i = 0; i < response.length; i++) {
                meicanMap.addMarker(
                    response[i].id + 'dev',
                    response[i].name,
                    'dev',
                    response[i].domain_id,
                    response[i].latitude,
                    response[i].longitude);
            };
            //meicanGraph.fit();
            loadDeviceLinks();
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
    clearSelect('dom-select');
    $("#dom-select").append('<option value="">' + I18N.t('loading') + '</option>');
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        data: {
            cols: JSON.stringify(['id','name']),
        },
        dataType: 'json',
        success: function(domains){
            clearSelect("dom-select");
            $("#dom-select").append('<option value="">' + I18N.t('select') + '</option>');
            for (var i = 0; i < domains.length; i++) {
                $("#dom-select").append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
            }
            enableSelect("dom-select");
        },
    });
}

function fillNetworkSelect(domainId, networkId, initDisabled) {
    disableSelect("net-select");
    clearSelect("net-select");
    if (domainId != "" && domainId != null) {
        $("#net-select").append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/network/get-by-domain',
            data: {
                id: domainId,
            },
            dataType: 'json',
            success: function(response){
                clearSelect("net-select");
                $("#net-select").append('<option value="">' + I18N.t('select') + '</option>');
                if (!initDisabled) enableSelect("net-select");
                for (var i = 0; i < response.length; i++) {
                    $("#net-select").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                }
                if (networkId != null) {
                    $("#net-select").val(networkId);
                }
            }
        });
    } 
}

function fillDeviceSelect(domainId, networkId, deviceId, initDisabled) {
    disableSelect("dev-select");
    clearSelect("dev-select");
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
        $("#dev-select").append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/device/get-by-' + parent[0],
            dataType: 'json',
            data: {
                id: parent[1],
            },
            success: function(response){
                clearSelect("dev-select");
                $("#dev-select").append('<option value="">' + I18N.t('select') + '</option>');
                if (!initDisabled) enableSelect("dev-select");
                for (var i = 0; i < response.length; i++) {
                    if (response[i].name == "") response[i].name = "default";
                    $("#dev-select").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                }
                if (deviceId != null && deviceId != "") {
                    $("#dev-select").val(deviceId);
                }
            }
        });
    } 
}

function fillPortSelect(deviceId, portId) {
    disableSelect("port-select");
    clearSelect("port-select");
    if (deviceId != "" && deviceId != null) {
        $("#port-select").append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/circuits/reservation/get-port-by-device',
            dataType: 'json',
            data: {
                id: deviceId,
                cols: JSON.stringify(['id','name']),
            },
            success: function(response){
                clearSelect("port-select");
                $("#port-select").append('<option value="">' + I18N.t('select') + '</option>');
                enableSelect("port-select");
                for (var i = 0; i < response.length; i++) {
                    var name = response[i].name;
                    if (response[i].port == "") {
                        name = I18N.t("default");
                    }
                    $("#port-select").append('<option value="' + response[i].id + '">' + name + '</option>');
                }
                if (portId != null && portId != "") $("#port-select").val(portId);
            }
        });
    } 
}

function fillVlanSelect(portId, vlan) {
    disableSelect("vlan-select");
    clearSelect("vlan-select");
    if (portId != "" && portId != null) {
        $("#vlan-select").append('<option value="">' + I18N.t('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/port/get-vlan-range',
            dataType: 'json',
            data: {
                id: portId,
            },
            success: function(response){
                clearSelect("vlan-select");
                $("#vlan-select").append('<option value="auto">Auto</option>');
                if(response) {
                    var ranges = response.split(",");
                    for (var i = 0; i < ranges.length; i++) {
                        var interval = ranges[i].split("-");
                        if (interval.length > 1)
                            $("#vlan-select").append('<option value="' + ranges[i] + '">' + ranges[i] + '</option>');
                    }

                    for (var i = 0; i < ranges.length; i++) {
                        var interval = ranges[i].split("-");
                        var low = parseInt(interval[0]);
                        var high = low;
                        if (interval.length > 1) {
                            high = parseInt(interval[1]);
                            for (var j = low; j < high+1; j++) {
                            $("#vlan-select").append('<option value="' + j + '">' + j + '</option>');
                        }
                        } else {
                            $("#vlan-select").append('<option value="' + low + '">' + low + '</option>');
                        }
                        
                        if (vlan != null && vlan != "") {
                            $("#vlan-select").val(vlan);
                        }
                    }
                    enableSelect("vlan-select");
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
    
    $('#dom-select').on('change', function() {
        fillNetworkSelect(this.value);
        fillDeviceSelect(this.value);
        fillPortSelect();
        fillVlanSelect();
    });
    
    $('#net-select').on('change', function() {
        fillDeviceSelect($('#dom-select').val(), this.value);
        fillPortSelect();
        fillVlanSelect();
    });
    
    $('#dev-select').on('change', function() {
        fillPortSelect(this.value);
        fillVlanSelect();
    });
    
    $('#port-select').on('change', function() {
        fillVlanSelect(this.value);
    });
}
