/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap = new LMap('canvas');
var connIsApproved = true;
var path;
var dataset = new vis.DataSet();
var statsGraphic;

$(document).ready(function() {
    meicanMap.show("rnp", 'dev');
    $("#canvas").css("height", 400);

    drawCircuit($("#circuit-id").attr('value'));

    /*refresher = setInterval(function() {
        updateCircuitStatus();
    }, 1000);*/

    initStats();
    initHistoryModal();
    initEditModal();
    initCancelModal();
});

$(document).on('ready pjax:success', function() {
    initHistoryModal();
});

function refreshPjaxContainer(id) {
    $.pjax.defaults.timeout = false;
    $.pjax.reload({
        container:'#' + id
    });
}

function initCancelModal() {
    $("#cancel-btn").on("click", function() {
        $('#cancel-modal').modal("show");
        return false;
    });

    $("#cancel-modal").on("click", '.close-btn', function() {
        $("#cancel-modal").modal("hide");
    });

    $("#cancel-modal").on("click", '.confirm-btn', function() {
        $("#cancel-modal").modal("hide");
    });
}

function initEditModal() {
    $("#edit-modal").on("click", '.confirm-btn', function() {
        $.ajax({
            type: "POST",
            url: baseUrl + '/circuits/connection/update?submit=true',
            data: $("#edit-form").serialize(),
            success: function (resId) {
                $("#edit-modal").modal('hide');
                setTimeout(function() {
                    refreshPjaxContainer('history-pjax');
                    setTimeout(function() {
                        refreshPjaxContainer('info-pjax');
                    }, 1000);
                }, 1000);
                $.ajax({
                    type: "POST",
                    url: baseUrl + '/circuits/connection/update?id='+ $("#circuit-id").attr('value') + '&confirm=true',
                    success: function () {
                    },
                    error: function() {
                        //showError(tt("Error proccessing your request. Contact your administrator."));
                    }
                });
            },
            error: function() {
                //showError(tt("Error proccessing your request. Contact your administrator."));
            }
        });
    });

    $("#connectionform-acceptrelease").on("switchChange.bootstrapSwitch", function(event, state) {
        if(state) {
            $("#edit-form").find(".field-connectionform-bandwidth").show();
            $("#edit-form").find(".field-connectionform-start").show();
        } else {
            $("#edit-form").find(".field-connectionform-bandwidth").hide();
            $("#edit-form").find(".field-connectionform-start").hide();
        }
    });

    $("#edit-modal").on("click", '.close-btn', function() {
        $("#edit-modal").modal("hide");
    });

    $("#edit-btn").on("click", function() {
        $('#edit-form').yiiActiveForm('resetForm');
        $("#connectionform-start").val($("#info-start").attr('value'));
        $("#connectionform-end").val($("#info-end").attr('value'));
        $("#connectionform-bandwidth").val($("#info-bandwidth").attr('value'));
        $('#edit-modal').modal("show");
        return false;
    });
}

function initHistoryModal() {
    $("#history-grid").on("click", '.event-message', function() {
        $('#history-modal').modal('show');
        return false;
    });
}

function updateCircuitStatus() {
    switch($("#info-status").attr("value")) {
        case 'reservating'   : 
            break;
        case 'scheduled'     : 
            if(moment().isAfter($("#info-start").attr("value"))) {
                activatingCircuit();
            } else {
                $("#status-box").find(".tts").text(moment().to($("#circuit-info").find('.start-time').attr("value")));
            }
            break;
        case 'activating'    : activeCircuit();
            break;
        case 'active'        : finishCircuit();
            break;
        case 'finished'      : 
            break;
    }
}

function scheduleCircuit() {
    $("#status-box").find(".info-box-text").text("Time to start");
    $("#status-box").find(".info-box-number").html('<span class="tts">loading...</span><br><small>10/02/2016 at 20:00</small>');
    $("#status-box").attr("data-value", 'scheduled');
}

function activatingCircuit() {
    $("#status-box").find(".ion-clock").removeClass().addClass("ion ion-gear-a");
    $("#status-box").find(".info-box-text").text("Status");
    $("#status-box").find(".info-box-number").text("Activating");
    $("#status-box").attr("data-value", 'activating');
}

function activeCircuit() {
    $("#status-box").find(".ion-clock").removeClass().addClass("ion ion-arrow-up-a");
    $("#status-box").find(".info-box-text").text("Status");
    $("#status-box").find(".info-box-number").text("Active");
    $("#status-box").attr("data-value", 'active');
}

function inactiveCircuit() {
    $("#status-box").find(".ion-clock").removeClass().addClass("ion ion-close-circled");
    $("#status-box").find(".info-box-text").text("Status");
    $("#status-box").find(".info-box-number").text("Inactive");
}

function finishCircuit() {
    $("#status-box").find(".ion-clock").removeClass().addClass("ion ion-checkmark-circled");
    $("#status-box").find(".info-box-text").text("Status");
    $("#status-box").find(".info-box-number").text("Finished");
}

function drawCircuit(connId, animate) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-ordered-paths',
        dataType: 'json',
        method: "GET",
        data: {
            id: connId,
        },
        success: function(response) {
            if (connIsApproved) {
                var size = response.length;
                path = response;

                //a ordem dos marcadores aqui eh importante,
                //pois eh a ordem do circuito
                //console.log(requiredMarkers);

                addSource(path[0]);
                addDestin(path[size-1]);
                
                for (var i = 1; i < size-1; i++) {
                    addWayPoint(path[i]);
                }
                
                //setMapBoundsMarkersWhenReady(requiredMarkers);
                
                drawCircuitWhenReady(path, animate);
                
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
                
                //setMapBoundsMarkersWhenReady(requiredMarkers);
            }
        }
    });
}

function drawCircuitWhenReady(requiredMarkers, animate) {
    if (areMarkersReady(requiredMarkers)) {
        //console.log("drew");
        if (animate) {
            drawCircuitAnimated(requiredMarkers);
        } else {
            var path = [];
            for (var i = 0; i < meicanMap.getNodes().length; i++) {
                path.push(meicanMap.getNodes()[i].options.id);
            }
            meicanMap.addLink(path);
            meicanMap.focusLink(meicanMap.getLinks()[0]);
            loadStats();
        }
    } else {
        setTimeout(function() {
            drawCircuitWhenReady(requiredMarkers, animate);
        } ,50);
    }
}

function setMapBoundsMarkersWhenReady(requiredMarkers) {
    if (areMarkersReady(requiredMarkers)) {
        //console.log("setbounds");
        var path = [];
        var size = requiredMarkers.length;
        for(var i = 0; i < size; i++){
            path.push(meicanMap.getNode('dev',requiredMarkers[i]).position);
        }
        setMapBounds(path);
    } else {
        setTimeout(function() {
            setMapBoundsMarkersWhenReady(requiredMarkers);
        } ,50);
    }
}

function addWayPoint(pathItem) {
    //marker = meicanMap.getMarker('dev'+ devId);
    //if (marker) return;

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: pathItem.device_id,
        },
        success: function(response) {
            addMarker(response, "#00FF00");
        }
    });
}

function addSource(pathItem) {
    //marker = meicanMap.getMarker('dev'+ devId);
    //if (marker) return meicanMap.changeDeviceMarkerColor(marker, "0000EE");

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: pathItem.device_id,
        },
        success: function(response) {
            addMarker(response, "#0000EE");
        }
    });
}

function addDestin(pathItem) {
    //marker = meicanMap.getMarker('dev'+ devId);
    //if (marker) return meicanMap.changeDeviceMarkerColor(marker, "FF0000");

    $.ajax({
        url: baseUrl+'/circuits/connection/get-stp',
        dataType: 'json',
        method: "GET",
        data: {
            id: pathItem.device_id,
        },
        success: function(response) {
            addMarker(response, "#FF0000");
        }
    });
}

function addMarker(dev, color) {
    marker = meicanMap.getNode('dev'+dev.id);
    if (marker) return marker;

    meicanMap.addNode(
        'dev'+dev.id,
        dev.name,
        'dev',
        dev.dom,
        dev.lat,
        dev.lng,
        color);
    //markerCluster.addMarker(marker);
    
    //addMarkerListeners(marker);
    
    //marker.setMap(meicanMap.getMap());
}

function areMarkersReady(ids) {
    for (var i = 0; i < ids.length; i++) {
        var marker = meicanMap.getNode('dev'+ids[i].device_id);
        if (marker === null) {
            return false;
        }
    }
    
    return true;
}

function initStats2() {
  // create a graph2d with an (currently empty) dataset
  var container = document.getElementById('stats');

  var options = {
    start: vis.moment().add(-24, 'hours'), // changed so its faster
    end: vis.moment(),
    height: '380px',
    drawPoints: {
      style: 'circle' // square, circle
    },
    shaded: {
      orientation: 'bottom' // top, bottom
    }
  };
  statsGraphic = new vis.Graph2d(container, dataset, options);

}

function initStats() {
    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history',
        dataType: 'json',
        method: "GET",
        success: function(data) {
            MG.data_graphic({
                data: data.traffic,
                full_width: true,
                height: 375,
                right: 40,
                target: document.getElementById('stats'),
                x_accessor: 'ts',
                y_accessor: 'val'
            });
            console.log(data);
        }
    });
    
}

/*
dataset.add(
{
            x: 1463005350,
            y: 0
        },
{
            x: 1463005380,
            y: 40.166666666666664
        },
        {
            x: 1463005410,
            y: 53112.13333333333
        },
        {
            x: 1463005440,
            y: 56492.3
        },
        {
            x: 1463005470,
            y: 14092.133333333333
        },
        {
            x: 1463005500,
            y: 770.7333333333333
        },
        {
            x: 1463005530,
            y: 0
        });

*/

function loadStats() {
    return;
    console.log('hola');
    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history',
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var stats = [];
            console.log(data);
            MG.data_graphic({
                title: "Custom Line Coloring",
                description: "By passing in an arisk.",
                data: [{
                    x: 1463005350,
                    y: 0
                },
                {       
                    x: 1463005380,
                    y: 40.166666666666664
                },
                {
                    x: 1463005410,
                    y: 53112.13333333333
                },
                {
                    x: 1463005440,
                    y: 56492.3
                },
                {
                    x: 1463005470,
                    y: 14092.133333333333
                },
                {
                    x: 1463005500,
                    y: 770.7333333333333
                },
                {
                    x: 1463005530,
                    y: 0
                }],
                width: 600,
                height: 200,
                right: 40,
                target: '#stats',
                aggregate_rollover: true
            });
        }
    });
}


function loadStats2() {
    console.log('hola');
    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history',
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var stats = [];
            for (var index in data.traffic) {
                stats.push({
                    x: moment.unix(data.traffic[index].ts),
                    y: data.traffic[index].val,
                    group: 1
                });
            }
            console.log(stats);
        }
    });
}

  function addDataPoint(ts, val, dir) {
    // add a new data point to the dataset
    dataset.add({
      x: moment.unix(ts),
      y: val,
      group: dir
    });

  }
  