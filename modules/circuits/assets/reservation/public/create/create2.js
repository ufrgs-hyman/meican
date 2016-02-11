//var meicanGMap = new MeicanGMap("map-canvas");
var meicanMap = new MeicanLMap('canvas');
var meicanGraph = new MeicanGraph("canvas");
var meicanTopo = [];
var mode = 'map';
var path = [];

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
    });

    $('#canvas').on('markerClick', function(e, marker) {
        marker.setPopupContent('Domain: cipo.rnp.br<br>Device: POA<br><br><div class="btn-group">'+
            '<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'+
              'Options <span class="fa fa-caret"></span>'+
            '</button>'+
            '<ul data-marker="' + marker.options.id + '" class="dropdown-menu">'+
              '<li><a class="set-source" href="#">From here</a></li>'+
              '<li><a class="add-waypoint" href="#">Add waypoint</a></li>'+
              '<li><a class="set-destination" href="#">To here</a></li>'+
            '</ul>'+
          '</div>');
    });

    $("#canvas").on("click",'.set-source', function() {
        setSourcePoint($(this).parent().parent().attr('data-marker'));
    });

    $("#canvas").on("click",'.set-destination', function() {
        setDestinationPoint($(this).parent().parent().attr('data-marker'));
    });

    $("#canvas").on("click",'.set-waypoint', function() {
        addWayPoint($(this).parent().parent().attr('data-marker'));
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

    var lsidebar = L.control.lsidebar('lsidebar').addTo(meicanMap.getMap());
});

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
            //meicanLMap.setDomains(response);
            //meicanGMap.setDomains(response);
            //meicanMap.setDomains(response);
            /*meicanGraph.setDomains(response);
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
            meicanTopo['dev'] = true;
            //meicanGraph.addNodes(response, 'dev', true);
            for (var i = 0; i < response.length; i++) {
                meicanMap.addMarker(response[i], 'dev');
            };/*
            meicanGraph.fit();
            loadDeviceLinks();*/
        }
    });
}

function loadPorts() {
    if(meicanTopo['port']) return;
    $.ajax({
        url: baseUrl+'/topology/port/get-all-bidirectional',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','device_id'])
        },
        success: function(response) {
            meicanTopo['port'] = true;
            meicanGraph.addNodes(response, 'port', true);
            meicanGraph.fit();
            loadPortLinks();
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

function loadProviders() {
    if(meicanTopo['prov']) return;
    $.ajax({
        url: baseUrl+'/topology/provider/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude', 'domain_id'])
        },
        success: function(response) {
            meicanTopo['prov'] = response;
            /*meicanGraph.addNodes(response, 'prov');
            for (var i = 0; i < response.length; i++) {
                meicanMap.addMarker(response[i], 'prov');
            };*/
            loadProviderLinks();
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

function loadProviderLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-peerings',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanGraph.addLinks(response, 'prov');
            for (var src in response) {
                for (var i = 0; i < response[src].length; i++) {
                    meicanMap.addLink('prov'+src,'prov'+response[src][i], 'prov');
                }
            }
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
            meicanGraph.addLinks(response, 'dev');
            for (var src in response) {
                for (var i = 0; i < response[src].length; i++) {
                    meicanMap.addLink('dev'+src,'dev'+response[src][i], 'dev');
                }
            }           
        }
    });
}

function loadPortLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-port-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanGraph.addLinks(response, 'port');
        }
    });
}


