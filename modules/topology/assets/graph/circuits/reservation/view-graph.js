$(document).ready(function() {
    prepareRefreshButton();
    prepareCancelDialog();
    
    selectConn($("#connections-grid tbody").children().attr("data-key"));
    loadEndPointDetails(selectedConn);
    selectedConnIsApproved = isAuthorizationReceived();

    $("#map-canvas").show();
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
            "barnesHut": {
              "gravitationalConstant": -24850,
              "centralGravity": 0.1
            },
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

    drawReservation(selectedConn);
    
    initEndPointButtons("src");
    initEndPointButtons('dst');
});

$("#viewer-type-select").selectmenu();
$("#node-type-select").selectmenu();

$(document).on('ready pjax:success', function() {
    /*selectConn(selectedConn);
    
    $('#connections-grid').on("click", '.cancel-button', function() {
        
        if ($(this).attr("disabled") != 'disabled') {
            disableAutoRefresh();
            $("#cancel-dialog").data('connId', $(this).parent().parent().parent().attr('data-key')).dialog("open");
        }
            
        return false;
    });
    
    $('#connections-grid tbody tr').on("click", function() {
        if(selectedConn != $(this).attr("data-key")) {
            selectConn($(this).attr("data-key"));
            selectedConnIsApproved = isAuthorizationReceived();
            showCircuit(selectedConn);
        }
    });
    
    if (!selectedConnIsApproved && isAuthorizationReceived()) {
        selectedConnIsApproved = true;
        console.log("connection approved");
        drawReservation(selectedConn, true);
    }

    for (var i = 0; i < circuits.length; i++) {
        if (circuits[i].connId == selectedConn) {
            circuits[i].setOptions(
                {
                    strokeColor: getSelectedConnCircuitColor()
                });
            break;
        } 
    }*/
});

var selectedConnIsApproved;
var selectedConn;

var refresher;
var circuits = [];

var nodes = new vis.DataSet();
var edges = new vis.DataSet();
var network;

function getSelectedConnCircuitColor() {
    if (isActiveSelectedConn()) {
        return "#1B8B1D"; 
    } else {
        return "#483D8B"; 
    }
} 

function showCircuit(connId) {
    var found = false;
    for (var i = 0; i < circuits.length; i++) {
        console.log(circuits[i].connId, connId)
        if (circuits[i].connId != connId) {
            circuits[i].setVisible(false);
        } else {
            found = true;
            circuits[i].setVisible(true);
            showMarkers(circuits[i].requiredMarkers);
        }
    }

    if (!found) {
        drawReservation(connId);
    }
}

function isAuthorizationReceived() {
    return $("#connections-grid tbody").
            children("tr[data-key=" + selectedConn + "]").find("td.authorized").length > 0;
}

function isActiveSelectedConn() {
    return $("#connections-grid tbody").
            children("tr[data-key=" + selectedConn + "]").find("td.active").length > 0;
}

function selectConn(id) {
    $("#connections-grid tbody").children("tr[data-key=" + selectedConn + "]").removeClass("checked-line");
    selectedConn = id;
    $("#connections-grid tbody").children("tr[data-key=" + selectedConn + "]").addClass("checked-line");
}

function loadEndPointDetails(connId) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-end-points',
        dataType: 'json',
        method: "GET",
        data: {
            id: connId,
        },
        success: function(response) {
            fillEndPointDetails("src", response["src"]);
            fillEndPointDetails("dst", response["dst"]);
        }
    });
}

function fillEndPointDetails(endPointType, path) {
    if (path.dom.length < 15) {
        $("#" + endPointType + "-dom").text(path.dom);
    } else {
        $("#" + endPointType + "-dom").text(path.dom.substr(0, 13) + "...");
        $("#" + endPointType + "-dom").prop("title", path.dom);
    }
    if (path.net.length < 15) {
        $("#" + endPointType + "-net").text(path.net);
    } else {
        $("#" + endPointType + "-net").text(path.net.substr(0, 13) + "...");
        $("#" + endPointType + "-net").prop("title", path.net);
    }
    if (path.dev.length < 15) {
        $("#" + endPointType + "-dev").text(path.dev);
    } else {
        $("#" + endPointType + "-dev").text(path.dev.substr(0, 13) + "...");
        $("#" + endPointType + "-dev").prop("title", path.dev);
    }
    if (path.port.length < 15) {
        $("#" + endPointType + "-port").text(path.port);
    } else {
        $("#" + endPointType + "-port").text(path.port.substr(0, 13) + "...");
        $("#" + endPointType + "-port").prop("title", path.port);
    }
    $("#" + endPointType + "-urn").text(path.urn);
    if (path.vlan.length < 15) {
        $("#" + endPointType + "-vlan").text(path.vlan);
    } else $("#" + endPointType + "-vlan").text(path.vlan.substr(0, 13) + "...");
}

function prepareRefreshButton() {
    refresher = setInterval(updateGridView, 10000);
    
    $("#refresh-button").click(function(){
        if ($("#refresh-button").val() == "true") {
            disableAutoRefresh();
        } else {
            enableAutoRefresh();
        }
    });
}

function prepareCancelDialog() {
    $("#cancel-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: "200",
        buttons: [{
            id:"yes-button",
            text: I18N.t("Yes"),
            click: function() {
                var connId = $("#cancel-dialog").data('connId');
                $("#cancel-dialog").dialog( "close" );
                
                $.ajax({
                    url: baseUrl+'/circuits/connection/cancel',
                    dataType: 'json',
                    data: {
                        id: connId,
                    },
                    success: function() {
                        enableAutoRefresh();
                    },
                    error: function() {
                        $("#dialog").dialog("open");
                        $("#message").html(I18N.t("You are not allowed for cancel connections in this domains."));
                        $("#dialog").dialog({
                            buttons: [
                                {
                                    text: "Ok",
                                    click: function() {
                                      $(this).dialog( "close" );
                                    }
                                },
                            ]
                        });
                    }
                });
            }
        },{
            text: I18N.t("No") + " (ESC)",
            click: function() {
                $("#cancel-dialog").dialog( "close" );
            }
        }],
        close: function() {
            $("#yes-button").attr("disabled", false);
        }
    });
}

function disableAutoRefresh() {
    $("#refresh-button").val('false');
    clearInterval(refresher);
    $("#refresh-button").text(I18N.t("Enable auto refresh"));
}

function enableAutoRefresh() {
    updateGridView();
    $("#refresh-button").val('true');
    refresher = setInterval(updateGridView, 10000);
    $("#refresh-button").text(I18N.t("Disable auto refresh"));
}

function updateGridView() {
    $.pjax.defaults.timeout = false;
    $.pjax.reload({
        container:'#connections-pjax'
    });
}

/////////////// botoes superiores da tabela origem destino /////////

function initEndPointButtons(endPointType) {
    $('#' + endPointType + '-copy-urn').click(function() {
        openCopyUrnDialog(endPointType);
    });
    
    $("#copy-urn-dialog").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: "auto",
        height: "auto",
        buttons: [{
            text: I18N.t("Close"),
            click: function() {
                $("#copy-urn-dialog").dialog( "close" );
            }
        }],
    });
}

function openCopyUrnDialog(endPointType) {
    $("#copy-urn-field").val($("#"+ endPointType+ "-urn").text());
    $("#copy-urn-dialog").dialog("open");
}

///////////// DESENHAR CIRCUITO NO MAPA ///////////////

function drawCircuit(requiredMarkers) {
    var size = requiredMarkers.length;

    for (var i = 0; i < size; i++) {
        addLink(requiredMarkers[i], requiredMarkers[i+1]);
    }
}

//////////// INICIALIZA MAPA /////////////////


function addNode(id, name, color) {
    nodes.add({
        id: id, 
        label: name,
        physics: true,
        color: {
           background: color,
           border: "#808080" 
        }
    });
}

function addLink(srcId, dstId) {
    edges.add({
        from: srcId, 
        to: dstId,
        arrows: {
            to: {
                enabled: true
            },
        },
    });
    edges.add({
        from: dstId, 
        to: srcId,
        arrows: {
            to: {
                enabled: true
            },
        },
    });
}

function drawReservation(connId, animate) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-ordered-paths',
        dataType: 'json',
        method: "GET",
        data: {
            id: connId,
        },
        success: function(response) {
            if (selectedConnIsApproved) {
                var size = response.length;
                var requiredMarkers = [];

                for (var i = 0; i < size; i++) {
                    if (response[i].port_id != null) {
                        requiredMarkers.push(response[i].port_id);
                    }
                }

                //showMarkers(requiredMarkers);

                //console.log(requiredMarkers);

                addSourceMarker(response[0]);
                addDestinMarker(response[size-1]);
                
                for (var i = 1; i < size-1; i++) {
                    if (response[i].port_id != null) {
                        addWayPointMarker(response[i]);
                    }
                }

                network.fit();
                network.stabilize();
                drawCircuit(requiredMarkers);
                
                //setMapBoundsMarkersWhenReady(requiredMarkers);
                
                //drawCircuitWhenReady(requiredMarkers, animate);
                
            } else {
                var size = response.length;
            
                var requiredMarkers = [];

                //aqui nao importa a ordem dos marcadores, pois nao ha circuito criado
                addSourceMarker(response[0].device_id);
                requiredMarkers.push(response[0].device_id);
                addDestinMarker(response[size-1].device_id);
                requiredMarkers.push(response[size-1].device_id);
                
                for (var i = 1; i < size-1; i++) {
                    if (response[i].device_id != null) {
                        addWayPointMarker(response[i].device_id);
                        requiredMarkers.push(response[i].device_id);
                    }
                }
                
                setMapBoundsMarkersWhenReady(requiredMarkers);
            }
        }
    });
}

function drawCircuitWhenReady(requiredMarkers, animate) {
    if (areMarkersReady(requiredMarkers)) {
        drawCircuit(requiredMarkers);
    } else {
        setTimeout(function() {
            drawCircuitWhenReady(requiredMarkers, animate);
        } ,50);
    }
}

function setMapBoundsMarkersWhenReady(requiredMarkers) {
    if (areMarkersReady(requiredMarkers)) {
        var path = [];
        var size = requiredMarkers.length;
        for(var i = 0; i < size; i++){
            path.push(meicanMap.getMarker('dev',requiredMarkers[i]).position);
        }
        setMapBounds(path);
    } else {
        setTimeout(function() {
            setMapBoundsMarkersWhenReady(requiredMarkers);
        } ,50);
    }
}

function addWayPointMarker(node) {
    addMarker(node, "#00FF00");
}

function addSourceMarker(node) {
    //if (marker) return meicanMap.changeDeviceMarkerColor(marker, "0000EE");

    addMarker(node, "#0000EE");
}

function addDestinMarker(node) {
    //if (marker) return meicanMap.changeDeviceMarkerColor(marker, "FF0000");

    addMarker(node, "#FF0000");
}

function showMarkers(connIds) {
    var size = meicanMap.getMarkers().length;
    for (var i = 0; i < size; i++) {
        var found = false;
        for (var k = 0; k < connIds.length; k++) {
            console.log(meicanMap.getMarkers()[i].id, connIds[k]);
            if (meicanMap.getMarkers()[i].id == connIds[k]) {
                meicanMap.getMarkers()[i].setVisible(true);
                found = true;
                break;
            } 
        }

        if (!found) 
            meicanMap.getMarkers()[i].setVisible(false);
    }
}

//////////// ADICIONA MARCADORES NO MAPA /////////////////

function addMarker(node, color) {
   // node = nodes.get(node.id)
   // if (node) return node;

    addNode(node.port_id, node.port_urn, color);
}

function areMarkersReady(ids) {
    /*for (var i = 0; i < ids.length; i++) {
        var marker = meicanMap.getMarker('dev',ids[i]);
        if (marker === null) {
            return false;
        }
    }*/
    
    return true;
}

