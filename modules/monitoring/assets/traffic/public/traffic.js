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
var loadedPortsStatusCounter = { val: 0 };
var refreshInterval;

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
    
    $("#map-l").on("switchChange.bootstrapSwitch", ".graphic-mode-switch", function(e, state) {
        if(state) {
            hideCircuitsOnGraphic();
        } else {
            showCircuitsOnGraphic();
        }
    });
    
    $("#auto-refresh-switch").bootstrapSwitch();
    $("#auto-refresh-switch").on('switchChange.bootstrapSwitch', function(event, state) {
        state ? enableAutoRefresh() : disableAutoRefresh();
    });

    var legend = L.control({position: 'bottomright'});

    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'info legend');

        div.innerHTML += '<label>Link status:</label><br>';
        div.innerHTML +=
            '<i style="background: #35E834"></i> ' +
            'Up and 0' + '&ndash;' + '59%*' +'<br>';
        div.innerHTML +=
            '<i style="background: #FFC604"></i> ' +
            'Up and 60' + '&ndash;' + '89%*' +'<br>';
        div.innerHTML +=
            '<i style="background: #E8160C"></i> ' +
            'Up and 90' + '&ndash;' + '100%*' +'<br>';
        div.innerHTML +=
            '<i style="background: #000"></i> ' +
            'Down<br>';
        div.innerHTML +=
            '<i style="background: #ccc"></i> ' +
            'Unknown<br>';
        div.innerHTML += '*of the capacity in use';

        return div;
    };

    legend.addTo(meicanMap._map);
    
    initCanvas(); 
    initMenu();   
    loadDomains();
    enableAutoRefresh();
});

function disableAutoRefresh() {
    clearInterval(refreshInterval);
}

function enableAutoRefresh() {
    refreshInterval = setInterval(refresh, 120000);
}

function initMenu() {
}

function initCanvas() {
    $('#canvas').on('lmap.nodeClick', function(e, node) {
        node.setPopupContent(
            'Domain: <b>' + meicanMap.getDomain(node.options.domainId).name + 
            '</b><br>Device: <b>' + node.options.name + '</b><br>'
        );
    });

    $('#canvas').on('lmap.linkClick', function(e, link) {
        console.log(link);
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
        noColumns: 1,
        container: $("#map-l").find('.stats-legend'),    
        labelFormatter: function(label, series) {
            return buildLegend(label,series);
        }
      }
    });

    $(".graphic-mode-switch").bootstrapSwitch();

    var updateLegendTimeout = null; 
    var latestPosition = null; 
     
    function updateLegend(legends) { 
        updateLegendTimeout = null; 
         
        var pos = latestPosition; 
        var trafficIn = 0;
        var trafficOut = 0;
         
        var axes = statsGraphic.getAxes(); 
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || 
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) 
            return; 

        var i, j, dataset = statsGraphic.getData(); 
        for (i = 0; i < dataset.length; ++i) { 
            var series = dataset[i]; 

            // find the nearest points, x-wise 
            for (j = 0; j < series.data.length; ++j) 
                if (series.data[j][0] > pos.x) 
                    break; 
             
            // now interpolate 
            var y, p1 = series.data[j - 1], p2 = series.data[j]; 
            if (p1 == null) 
                y = p2[1]; 
            else if (p2 == null) 
                y = p1[1]; 
            else 
                y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);

            if(statsGraphic.getOptions().mode.type == 'circuit') {
                $("#map-l").find('.traffic-value').eq(i).text(Math.abs(y).toFixed(5));
            } else {
                if(series.stack == 'in') {
                    trafficIn += y;
                } else {
                    trafficOut += y;
                }
            }
        } 

        if(statsGraphic.getOptions().mode.type != 'circuit') {
            for (i = 0; i < dataset.length; ++i) { 
                var series = dataset[i]; 

                $("#map-l").find('.legendLabel').eq(i).text(
                    $("#map-l").find('.legendLabel').eq(i).text().replace(/=.*/, "= " + 
                        Math.abs(series.stack == 'in' ? trafficIn : trafficOut).toFixed(5) + ' Mbps'));
            }
        }
    } 
     
    $(divElement).bind("plothover",  function (event, pos, item) { 
        latestPosition = pos; 
        if (!updateLegendTimeout) 
            updateLegendTimeout = setTimeout(updateLegend, 50); 
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

function buildLegend(label, series) {
    var mode = statsGraphic.getOptions().mode;
    if(mode.type == 'circuit') {
        return '<a href="' + baseUrl + '/circuits?id=' + series.circuit.parent_id + '">' + label + '</a> to ' + 
            series.direction.split(' ')[2] + ' = <span class="traffic-value">0.0</span> Mbps';
    } else {
        if(mode.seriesIn == 0 && series.stack == 'in') {
            mode.seriesIn++;
            return series.direction + ' = 0.0 Mbps';
        } else if(mode.seriesOut == 0) {
            mode.seriesOut++;
            return series.direction + ' = 0.0 Mbps';
        }
    }
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
                label: circuit.name,
                circuit: circuit, 
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
                label: circuit.name, 
                circuit: circuit,
                direction: relativeDir == 'out' ? toDev + " to " + fromDev : fromDev + ' to ' + toDev, 
                color: color,
                data: dataIn});

            statsGraphic.setData(dataSeries);
            loadedCircuitsCounter.val++;
        }
    });
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
            console.log('loading port status');
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
                    loadPortStatus(loadedPortsStatusCounter, meicanMap.getNode('dev'+dev).options.name, response[dev][port].name);
                } 
            }  
            
            loadCircuits();       
        }
    });
}

function reloadPortsStatus() {
    console.log('loading ports status');
    loadedPortsStatusCounter.val = 0;
    for (var i = meicanMap.getNodes().length - 1; i >= 0; i--) {
        var node = meicanMap.getNodes()[i];
        for (var portId in node.options.ports) {
            loadPortStatus(
                loadedPortsStatusCounter,
                node.options.name, 
                node.options.ports[portId].name
            );
        }
    };
}

function loadPortStatus(loadedCounter, dev, port) {
    $.ajax({
        url: baseUrl+'/monitoring/status/get-by-port?dev=' + dev + '&port=' + port,
        dataType: 'json',
        method: "GET",
        success: function(response) {
            var node = meicanMap.getNodeByName(response.dev);
            for (var portId in node.options.ports) {
                if(response.port == node.options.ports[portId].name) {
                    node.options.ports[portId].status = response.status;
                    loadedCounter.val++;
                    break;
                }
            }
        }
    });
}

function refresh() {
    clearCircuits();
    reloadPortsStatus();
    loadCircuits();
}

function loadCircuits() {
    console.log('loading circuits');
    $.ajax({
        url: baseUrl+'/circuits/connection/get-all?status=ACTIVE&type=OSCARS',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            circuits = response;
            addCircuits(circuits);
            prepareCircuitsDevPath();
            setLinkStatusWhenReady();
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
                port.linkIn.options.directedCircuits = [];
                port.linkOut.options.directedCircuits = [];
            }
        }
    };
}

function addCircuits(circuits) {
    for (var i = circuits.length - 1; i >= 0; i--) {
        loadCircuitTraffic(circuits[i]);
        circuits[i].links = [];
        for (var j = circuits[i].fullPath.length - 1; j >= 0; j--) {
            var nodeName = circuits[i].fullPath[j].port_urn.split(':')[4].split('=')[1];
            var node = meicanMap.getNodeByName(nodeName);
            var portName = circuits[i].fullPath[j].port_urn.split(':')[5].split('=')[1];
            for (var portId in node.options.ports) {
                if(portName == node.options.ports[portId].name) {
                    node.options.ports[portId].circuits.push(circuits[i]);
                    if(node.options.ports[portId].linkOut != null) {
                        var linkOut = node.options.ports[portId].linkOut;
                        linkOut.bindPopup(
                        '<div class="pull-right"><br>' + buildGraphicModeSwitch() + '</div>' +
                        'Link between <b>' + 
                        meicanMap.getNode(linkOut.options.from).options.name +
                        '</b> and <b>' + 
                        meicanMap.getNode(linkOut.options.to).options.name +
                        '</b><br>' + 
                        'Capacity: <b>' + node.options.ports[portId].cap + ' Mbps</b>' + 
                        '<br>' + 
                        '<br><div class="traffic-stats" style="width: 610px; height: 250px"></div><br>' + 
                        '<div class="stats-legend"></div>',
                        {'maxWidth': '625'});
                    }
                }
            }
        }
    }
}

function buildGraphicModeSwitch() {
    return '<input class="graphic-mode-switch" data-label-text="Aggregate traffic" data-size="mini" type="checkbox" name="graphic-mode" checked>';
}

function loadCircuitTraffic(circuit) {
    var node = circuit.fullPath[0].port_urn.split(':')[4].split('=')[1];
    var port = circuit.fullPath[0].port_urn.split(':')[5].split('=')[1];
    var vlan = circuit.fullPath[0].vlan;
    circuit.fullPath[0]['device'] = node;
    circuit.fullPath[0]['port'] = port; 

    $.ajax({
        url: baseUrl+'/monitoring/traffic/get?dev=' + node +
            '&port=' + port + '&vlan=' + vlan + '&dir=in',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            circuit['trafficIn'] = Math.round(response.traffic*8/1000000*100) / 100;
        }
    });

    $.ajax({
        url: baseUrl+'/monitoring/traffic/get?dev=' + node +
            '&port=' + port + '&vlan=' + vlan + '&dir=out',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            circuit['trafficOut'] = Math.round(response.traffic*8/1000000*100) / 100;
        }
    });
}

function setLinkStatusWhenReady() {
    console.log('trying set link status');
    if (areCircuitsReady() && arePortsStatusReady()) {
        setLinkStatus();
    } else {
        setTimeout(function() {
            setLinkStatusWhenReady();
        }, 100);
    }
}

function areCircuitsReady() {
    for (var i = 0; i < circuits.length; i++) {
        if(circuits[i].trafficOut == null || circuits[i].trafficIn == null)
            return false;
    }
    
    return true;
}

function arePortsStatusReady() {
    console.log(loadedPortsStatusCounter.val, meicanMap.getPortsSize());
    return loadedPortsStatusCounter.val == meicanMap.getPortsSize();
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
                //circuits[i].links.push(linkIn);
            }
            if(linkOut) {
                linkOut.options.traffic += circuits[i].trafficOut;
                linkOut.options.directedCircuits.push({
                    circuit: circuits[i],
                    dir: 'out'
                });
                //circuits[i].links.push(linkOut);
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
        return "#FFC604";
    } else {
        return "#E8160C";
    } 
}

