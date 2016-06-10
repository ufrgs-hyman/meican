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

function initMenu() {
}

function initCanvas() {
    $('#canvas').on('lmap.nodeClick', function(e, node) {
        var circuitsList = [];
        for (var portId in node.options.ports) {
            for (var k = node.options.ports[portId].circuits.length - 1; k >= 0; k--) {
                circuitsList[node.options.ports[portId].circuits[k].id] = node.options.ports[portId].circuits[k].external_id;
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
        console.log(link);
        //$("#map-l").find('.traffic-stats').css('height', 250);
        //$("#map-l").find('.traffic-stats').css('width', 475);
        if(link.options.fromPort.circuits.length > 0)
            loadStats(link, $("#map-l").find('.traffic-stats'));

        if(link.options.fromPort.status == 0 || link.options.toPort.status == 0) {
            $("#map-l").find('.link-status').html("Status: <b>Unknown</b>");
        } else if(link.options.fromPort.status == 2 || link.options.toPort.status == 2) {
            $("#map-l").find('.link-status').html("Status: <b>Down</b>");
        } else {
            $("#map-l").find('.link-status').html("Status: <b>Up</b>");
        }
    });
}

function loadStats(link, divElement) {
    statsGraphic = $.plot($(divElement), [], {
      grid: {
        hoverable: true,
        borderColor: "#f3f3f3",
        borderWidth: 1,
        tickColor: "#f3f3f3"
      },
      series: {
        shadowSize: 0,
        lines: {
          show: true
        },
        points: {
          show: true
        }
      },
      lines: {
        fill: true,
      },
      yaxis: {
        show: true,
        tickFormatter: function(val, axis) {
            if (Math.abs(val) < 1) 
                val = val.toFixed(4);
            else 
                val = val.toFixed(2);
            return Math.abs(val) + " Mbps";
        }
      },
      xaxis: {
        mode: "time",
        timezone: 'browser',
        show: true,
      },
      legend: {
        noColumns: 2,
      }
    });

    var domain = 'cipo.rnp.br';
    var linkCircuits = link.options.fromPort.circuits;
    var srcDev = link.options.fromPort.device.options.name;
    var dstDev = link.options.toPort.device.options.name;
    var statsData =[];

    for (var i = linkCircuits.length - 1; i >= 0; i--) {
        var circuitDev = linkCircuits[i].fullPath[0].device;
        var circuitPort = linkCircuits[i].fullPath[0].port;
        var circuitVlan = linkCircuits[i].fullPath[0].vlan;

        $.ajax({
            url: baseUrl+'/monitoring/traffic/get-vlan-history?dom=' + domain + '&dev=' + circuitDev +
                '&port=' + circuitPort + '&vlan=' + circuitVlan + '&dir=out' + '&interval=' + 0,
            dataType: 'json',
            method: "GET",
            success: function(data) {
                var dataOut = [];
                for (var i = 0; i < data.traffic.length; i++) {
                    dataOut.push([moment.unix(data.traffic[i].ts), data.traffic[i].val*8/1000000]);
                }
                statsData.push({label: dstDev + ' to ' + srcDev, data: dataOut, color: "#3c8dbc" });

                if(data.traffic.length > 0) {
                    $.ajax({
                        url: baseUrl+'/monitoring/traffic/get-vlan-history?dom=' + domain + '&dev=' + circuitDev +
                            '&port=' + circuitPort + '&vlan=' + circuitVlan + '&dir=in' + '&interval=' + 0,
                        dataType: 'json',
                        method: "GET",
                        success: function(data) {
                            var dataIn = [];
                            for (var i = 0; i < data.traffic.length; i++) {
                                dataIn.push([moment.unix(data.traffic[i].ts), 0-(data.traffic[i].val*8/1000000)]);
                            }
                            statsData.push({label: srcDev + ' to ' + dstDev, data: dataIn, color: "#f56954" });

                            statsGraphic.setData(statsData);
                            statsGraphic.setupGrid();
                            statsGraphic.draw();
                        }
                    });
                }
            }
        });
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
                    loadPortStatus(meicanMap.getNode('dev'+dev).options.name, response[dev][port].name);
                } 
            }  
            
            loadCircuits();       
        }
    });
}

function loadPortStatus(dev, port) {
    $.ajax({
        url: baseUrl+'/monitoring/status/get-by-port?dev=' + dev + '&port=' + port,
        dataType: 'json',
        method: "GET",
        success: function(response) {
            var node = meicanMap.getNodeByName(response.dev);
            for (var portId in node.options.ports) {
                if(response.port == node.options.ports[portId].name) {
                    node.options.ports[portId].status = response.status;
                    break;
                }
            }
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
            prepareCircuitsDevPath();
        }
    });
}

function prepareCircuitsDevPath() {
    for (var i = circuits.length - 1; i >= 0; i--) {
        circuits[i].devPath = [];
        for (var j = 0; j <= circuits[i].fullPath.length - 2; j++) {
            var nodeName = circuits[i].fullPath[j].port_urn.split(':')[4].split('=')[1];
            var node = meicanMap.getNodeByName(nodeName);
            var dstNodeName = circuits[i].fullPath[j+1].port_urn.split(':')[4].split('=')[1];
            var dstNode = meicanMap.getNodeByName(dstNodeName);
            if(node.options.id != dstNode.options.id) {
                circuits[i].devPath.push(node.options.id);
                circuits[i].devPath.push(dstNode.options.id);
            }
        }
    }
}

function clearCircuits() {
    circuits = [];
    for (var i = meicanMap.getNodes().length - 1; i >= 0; i--) {
        for (var portId in meicanMap.getNodes()[i].options.ports) {
            var port = meicanMap.getNodes()[i].options.ports[portId];
            port.circuits = [];
            if(port.linkIn) {
                port.linkIn.options.traffic = 0;
                port.linkOut.options.traffic = 0;
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
                    node.options.ports[portId].circuits.push(circuits[i]);
                    if(node.options.ports[portId].linkOut != null) {
                        var circuitsList = [];
                        for (var k = node.options.ports[portId].circuits.length - 1; k >= 0; k--) {
                            circuitsList[node.options.ports[portId].circuits[k].id] = node.options.ports[portId].circuits[k].external_id;
                        }
                        var circuitsHtml = '';
                        for (var circuitId in circuitsList) {
                            circuitsHtml += circuitsList[circuitId] + '<br>';
                        };
                        var linkOut = node.options.ports[portId].linkOut;
                        linkOut.bindPopup(
                        'Link between <b>' + 
                        meicanMap.getNode(linkOut.options.from).options.name +
                        '</b> and <b>' +
                        meicanMap.getNode(linkOut.options.to).options.name +
                        '</b><br>Capacity: <b>' + node.options.ports[portId].cap + ' Mbps</b><br>Circuits:<br>' + circuitsHtml + 
                        '<br><div class="traffic-stats" style="width: 475px; height: 250px"></div>',
                        {'maxWidth': '500'});
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
                        circuits[j].fullPath[0]['device'] = response.dev;
                        circuits[j].fullPath[0]['port'] = response.port; 
                        circuits[j]['trafficIn'] = Math.round(response.traffic*8/1000000*100) / 100;
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
                                circuits[j]['trafficOut'] = Math.round(response.traffic*8/1000000*100) / 100;
                                break;
                            }
                        }
                    }
                });
            }
        });
    };

    setLinkStatusWhenReady();
}

function setLinkStatusWhenReady() {
    console.log('trying set link status');
    if (areCircuitsReady()) {
        //console.log("setbounds");
        setLinkStatus();
    } else {
        setTimeout(function() {
            setLinkStatusWhenReady();
        }, 100);
    }
}

function areCircuitsReady() {
    for (var i = 0; i < circuits.length; i++) {
        if(circuits[i].trafficOut == null)
            return false;
    }
    
    return true;
}

function setLinkStatus() {
    //pegar links associados a circuitos e atualizar sua banda (cor)
    console.log('setting colors');
    for (var i = circuits.length - 1; i >= 0; i--) {
        for (var j = 0; j <= circuits[i].devPath.length - 2; j++) {
            var linkIn = meicanMap.getLink(circuits[i].devPath[j]+circuits[i].devPath[j+1]);
            var linkOut = meicanMap.getLink(circuits[i].devPath[j+1]+circuits[i].devPath[j]);
            
            if(linkIn)
                linkIn.options.traffic += circuits[i].trafficIn;
            if(linkOut)
                linkOut.options.traffic += circuits[i].trafficOut;

            //console.log(linkIn, linkOut);
        };
    };

    for (var j = meicanMap.getLinks().length - 1; j >= 0; j--) {
        var link = meicanMap.getLinks()[j];
        var fromPortStatus = link.options.fromPort.status;
        var toPortStatus = link.options.toPort.status;
        var portCap = link.options.fromPort.cap;

        link.options.traffic = link.options.traffic ? link.options.traffic : 0;

        link.setStyle({
            opacity: 0.7,
            color: buildColorByStatusAndTraffic(
                fromPortStatus, 
                toPortStatus,
                portCap,
                link.options.traffic, 
                link)
        });
        //TODO banda sobre os links
        //PROBLEMA TextPath nao resolve a orientacao on the fly.
        //RESULTADO texto da banda fica invertido sobre o link.
        /*if(link.options.traffic != null) {
            link.setText(link.options.traffic + "Mbps", {center: true});
        }*/
    }
}

function buildColorByStatusAndTraffic(fromPortStatus, toPortStatus, cap, traffic, link) {
    if (fromPortStatus == 2 || toPortStatus == 2) {
        //console.log('unknown status', link);
        return '#000';
    } else if (fromPortStatus == 0 || toPortStatus == 0) {
        //console.log('offline', link);
        return '#ccc';
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

