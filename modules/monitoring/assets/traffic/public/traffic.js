/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap = new LMap('canvas');
var meicanTopo = [];
var lsidebar;
var circuits;
var statsGraphic;
var defaultColorIn = "#3c8dbc";
var defaultColorOut = "#000000";

$(document).ready(function() {
    meicanMap.show('dev');
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

    $("#map-l").on("click", ".agg-stats-btn", function() {
        if($(this).attr('agg') == 'true') {
            hideCircuitsOnGraphic();
            $(this).attr('agg', 'false');
        } else {
            showCircuitsOnGraphic();
            $(this).attr('agg', 'true');
        }
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
            (circuitsHtml != '' ? 'Circuits:<br>' + circuitsHtml : '')
        );
    });

    $('#canvas').on('lmap.linkClick', function(e, link) {
        console.log(link);
        //$("#map-l").find('.traffic-stats').css('height', 250);
        //$("#map-l").find('.traffic-stats').css('width', 475);
        if(link.options.fromPort.circuits.length > 0)
            initStats(link, $("#map-l").find('.traffic-stats'));

        if(link.options.fromPort.status == 0 || link.options.toPort.status == 0) {
            $("#map-l").find('.link-status').html("Status: <b>Unknown</b>");
        } else if(link.options.fromPort.status == 2 || link.options.toPort.status == 2) {
            $("#map-l").find('.link-status').html("Status: <b>Down</b>");
        } else {
            $("#map-l").find('.link-status').html("Status: <b>Up</b>");
        }
    });
}

function initStats(link, divElement) {
    statsGraphic = $.plot($(divElement), [], {
        mode: {
            type: 'circuit',
            seriesIn: 0,
            seriesOut: 0
        },
      grid: {
        hoverable: true,
        autoHighlight: false,
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
          show: false
        }
      },
      crosshair: {
        mode: "x"
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
        noColumns: 4,
        container: $("#map-l").find('.stats-legend'),    
        labelFormatter: function(label, series) {
            var mode = statsGraphic.getOptions().mode;
            if(mode.type == 'circuit') {
                if(series.stack == 'in')
                    return '<span style="margin-right: 10px; margin-left: 5px;">' + label + ' to MXSP</span>';
                else 
                    return '<span style="margin-right: 10px; margin-left: 5px;">' + label + ' to MXSC</span>';
            } else {
                if(mode.seriesIn == 0 && series.stack == 'in') {
                    mode.seriesIn++;
                    return '<span style="margin-right: 10px; margin-left: 5px;">' + series.direction + '</span>';
                } else if(mode.seriesOut == 0) {
                    mode.seriesOut++;
                    return '<span style="margin-right: 10px; margin-left: 5px;">' + series.direction + '</span>';
                }
            }
        }
      }
    });

    var linkCircuits = link.options.directedCircuits;
    var fromDev = link.options.fromPort.device.options.name;
    var toDev = link.options.toPort.device.options.name;
    var dataSeries = [];
    var loadedCircuitsCounter = { val: 0 };

    for (var i = linkCircuits.length - 1; i >= 0; i--) {
        loadTrafficHistory(fromDev, toDev, loadedCircuitsCounter, dataSeries, linkCircuits[i].circuit, linkCircuits[i].dir);
    }

    showGraphicWhenReady(loadedCircuitsCounter, linkCircuits.length);
}

function showGraphicWhenReady(loaded, total) {
    console.log('trying draw graphic');
    if (loaded.val == total*2) {
        hideCircuitsOnGraphic();
    } else {
        setTimeout(function() {
            showGraphicWhenReady(loaded, total);
        }, 100);
    }
}

function showCircuitsOnGraphic() {
    statsGraphic.getOptions().mode.type = 'circuit';
    statsGraphic.getOptions().mode.seriesIn = 0;
    statsGraphic.getOptions().mode.seriesOut = 0;
    for (var i = statsGraphic.getData().length - 1; i >= 0; i--) {
        statsGraphic.getData()[i].lines.lineWidth = 2;
        statsGraphic.getData()[i].color = statsGraphic.getData()[i].oldColor;
    };
    statsGraphic.setupGrid();
    statsGraphic.draw();
}

function hideCircuitsOnGraphic() {
    statsGraphic.getOptions().mode.type = 'global';
    statsGraphic.getOptions().mode.seriesIn = 0;
    statsGraphic.getOptions().mode.seriesOut = 0;
    var firstInPassed = false;
    var firstOutPassed = false;
    for (var i = statsGraphic.getData().length - 1; i >= 0; i--) {
        if(statsGraphic.getData()[i].stack == 'out') {
            if(!firstOutPassed) {
                firstOutPassed = true;
            } else {
                statsGraphic.getData()[i].lines.lineWidth = 0;
            }
        }

        if(statsGraphic.getData()[i].stack == 'in') {
            if(!firstInPassed) {
                firstInPassed = true;
            } else {
                statsGraphic.getData()[i].lines.lineWidth = 0;
            }
        }

        statsGraphic.getData()[i].oldColor = statsGraphic.getData()[i].color;
        statsGraphic.getData()[i].color = statsGraphic.getData()[i].stack == 'out' ? defaultColorOut : defaultColorIn;
    };
    statsGraphic.setupGrid();
    statsGraphic.draw();
}

function buildRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function loadTrafficHistory(fromDev, toDev, loadedCircuitsCounter, dataSeries, circuit, relativeDir) {
    var domain = 'cipo.rnp.br';
    var circuitDev = circuit.fullPath[0].device;
    var circuitPort = circuit.fullPath[0].port;
    var circuitVlan = circuit.fullPath[0].vlan;
    var color = buildRandomColor();

    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history?dom=' + domain + '&dev=' + circuitDev +
            '&port=' + circuitPort + '&vlan=' + circuitVlan + '&dir=out' + '&interval=' + 0,
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var dataOut = [];
            for (var i = 0; i < data.traffic.length; i++) {
                dataOut.push([moment.unix(data.traffic[i].ts), 
                    relativeDir == 'out' ? data.traffic[i].val*8/1000000 : (0-(data.traffic[i].val*8/1000000))]);
            }

            dataSeries.push({
                stack: relativeDir == 'out' ? 'in' : 'out', 
                label: circuit.external_id, 
                direction: relativeDir == 'out' ? fromDev + " to " + toDev : toDev + ' to ' + fromDev, 
                color: color,
                data: dataOut});

            statsGraphic.setData(dataSeries);
            loadedCircuitsCounter.val++;
        }
    });

    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history?dom=' + domain + '&dev=' + circuitDev +
            '&port=' + circuitPort + '&vlan=' + circuitVlan + '&dir=in' + '&interval=' + 0,
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var dataIn = [];
            for (var i = 0; i < data.traffic.length; i++) {
                dataIn.push([moment.unix(data.traffic[i].ts), 
                    relativeDir == 'out' ? (0-(data.traffic[i].val*8/1000000)) : data.traffic[i].val*8/1000000]);
            }

            dataSeries.push({
                stack: relativeDir == 'out' ? 'out' : 'in', 
                label: circuit.external_id, 
                direction: relativeDir == 'out' ? toDev + " to " + fromDev : fromDev + ' to ' + toDev, 
                color: color,
                data: dataIn});

            statsGraphic.setData(dataSeries);
            loadedCircuitsCounter.val++;
        }
    });
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

function reloadPortStatus(dev, port) {
    //for dev
    ///for port
    ///loadPortStatus()
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
        circuits[i].links = [];
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
                        var fromDev = meicanMap.getNode(linkOut.options.from).options.name;
                        var toDev = meicanMap.getNode(linkOut.options.to).options.name;
                        linkOut.bindPopup(
                        'Link between <b>' + 
                        meicanMap.getNode(linkOut.options.from).options.name +
                        '</b> and <b>' +
                        meicanMap.getNode(linkOut.options.to).options.name +
                        '</b><br>Capacity: <b>' + node.options.ports[portId].cap + ' Mbps</b>' + 
                        '<div class="pull-right"><button class="btn btn-default btn-sm agg-stats-btn">Show aggregate DCN traffic</button> ' + 
                        '</div><br>' + 
                        '<br><div class="traffic-stats" style="width: 610px; height: 250px"></div><br>' + 
                        '<div class="stats-legend"></div>',
                        {'maxWidth': '625'});
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
            
            if(linkIn) {
                linkIn.options.traffic += circuits[i].trafficIn;
                linkIn.options.directedCircuits.push({
                    circuit: circuits[i],
                    dir: 'in'
                });
                circuits[i].links.push(linkIn);
            }
            if(linkOut) {
                linkOut.options.traffic += circuits[i].trafficOut;
                linkOut.options.directedCircuits.push({
                    circuit: circuits[i],
                    dir: 'out'
                });
                circuits[i].links.push(linkOut);
            }
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
        //console.log('offline', link);
        return '#000';
    } else if (fromPortStatus == 0 || toPortStatus == 0) {
        //console.log('unkown', link);
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

