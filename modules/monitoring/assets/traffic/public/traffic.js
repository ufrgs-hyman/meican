/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap = new LMap('canvas');
var meicanGraph = new VGraph("canvas");
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
    $('input[name="mode"]').on('ifChecked', function(){
        if(this.value == 'map') {
            viewer = meicanMap;
            meicanGraph.hide();
            meicanMap.show($('input[name="node-type"]:checked').val());
        } else {
            viewer = meicanGraph;
            meicanMap.hide();
            meicanGraph.show($('input[name="node-type"]:checked').val());
        }
    });

    $('input[name="node-type"]').on('ifChecked', function(){
        viewer.setNodeType(this.value);
    });
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
        var node = meicanGraph.getNode(nodeId);
        meicanGraph.showPopup(
            nodeId, 
            buildPopupContent(
                node.type, 
                node.label, 
                node.domainId ? meicanGraph.getDomain(node.domainId).name : null
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
    if(mode != 'map') {
        meicanGraph.closePopups();
    } else {
        meicanMap.closePopups();
    }
}

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            meicanTopo['dom'] = response;
            meicanMap.setDomains(response);
            meicanGraph.setDomains(response);
            meicanGraph.addNodes(response, "dom", true);
            loadDomainLinks();
            loadDevices();
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
                meicanMap.addNode(response[i], 'net');
            };
            loadNetworkLinks();
        }
    });
}

function loadDevices() {
    //console.log("load devs")
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
            meicanGraph.addNodes(response, 'dev', true);
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

function loadDomainLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-domain-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanTopo['dom']['links'] = response;
            meicanGraph.addLinks(response, 'dom');            
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

function loadDevicePorts() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-device-ports',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            meicanTopo['dev']['ports'] = response;
            //meicanGraph.addLinks(response, 'dev');
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

