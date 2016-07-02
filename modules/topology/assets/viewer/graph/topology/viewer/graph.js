var nodes = new vis.DataSet();
var edges = new vis.DataSet();
var network;

$("#viewer-type-select").selectmenu();

$("#node-type-select").selectmenu({
    select: function( event, ui ) {
        //setNodeType(ui.item.value);
    }
});

function setNodeType(markerType) {
    setLinkTypeVisible(markerType);
    if (markerType == "dev") {
        if (!devicesLoaded) {
            loadDeviceMarkers();
            devicesLoaded = true;
        } 
    }
}

function buildGraph() {
    var container = document.getElementById('map-canvas');
    var data = {
        nodes: nodes,
        edges: edges
    };
    var options = {
        height: "98%",
        edges: {
            color: "#2B7CE9",
            width: 1,
          },
        physics: {
            enabled: true,
        },
        nodes: {
            shape: 'dot',
            size: 30,
            font: {
                size: 32
            },
            borderWidth: 2,
        },
        interaction:{
            hover: true,
        }
    };
    network = new vis.Network(container, data, options);
    network.on("click", function (params) {
        if(params.nodes.length > 0) {
            var nodePos = network.getPositions(params.nodes[0]);
            var d = document.getElementById('tooltip');
            var pos = network.canvasToDOM({x:nodePos[params.nodes[0]].x,y:nodePos[params.nodes[0]].y});
            d.style.left = pos.x - 3 +'px';
            d.style.top = pos.y - 85 +'px';
            $("#tooltip").html('<b>Name</b>: ' + nodes.get(params.nodes[0]).label + '<br><a href="#">Show details</a>');
            $("#tooltip").show();
        } else if (params.edges.length > 0) {
            /*var d = document.getElementById('tooltip');
            var edge = edges.get(params.edges[0]);
            d.style.left = params.pointer.DOM.x + 5 + 'px';
            d.style.top = params.pointer.DOM.y - 50 +'px';
            $("#tooltip").text((edge.count > 1) ? edge.count + " links" : edge.count + " link");
            $("#tooltip").show();*/
        } else $("#tooltip").hide();
    });
    network.on("dragging", function (params) {
        $("#tooltip").hide();
    });
    network.on("resize", function (params) {
        $("#tooltip").hide();
    });
    network.on("zoom", function (params) {
        $("#tooltip").hide();
    });
}

$("#save-button").on("click", function(){
    network.storePositions();
    $.ajax({
        type: "POST",
        url: baseUrl + '/topology/viewer/save-graph',
        data: {
            mode: $("#node-type-select").val(),
            nodes: nodes.get()
        },
        success: function (response) {
        },
        error: function() {
        }
    });
});

$(document).ready(function() {
    $("#map-canvas").show();
    buildGraph();
    loadDevices();
});

function loadDevices() {
    $.ajax({
        url: baseUrl+'/topology/device/get-all-color',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','graph_x','graph_y'])
        },
        success: function(response) {
            addNodes(response);
            loadDeviceLinks();
        }
    });
}

function loadDeviceLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-device-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            addLinks(response);
            network.fit();
            network.stabilize();
        }
    });
}

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','graph_x','graph_y','color'])
        },
        success: function(response) {
            addNodes(response);
            loadDomainLinks();
        }
    });
}

function loadDomainLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-domain-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            addLinks(response);
        }
    });
}

function addNodes(objects) {
    var size = objects.length;
    for (var i = 0; i < size; i++) {
        nodes.add({
            id: objects[i].id, 
            label: objects[i].name, 
            physics: false, 
            x: objects[i].graph_x, 
            y: objects[i].graph_y,
            color: {
               background: objects[i].color,
               border: "#808080" 
            }
        });
    };
}

function addLinks(objects) {
    var size = objects.length;
    for (var src in objects) {
        for (var i = 0; i < objects[src].length; i++) {
            edges.add({
                from: src, 
                to: objects[src][i],
                arrows: {
                    to: {
                        enabled: true
                    },
                },
            });
        }
    }
}

