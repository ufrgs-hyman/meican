/**
 * @copyright Copyright (c) 2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

var meicanMap = new LMap('canvas');
var meicanGraph = new VGraph("canvas");
var lsidebar;

$(document).ready(function() {
    meicanMap.show();
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
    
    
    meicanGraph.loadTopology(true);
    meicanMap.loadTopology(true);

});

function initMenu() {
    $('input[name="mode"]').on('ifChecked', function(){
        if(this.value == 'map') {
            //viewer = meicanMap;
            meicanGraph.hide();
            meicanMap.show($('input[name="node-type"]:checked').val());
        } else {
            //viewer = meicanGraph;
            meicanMap.hide();
            meicanGraph.show($('input[name="node-type"]:checked').val());
        }
    });

    $('input[name="node-type"]').on('ifChecked', function(){
       console.log('change node-type');// viewer.setNodeType(this.value);
    });
}

function initCanvas() {
    $('#canvas').on('lmap.nodeClick', function(e, node) {
        let expandGroupButton;
        let networkId = node.options.ports[0].network_id;

        if (node.options.type == "domain")
            if(meicanMap.hasLocation(networkId))
                expandGroupButton = ' <button style="visibility:visible" class="btn btn-xs btn-default expand-locations" title="Expand"><i class="fa fa-expand"></i></button>';
            else
                expandGroupButton = '';
        else if(node.options.type == "location")
            expandGroupButton = ' <button style="visibility:visible" class="btn btn-xs btn-info group-locations" title="Group"><i class="fa fa-compress"></i></button>';

        node.setPopupContent(
            '<div data-node="' + node.options.id + '">' +
                'Domain: <b>' + node.options.ports[0].network.domain.name + '</b>' + 
                expandGroupButton +
            '</div>');
    });

    $("#canvas").on("click",'.expand-locations', function() {
        meicanMap.expandLocations($(this).parent().attr('data-node'));
        meicanMap.removeLinks();
        meicanMap._loadLinks();
    });

    $("#canvas").on("click",'.group-locations', function() {
        meicanMap.groupLocations($(this).parent().attr('data-node'));
        meicanMap.removeLinks();
        meicanMap._loadLinks();
    });

    $("#canvas").on("click",'.show-link-details', function() {
        let element = $("#detailedLinkInformation");
        let wrapper = document.querySelector('.show-link-details');

        if(element.is(":hidden")){
            element.show();
            wrapper.innerHTML = '<i class="fa fa-minus"></i>'
        }else{
            element.hide();
            wrapper.innerHTML = '<i class="fa fa-info"></i>'
        }
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
        case 'prov':
            return '';
        case 'net':
            return '';
        case 'dev':
            return 'Domain: <b>' + domainName + '</b><br>Device: <b>' + name + '</b>';
        case 'dom':
        default:
            return 'Domain: <b>' + name + '</b>';

    }
}

function closePopups() {
    if(mode != 'map') {
        meicanGraph.closePopups();
    } else {
        meicanMap.closePopups();
    }
}

$("#save-positions-btn").on("click", function(){
    meicanGraph._graph.storePositions();
    $.ajax({
        type: "POST",
        url: baseUrl + '/topology/viewer/save-graph-positions',
        data: {
            _csrf: yii.getCsrfToken(),
            mode: 'dev',
            nodes: meicanGraph._nodes.get({
                filter: function (item) {
                    return item.type == 'dev';
                }
            })
        },
        success: function (response) {
        },
        error: function() {
        }
    });
});

function loadDomains() {
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            meicanTopo['dom'] = response;
            meicanMap.setDomains(response);
            meicanGraph.setDomains(response);
            //meicanGraph.addNodes(response, "dom", true);
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
            loadDeviceLinks();
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

function loadPortLinks() {
    $.ajax({
        url: baseUrl+'/topology/viewer/get-port-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            //meicanGraph.addLinks(response, 'dev');
            for (var src in response) {
                for (var i = 0; i < response[src].length; i++) {
                    //console.log(src, response[src][i]);
                    meicanMap.addLink(src,response[src][i]);
                }
            }           
        }
    });
}

