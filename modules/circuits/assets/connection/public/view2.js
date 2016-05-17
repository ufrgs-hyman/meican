/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

var meicanMap;
var connIsApproved = true;
var path;
var statsData;
var statsGraphic;

$(document).ready(function() {
    initPathBox();

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

function initPathBox() {
    meicanMap = new LMap('canvas');
    meicanMap.show('dev');
    $("#canvas").css("height", 400);
    drawCircuit($("#circuit-id").attr('value'));

    $('#canvas').on('lmap.nodeClick', function(e, marker) {
        marker.setPopupContent('Domain: <b>' + meicanMap.getDomain(marker.options.domainId).name + 
            '</b><br>Device: <b>' + marker.options.name + '</b><br><br>');
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
}

function drawCircuit(connId, animate) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-ordered-path',
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

function loadStats2() {
    $("#stats-loading").show();
    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history?port=' + 372 + '&vlan=' + 206 + '&dir=' + 'out' + '&interval=' + 0,
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var dataOut = [];
            for (var i = 0; i < data.traffic.length; i++) {
                dataOut.push({ts: new Date(data.traffic[i].ts*1000), val:data.traffic[i].val*8/1000000});
            }
            statsData = [dataOut];

            if(data.traffic.length > 0) {
                $.ajax({
                    url: baseUrl+'/monitoring/traffic/get-vlan-history?port=' + 372 + '&vlan=' + 206 + '&dir=' + 'in' + '&interval=' + 0,
                    dataType: 'json',
                    method: "GET",
                    success: function(data) {
                        var dataIn = [];
                        for (var i = 0; i < data.traffic.length; i++) {
                            dataIn.push({ts: new Date(data.traffic[i].ts*1000), val: (0-(data.traffic[i].val*8/1000000))});
                        }
                        statsData.push(dataIn);
                        console.log(statsData);

                        MG.data_graphic({
                            data: statsData,
                            full_width: true,
                            height: 375,
                            right: 10,
                            target: document.getElementById('stats'),
                            x_accessor: 'ts',
                            y_accessor: 'val',
                            aggregate_rollover: true,
                        });

                        $("#stats-loading").hide();
                    }
                });
            } else $("#stats-loading").hide();
        }
    });
}

function initStats() {
    //return;
    $("#stats").css("height", 375);

    $("#stats-box").on('click', '.refresh-btn', function() {
        loadStats();
    });

    statsGraphic = $.plot("#stats", [], {
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
            val = val.toFixed(4)
            return (val < 0 ? -1*val : val) + " Mbps";
        }
      },
      xaxis: {
        mode: "time",
        timezone: 'browser',
        show: true,
      }
    });
    //Initialize tooltip on hover
    $('<div class="tooltip-inner" id="line-chart-tooltip"></div>').css({
      position: "absolute",
      display: "none",
      opacity: 0.8
    }).appendTo("body");
    $("#stats").bind("plothover", function (event, pos, item) {

      if (item) {
        var x = item.datapoint[0],
            y = item.datapoint[1].toFixed(4);

        $("#line-chart-tooltip").html(moment.unix(x/1000).format("DD/MM/YYYY HH:mm:ss") + '<br>' + (y < 0 ? (-1*y) : y) + ' Mbps')
            .css({top: item.pageY + 5, left: item.pageX + 5})
            .fadeIn(200);
      } else {
        $("#line-chart-tooltip").hide();
      }

    });
}

function loadStats() {
    $("#stats-loading").show();
    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history?port=' + 372 + '&vlan=' + 206 + '&dir=' + 'out' + '&interval=' + 0,
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var dataOut = [];
            for (var i = 0; i < data.traffic.length; i++) {
                dataOut.push([moment.unix(data.traffic[i].ts), data.traffic[i].val*8/1000000]);
            }
            statsData = [{data: dataOut, color: "#3c8dbc" }];

            if(data.traffic.length > 0) {
                $.ajax({
                    url: baseUrl+'/monitoring/traffic/get-vlan-history?port=' + 372 + '&vlan=' + 206 + '&dir=' + 'in' + '&interval=' + 0,
                    dataType: 'json',
                    method: "GET",
                    success: function(data) {
                        var dataIn = [];
                        for (var i = 0; i < data.traffic.length; i++) {
                            dataIn.push([moment.unix(data.traffic[i].ts), 0-(data.traffic[i].val*8/1000000)]);
                        }
                        statsData.push({data: dataIn, color: "#f56954" });

                        statsGraphic.setData(statsData);
                        statsGraphic.setupGrid();
                        statsGraphic.draw();

                        $("#stats-loading").hide();
                    }
                });
            } else $("#stats-loading").hide();
        }
    });
}

