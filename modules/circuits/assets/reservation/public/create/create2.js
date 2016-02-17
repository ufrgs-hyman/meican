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
    
    $("#add-point").click(function() {
        addPoint();
        return false;
    });

    $("#switch-mode").on('click', function() {
        meicanMap.hide();
        meicanGraph.show();
        meicanGraph.addNodes(meicanTopo['dev'], 'dev', true);
        meicanGraph.addLinks(meicanTopo['dev']['links'], 'dev');
    });

    $('#canvas').on('markerClick', function(e, marker) {
        marker.setPopupContent('Domain: <b>' + meicanMap.getDomain(marker.options.domainId).name + 
            '</b><br>Device: <b>' + marker.options.name + '</b><br><br>'+
            '<div data-marker="' + marker.options.id + '">'+
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
            '<div data-marker="' + nodeId + '">'+
              '<button class="set-source">From here</button>'+
              '<button class="add-waypoint">Add waypoint</button>'+
              '<button class="set-destination">To here</button>'+
            '</div>');
    });

    $("#canvas").on("click",'.set-source', function() {
        lsidebar.open("path");
        setSourcePoint($(this).parent().attr('data-marker'));
        closePopups();
        $("#point-modal").modal("show");
        return false;
    });

    $("#canvas").on("click",'.set-destination', function() {
        lsidebar.open("path");
        setDestinationPoint($(this).parent().attr('data-marker'));
        closePopups();
        return false;
    });

    $("#canvas").on("click",'.add-waypoint', function() {
        lsidebar.open("path");
        addWayPoint($(this).parent().attr('data-marker'));
        closePopups();
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

function setSourcePoint(nodeId) {
    setPoint(0, nodeId);
}

function setDestinationPoint(nodeId) {
    setPoint($('.point').length - 1, nodeId);
}

function addWayPoint(nodeId) {
    addPoint($('.point').length - 1, nodeId);
}

function setPoint(position, nodeId) {
    console.log(position, nodeId);
    //var node = meicanMap.getMarker(nodeId);
    //var node = meicanGraph.getNode(nodeId);
    if(mode == 'map') {
        var marker = meicanMap.getMarker(nodeId);
        $($(".point")[position]).find('.dom-l').text(meicanMap.getDomain(marker.options.domainId).name);
        $($(".point")[position]).find('.dev-l').text(marker.options.name);
        $($(".point")[position]).find('.dev-l').attr('data', marker.options.id.replace('dev',''));
        $($(".point")[position]).find('.vlan').val('auto');
        $($(".point")[position]).find('.timeline-body').slideDown();
        var element = $($(".point")[position]).find('.fa-plus'); 
        element.removeClass('fa-plus');
        element.addClass('fa-minus');
    } else {
        var node = meicanGraph.getNode(nodeId);
    }

    drawPath();
}

function addPoint(position, markerId) {
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
                meicanMap.addMarker(response[i], 'dev');
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
    $("#dom-select").append('<option value="">' + I18N('loading') + '</option>');
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        data: {
            cols: JSON.stringify(['id','name']),
        },
        dataType: 'json',
        success: function(domains){
            clearSelect("dom-select");
            $("#dom-select").append('<option value="">' + I18N('select') + '</option>');
            for (var i = 0; i < domains.length; i++) {
                $("#dom-select").append('<option value="' + domains[i].id + '">' + domains[i].name + '</option>');
            }
        },
    });
}

function fillNetworkSelect(domainId, networkId, initDisabled) {
    disableSelect("net-select");
    clearSelect("net-select");
    if (domainId != "" && domainId != null) {
        $("#net-select").append('<option value="">' + I18N('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/network/get-by-domain',
            data: {
                id: domainId,
            },
            dataType: 'json',
            success: function(response){
                clearSelect("net-select");
                $("#net-select").append('<option value="">' + I18N('select') + '</option>');
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
        $("#dev-select").append('<option value="">' + I18N('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/device/get-by-' + parent[0],
            dataType: 'json',
            data: {
                id: parent[1],
            },
            success: function(response){
                clearSelect("dev-select");
                $("#dev-select").append('<option value="">' + I18N('select') + '</option>');
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
        $("#port-select").append('<option value="">' + I18N('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/circuits/reservation/get-port-by-device',
            dataType: 'json',
            data: {
                id: deviceId,
                cols: JSON.stringify(['id','name']),
            },
            success: function(response){
                clearSelect("port-select");
                $("#port-select").append('<option value="">' + I18N('select') + '</option>');
                enableSelect("port-select");
                for (var i = 0; i < response.length; i++) {
                    var name = response[i].name;
                    if (response[i].port == "") {
                        name = I18N("default");
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
        $("#vlan-select").append('<option value="">' + I18N('loading') + '</option>');
        $.ajax({
            url: baseUrl+'/topology/port/get-vlan-range',
            dataType: 'json',
            data: {
                id: portId,
            },
            success: function(response){
                clearSelect("vlan-select");
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

function clearSelect(endPointType, object) {
      $('#' + object).children().remove();
}

function disableSelect(endPointType, object) {
  $('#' + object).prop('disabled', true);
}

function enableSelect(endPointType, object) {
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
