/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap = new LMap('canvas');
var meicanTopo = [];
var viewer;
var lsidebar;

$(document).ready(function() {
    meicanMap.show('dev');
    viewer = meicanMap;
    $(".sidebar-toggle").remove();
    $(".sidebar-mini").addClass("sidebar-collapse");

    lsidebar = L.control.lsidebar('lsidebar').addTo(meicanMap._map);

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
    });
    
    initCanvas(); 
    initMenu();   
    loadDomains();
});

function loadTraffic() {
    for (var i = meicanMap._nodes.length - 1; i >= 0; i--) {
        for (var portId in meicanMap._nodes[i].options.ports) {
            if(meicanMap._nodes[i].options.ports[portId].link != null) {
                $.ajax({
                    url: baseUrl+'/monitoring/traffic/get?port=' + portId + '&dir=in',
                    dataType: 'json',
                    method: "GET",
                    success: function(response) {
                        var color; 
                        if(response.traffic < 0.4) {
                            color = "#35E834";
                        } else if(response.traffic < 0.8) {
                            color = "#FF7619";
                        } else {
                            color = "#E8160C";
                        } 
                        meicanMap.getNode('dev' + response.dev).options.ports[parseInt(response.port)].link.setStyle({color: color});
                    }
                });
            }
        }
    };
}

function initMenu() {
}

function initCanvas() {
    $('#canvas').on('lmap.nodeClick', function(e, marker) {
        marker.setPopupContent(
            buildPopupContent(
                marker.options.type, 
                marker.options.name, 
                meicanMap.getDomain(marker.options.domainId).name
            )
        );
    });
}

function buildPopupContent(type, name, domainName) {
    switch(type) {
        case 'dom':
            return 'Domain: <b>' + name + '</b>';
        case 'prov':
            return '';
        case 'net':
            return '';
        case 'dev':
            return 'Domain: <b>' + domainName + '</b><br>Device: <b>' + name + '</b>';
    }
}

function closePopups() {
    meicanMap.closePopups();
}

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-by-name?name=cipo.rnp.br',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            meicanTopo['dom'] = response;
            meicanMap.setDomains([response]);
            loadDevices();
        }
    });
}

function loadDevices() {
    $.ajax({
        url: baseUrl+'/topology/device/get-by-domain?id=' + meicanMap.getDomains()[0].id,
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
            loadDevicePorts();
        }
    });
}

function loadDevicePorts() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-device-ports?dom=' + meicanMap.getDomains()[0].id + "&type=NMWG",
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanTopo['dev']['ports'] = response;
            for (var dev in response) {
                for (var port in response[dev]) {
                    meicanMap.addPort(
                        port, 
                        response[dev][port].name, 
                        response[dev][port].dir, 
                        'dev'+dev, 
                        response[dev][port].link ? 'dev'+response[dev][port].link : null, 
                        'dev');
                } 
            }  
            loadTraffic();         
        }
    });
}

function loadCircuits() {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-all?status=ACTIVE&type=OSCARS',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            console.log(response);
        }
    });
}

