/**
 * @copyright Copyright (c) 2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

var meicanMap;
var circuitApproved = false;
var circuitDataPlane;
var path;
var statsCurrentPts;
var statsGraphic;
var refreshInterval;

$(document).ready(function() {
    circuitApproved = isAuthorized();
    circuitDataPlane = getDataPlaneStatus();
    requestRefresh();
    initPathBox();
    initStats();
    initHistoryModal();
    initEditModal();
    initCancelModal();
    enableAutoRefresh();

    $("#refresh-btn").on('click', function() {
        requestRefresh();
        MAlert.show("Refresh in progress.", "Please, wait a moment while we get updated information.", 'success');
    });
});

$(document).on('ready pjax:success', function() {
    //se o circuito nao esta aprovado, verifica o ultimo status
    //se ele for aprovado, entao deve ser recarregado 
    if (!circuitApproved && isAuthorized()) {
        circuitApproved = true;
        console.log("connection approved");
        drawCircuit($("#circuit-id").attr('value'), true);
    }

    //se mudou dataplane entao atualiza cor do circuito
    if(circuitDataPlane != getDataPlaneStatus()) {
        console.log('dataplane change');
        circuitDataPlane = getDataPlaneStatus();
        updateCircuitColor();
    }
});

function updateCircuitColor() {
    var color = (circuitDataPlane == 'ACTIVE') ? "#35E834" : "#27567C";
    for (var i = meicanMap.getLinks().length - 1; i >= 0; i--) {
        meicanMap.getLinks()[i].setStyle({
            color: color
        });
    };
}

function getDataPlaneStatus() {
    return $("#status-dataplane").attr("status");
}

function isAuthorized() {
    return $("#status-auth").attr('status') == 'AUTHORIZED';
}

function requestRefresh() {
    $.ajax({
        url: baseUrl+'/circuits/connection/refresh',
        data: {
            id: $("#circuit-id").attr('value'),
        },
        success: function() {
            refreshPjax("details-pjax");
        }
    });
}

function disableAutoRefresh() {
    clearInterval(refreshInterval);
}

function enableAutoRefresh() {
    refreshInterval = setInterval(refreshAll, 30000);
}

function refreshAll() {
    console.log('refreshing...');
    refreshPjax('status-pjax');
    setTimeout(function() {
        refreshPjax('details-pjax');
    }, 2000);
    setTimeout(function() {
        refreshPjax('history-pjax');
    }, 4000);
}

function refreshPjax(id) {
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
        $.ajax({
            url: baseUrl+'/circuits/connection/cancel',
            dataType: 'json',
            data: {
                id: $("#circuit-id").attr("value"),
            },
            success: function() {
            },
            error: function() {
                MAlert.show(I18N.t("Error."), I18N.t("You are not allowed for cancel circuits in this domains."), 'danger');
            }
        });
        $("#cancel-modal").modal("hide");
        MAlert.show(I18N.t("Cancellation in progress."), I18N.t("Please, wait a moment while we process your request."), 'success');
    });
}

function initEditModal() {
    $("#edit-modal").on("click", '.confirm-btn', function() {
        validateEditForm();

        setTimeout(function() {
            if($("#edit-modal").find(".has-error").length > 0) {
                console.log("tem erro")
                MAlert.show(I18N.t("Request invalid."), I18N.t("Please, check your input and try again."), 'danger');
                return;
            }

            $.ajax({
                type: "POST",
                url: baseUrl + '/circuits/connection/update?submit=true',
                data: $("#edit-form").serialize(),
                success: function (response) {
                    if(response) {
                        MAlert.show(I18N.t("Modification in progress."), 
                        I18N.t("Please, wait a moment while we process your request."), 'success');
                        $.ajax({
                            type: "POST",
                            url: baseUrl + '/circuits/connection/update?id='+ $("#circuit-id").attr('value') + '&confirm=true',
                            success: function () {
                            },
                            error: function() {
                                MAlert.show(I18N.t("Error."), I18N.t("Sorry, contact your administrator."), 'danger');
                            }
                        });
                        $("#edit-modal").modal('hide');
                    }
                    else MAlert.show(
                        I18N.t("No changes."), 
                        I18N.t("Please, check your input and try again."), 
                        'warning');
                },
                error: function() {
                    MAlert.show(I18N.t("Error."), I18N.t("Sorry, contact your administrator."), 'danger');
                }
            });

        }, 200);

        return false;
    });

    $("#connectionform-acceptrelease").on("switchChange.bootstrapSwitch", function(event, state) {
        validateEditForm();
    });

    $("#edit-modal").on("click", '.close-btn', function() {
        $("#edit-modal").modal("hide");
        return false;
    });

    $("#edit-modal").on("click", '.undo-btn', function() {
        $("#connectionform-start").val($("#info-start").attr('value'));
        $("#connectionform-end").val($("#info-end").attr('value'));
        $("#connectionform-bandwidth").val($("#info-bandwidth").attr('value'));
        validateEditForm();
        return false;
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

function validateEditForm() {
    $("#edit-form").yiiActiveForm("validateAttribute", 'connectionform-bandwidth');
    $("#edit-form").yiiActiveForm("validateAttribute", 'connectionform-start');
    $("#edit-form").yiiActiveForm("validateAttribute", 'connectionform-end');
}

function initHistoryModal() {
    $("#history-pjax").on("click", '.event-message', function(e) {
        $('#event-message-modal').modal('show');
        $.ajax({
            url: baseUrl+'/circuits/connection/get-event-message',
            data: {
                id: $(this).parent().parent().attr('data-key')
            },
            method: "GET",
            success: function(response) {
                $("#event-message-modal").find('.modal-body').html(response);
            },
        });
        return false;
    });

    $('#event-message-modal').on('hidden.bs.modal', function () {
        $("#event-message-modal").find('.modal-body').html('');
    })
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
    $("#path-grid").css("margin", '10px');
    $("#path-box").css("height", 445);
    $("#path-map").css("height", 400);
    meicanMap = new LMap('path-map');
    meicanMap.show();
    loadDomains();

    /*$("#path-map").on("linkClick", function(e, link) {
        var srcPoint;
        for (var i = 0; i < path.length; i++) {
            if(('dev' + path[i].device_id) == link.options.from)
                loadStats(path[i]);
        };
    });*/

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if(target == '#path-map') {
            meicanMap.show(true);
            meicanMap.focusNodes();
        }
    });

    $('#path-map').on('lmap.nodeClick', function(e, node) {
        let portName = node.options.name.split(":").slice(3,6).join(':');
        let domainName = node.options.name.split(":")[0];
        node.setPopupContent('Domain: <b>' + domainName + '</b><br>Port: <b>' + portName + '</b><br>');
    });

    $("#path-box").on("click", '.show-stats', function() {
        ///monitoramento de interfaces????????
        //loadStats($(this).parent().parent().attr("data-key"));
    });
}

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            meicanMap.getTopology()['domains'] = response;
            loadNetworks();
        }
    });
}

function loadNetworks() {
    $.ajax({
        url: baseUrl+'/topology/network/get-all',
        dataType: 'json',
        method: "GET",
        success: function(nets) {
            for (var i = nets.length - 1; i >= 0; i--) {
                for (var k = meicanMap.getTopology()['domains'].length - 1; k >= 0; k--) {
                    if (nets[i]['domain_id'] == meicanMap.getTopology()['domains'][k]['id']) {
                        nets[i]['domain'] = meicanMap.getTopology()['domains'][k];
                    }
                }
            }
            meicanMap.getTopology()['networks'] = nets;
            drawCircuit($("#circuit-id").attr('value'));
        }
    });
}

function drawCircuit(connId, animate) {
    $.ajax({
        url: baseUrl+'/circuits/connection/get-path',
        dataType: 'json',
        method: "GET",
        data: {
            id: connId,
        },
        success: function(response) {
        	path = response;
        	var nets = meicanMap.getTopology()['networks'];
        	for (var i = path.length - 1; i >= 0; i--) {
                for (var k = nets.length - 1; k >= 0; k--) {
                    if (path[i]['network_id'] == nets[k]['id']) {
                        path[i]['network'] = nets[k];
                    }
                }
                if(path[i]['network']['latitude'] == null && path[i]['network']['longitude'] == null){
                    path[i]['network']['latitude'] = path[i]['provider_lat'];
                    path[i]['network']['longitude'] =  path[i]['provider_lng'];
                }
            }
            var size = response.length;

            if (circuitApproved) {
                //a ordem dos marcadores aqui eh importante,
                //pois eh a ordem do circuito
                //console.log(requiredMarkers);
                addSource(path[0]);
                addDestin(path[size-1]);
                
                for (var i = 1; i < size-1; i++) {
                    addWayPoint(path[i]);
                }

                updatePathInfo(path);
                
                drawCircuitWhenReady(path, false);
                
            } else {
                //aqui nao importa a ordem dos marcadores, pois nao ha circuito criado

                addSource(path[0]);
                addDestin(path[size-1]);
                
                for (var i = 1; i < size-1; i++) {
                    addWayPoint(path[i]);
                }
                
                setMapBoundsMarkersWhenReady(path);
            }
        }
    });
}

function updatePathInfo(path) {
    $($("#path-grid").find('tbody').find("tr").children()[0]).text(0);
    $($("#path-grid").find('tbody').find("tr").children()[1]).text(path[0].urn);
    $($("#path-grid").find('tbody').find("tr").children()[2]).text(path[0].vlan);

    for (var i = 1; i < path.length; i++) {
        var pointRow = $($("#path-grid").find('tbody').find("tr")[0]).clone();

        $(pointRow.children()[0]).text(i);
        $(pointRow.children()[1]).text(path[i].urn);
        $(pointRow.children()[2]).text(path[i].vlan);
        $(pointRow).attr('data-key', i);
        $($("#path-grid").find('tbody')).append(pointRow);
    }
}

function drawCircuitWhenReady(path, animate) {
    if (areMarkersReady(path)) {
        console.log("drew");
        if (animate) {
            drawCircuitAnimated();
        } else {
            for (var i = 0; i < path.length - 1; i++) {
                meicanMap.addLink(
                    path[i].urn, 
                    path[i+1].urn, 
                    false,
                    null,
                    (circuitDataPlane == 'ACTIVE') ? "#35E834" : "#27567C");
            }
            
            meicanMap.focusNodes();
            loadStats(path);
        }
    } else {
        console.log("try draw");
        setTimeout(function() {
            drawCircuitWhenReady(path, animate);
        } ,50);
    }
}

function setMapBoundsMarkersWhenReady(path) {
    if (areMarkersReady(path)) {
        console.log("setbounds");
        meicanMap.focusNodes();
    } else {
        console.log("try bounds");
        setTimeout(function() {
            setMapBoundsMarkersWhenReady(path);
        } ,50);
    }
}

function addWayPoint(port) {
    addMarker(port, "#00FF00");
}

function addSource(port) {
    addMarker(port, "#0000EE");
}

function addDestin(port) {
    addMarker(port, "#FF0000");
}

function addMarker(port, color) {
    marker = meicanMap.getNodeByPort(port.urn);
    if (marker) return;

    meicanMap.addNode(port, color, true);
}

function areMarkersReady(path) {
    for (var i = path.length - 1; i >= 0; i--) {
        if (meicanMap.getNodeByPort(path[i].urn) === null) {
            return false;
        }
    }
    
    return true;
}

function initStats() {
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
            return convertTrafficValue(val);
        }
      },
      xaxis: {
        mode: "time",
        timezone: 'browser',
        show: true,
      },
      legend: {
        noColumns: 2,
        container: $("#stats-legend"),
        labelFormatter: function(label, series) {
            return '<span style="margin-right: 10px; margin-left: 5px;">' + label + '</span>';
        }
      }
    });

    //Initialize tooltip on hover
    $('<div class="tooltip-inner" id="line-chart-tooltip"></div>').css({
      position: "absolute",
      display: "none",
      opacity: 0.8,
      zIndex: 3,
    }).appendTo("body");


    $("#stats").bind("plothover", function (event, pos, item) {

      if (item) {
        var x = item.datapoint[0],
            y = convertTrafficValue(item.datapoint[1], 2);

        $("#line-chart-tooltip").html(moment.unix(x/1000).format("DD/MM/YYYY HH:mm:ss") + '<br>' + y)
            .css({top: item.pageY + 5, left: item.pageX + 5})
            .fadeIn(200);
      } else {
        $("#line-chart-tooltip").hide();
      }

    });
}

function convertTrafficValue(val, fixed) {
    if (Math.abs(val) > 999999)
        return (Math.abs(val)/1000000).toFixed(fixed ? fixed : 0) + " Mbps";
    else if(Math.abs(val) > 999)
        return (Math.abs(val)/1000).toFixed(fixed ? fixed : 0) + " Kbps";
    else
        return (Math.abs(val)).toFixed(fixed ? fixed : 0) + " bps";
}

function loadStats() {
    //$("#stats-target").html("<b>" + statsCurrentPoint.port_urn + "</b> (VLAN <b>" + statsCurrentPoint.vlan + "</b>)");

    $("#stats-loading").show();

    //if(urnType == "NSI")....
    var statsData = [];

    loadTrafficHistory(statsData, path[0].urn, path[0].vlan);
}

function loadTrafficHistory(statsData, port, vlan) {
    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history?port=' + port +
            '&vlan=' + vlan + '&dir=out' + '&interval=' + 0,
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var dataOut = [];
            for (var i = 0; i < data.traffic.length; i++) {
                dataOut.push([moment.unix(data.traffic[i].ts), 0-(data.traffic[i].val*8)]);
            }
            statsData.push({label: 'To ' + port, data: dataOut, color: "#f56954" });

            statsGraphic.setData(statsData);
            statsGraphic.setupGrid();
            statsGraphic.draw();
        }
    });

    $.ajax({
        url: baseUrl+'/monitoring/traffic/get-vlan-history?port=' + port + 
            '&vlan=' + vlan + '&dir=in' + '&interval=' + 0,
        dataType: 'json',
        method: "GET",
        success: function(data) {
            var dataIn = [];
            for (var i = 0; i < data.traffic.length; i++) {
                dataIn.push([moment.unix(data.traffic[i].ts), data.traffic[i].val*8]);
            }
            statsData.push({label: 'From ' + port, data: dataIn, color: "#3c8dbc" });

            statsGraphic.setData(statsData);
            statsGraphic.setupGrid();
            statsGraphic.draw();
            
            $("#stats-loading").hide();
        }
    });
}
