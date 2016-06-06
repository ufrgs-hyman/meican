/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap = new LMap('canvas');
var meicanTopo = [];
var viewer;
var lsidebar;
var circuits;

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

    $("#refresh-btn").on('click', function() {
        clearCircuits();
        loadCircuits();
    });
    
    initCanvas(); 
    initMenu();   
    loadDomains();
    
});

function loadTraffic() {
    for (var i = meicanMap._nodes.length - 1; i >= 0; i--) {
        for (var portId in meicanMap._nodes[i].options.ports) {
            if(meicanMap._nodes[i].options.ports[portId].link.out != null) {
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
    $('#canvas').on('lmap.nodeClick', function(e, node) {
        var circuitsList = [];
        for (var portId in node.options.ports) {
            for (var k = node.options.ports[portId].link.circuits.length - 1; k >= 0; k--) {
                circuitsList[node.options.ports[portId].link.circuits[k].id] = node.options.ports[portId].link.circuits[k].external_id;
            }
        }
        var circuitsHtml = '';
        for (var circuitId in circuitsList) {
            circuitsHtml += circuitsList[circuitId] + '<br>';
        };
        node.setPopupContent(
            'Domain: <b>' + meicanMap.getDomain(node.options.domainId).name + 
            '</b><br>Device: <b>' + node.options.name + '</b><br><br>' +
            'Circuits:<br>' + circuitsHtml
        );
    });

    $('#canvas').on('lmap.linkClick', function(e, link) {
    });
}

function buildPopupContent(type, name, domainName) {
    switch(type) {
        case 'dev':
            return 
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
                        response[dev][port].cap,
                        'dev'+dev, 
                        response[dev][port].link.dev ? 'dev'+response[dev][port].link.dev : null, 
                        response[dev][port].link.port ? response[dev][port].link.port : null,
                        'dev');
                } 
            }  
            
            loadCircuits();       
        }
    });
}

function loadCircuits() {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-all?status=ACTIVE&type=OSCARS',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            circuits = response;
            addCircuits(circuits);
        }
    });
}

function clearCircuits() {
    circuits = [];
    for (var i = meicanMap.getNodes().length - 1; i >= 0; i--) {
        for (var portId in meicanMap.getNodes()[i].options.ports) {
            var link = meicanMap.getNodes()[i].options.ports[portId].link;
            link.circuits = [];
            if(link.in) {
                link.in.options.traffic = 0;
                link.out.options.traffic = 0;
                var color; 
                color = "#35E834";
                link.in.setStyle({color: color});
                link.out.setStyle({color: color});
            }
        }
    };
}

function addCircuits(circuits) {
    for (var i = circuits.length - 1; i >= 0; i--) {
        for (var j = circuits[i].fullPath.length - 1; j >= 0; j--) {
            var nodeName = circuits[i].fullPath[j].port_urn.split(':')[4].split('=')[1];
            var node = meicanMap.getNodeByName(nodeName);
            var portName = circuits[i].fullPath[j].port_urn.split(':')[5].split('=')[1];
            for (var portId in node.options.ports) {
                if(portName == node.options.ports[portId].name) {
                    node.options.ports[portId].link.circuits.push(circuits[i]);
                    if(node.options.ports[portId].link.out != null) {
                        var circuitsList = [];
                        for (var k = node.options.ports[portId].link.circuits.length - 1; k >= 0; k--) {
                            circuitsList[node.options.ports[portId].link.circuits[k].id] = node.options.ports[portId].link.circuits[k].external_id;
                        }
                        var circuitsHtml = '';
                        for (var circuitId in circuitsList) {
                            circuitsHtml += circuitsList[circuitId] + '<br>';
                        };
                        var linkOut = node.options.ports[portId].link.out;
                        linkOut.bindPopup(
                        'Link between <b>' + 
                        meicanMap.getNode(linkOut.options.from).options.name +
                        '</b> and <b>' +
                        meicanMap.getNode(linkOut.options.to).options.name +
                        '</b><br><br>Circuits:<br>' + circuitsHtml);
                    }
                }
            }
        }
    }
    loadCircuitTraffic(); 
}

function loadCircuitTraffic() {
    for (var i = circuits.length - 1; i >= 0; i--) {
        var nodeName = circuits[i].fullPath[0].port_urn.split(':')[4].split('=')[1];
        var portName = circuits[i].fullPath[0].port_urn.split(':')[5].split('=')[1];
        var vlan = circuits[i].fullPath[0].vlan;
        $.ajax({
            url: baseUrl+'/monitoring/traffic/get?dev=' + nodeName +
                '&port=' + portName + '&vlan=' + vlan + '&dir=in',
            dataType: 'json',
            method: "GET",
            success: function(response) {
                for (var j = circuits.length - 1; j >= 0; j--) {
                    var nodeName = circuits[j].fullPath[0].port_urn.split(':')[4].split('=')[1];
                    var portName = circuits[j].fullPath[0].port_urn.split(':')[5].split('=')[1];
                    var vlan = circuits[j].fullPath[0].vlan;
                    if(response.dev == nodeName &&
                            response.port == portName &&
                            response.vlan == vlan) {
                        if(circuits[j]['traffic']) {
                            circuits[j]['traffic'].in = response.traffic;
                        } else {
                            circuits[j]['traffic'] = {
                                in: response.traffic,
                                out: 0
                            }
                        }
                        break;
                    }
                }

                $.ajax({
                    url: baseUrl+'/monitoring/traffic/get?dev=' + nodeName +
                        '&port=' + portName + '&vlan=' + vlan + '&dir=out',
                    dataType: 'json',
                    method: "GET",
                    success: function(response) {
                        //pegar trafego e gravar no circuito
                        for (var j = circuits.length - 1; j >= 0; j--) {
                            var nodeName = circuits[j].fullPath[0].port_urn.split(':')[4].split('=')[1];
                            var portName = circuits[j].fullPath[0].port_urn.split(':')[5].split('=')[1];
                            var vlan = circuits[j].fullPath[0].vlan;
                            if(response.dev == nodeName &&
                                    response.port == portName &&
                                    response.vlan == vlan) {
                                if(circuits[j]['traffic']) {
                                    circuits[j]['traffic'].out = response.traffic;
                                } else {
                                    circuits[j]['traffic'] = {
                                        in: 0,
                                        out: response.traffic
                                    }
                                }
                                break;
                            }
                        }
                    }
                });
            }
        });
    };

    setTimeout(function() {
        setLinkStatus();
    }, 2000);
}

function setLinkStatus() {
    //pegar links associados a circuitos e atualizar sua banda (cor)
    console.log('setting colors');
    for (var j = meicanMap.getLinks().length - 1; j >= 0; j--) {
        var link = meicanMap.getLinks()[j];
        var linkCircuits = meicanMap.getLinks()[j].options.port.link.circuits;
        var color; 
        if(linkCircuits.length > 0) {
            for (var i = linkCircuits.length - 1; i >= 0; i--) {
                link.options.traffic += linkCircuits[i].traffic.in;
            };
        } 
        color = getColorByStatusAndTraffic('ok', (link.options.traffic)*8/1000000, link.options.port.cap);
        link.setStyle({color: color});
    }
}

function getColorByStatusAndTraffic(status, traffic, cap) {
    console.log(status, traffic, cap);
    if (status == 'down') {
        return '#000';
    } else if (cap == null) {
        return '#ccc';
    } else if(traffic < cap*0.6) {
        return '#35E834';
    } else if(traffic < cap*0.9) {
        return "#FF7619";
    } else {
        return "#E8160C";
    } 
}

