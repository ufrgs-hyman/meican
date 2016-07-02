/**
 * Meican VGraph 1.0
 *
 * A DCN topology visualization based on Vis.js library.
 *
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

function VGraph(canvasDivId) {
    this._canvasDivId = canvasDivId; 
    this._graph;                        // vis.js network
    this._nodes = new vis.DataSet();    // nodes
    this._links = new vis.DataSet();    // edges
    this._nodeType;                     // current node type visible
    this._domainsList;                  // domains list reference;
    this._popup;
};

VGraph.prototype.show = function(nodeType) {
    if($("#graph-v").length == 1) {
        $("#graph-v").show();
    } else {
        $("#"+this._canvasDivId).append('<div id="graph-v" style="width:100%; height:100%;"></div>');
        this.build("graph-v");
    }

    this.setNodeType(nodeType);
    this.fit();
}

VGraph.prototype.hide = function() {
    if($("#graph-v").length == 1) {
        $("#graph-v").hide();
        /*this._graph.destroy();
        $("#graph-v").remove();
        this._nodes = new vis.DataSet(); // nodes
        this._links = new vis.DataSet(); // edges*/
    }
}

VGraph.prototype.setDomains = function(list) {
    this._domainsList = list;
}

VGraph.prototype.getDomain = function(id) {
    for (var i = 0; i < this._domainsList.length; i++) {
        if (this._domainsList[i].id == id) return this._domainsList[i];
    }
}

VGraph.prototype.addNodes = function(objects, type, loadPosition) {
    var size = objects.length;
    var nodes = [];
    var physics = true;
    for (var i = 0; i < size; i++) {
        physics = true;
        if(objects[i].graph_x && objects[i].graph_y) physics = false;
        if (!objects[i].color) {
            if(objects[i].domain_id) {
                objects[i].color = this.getDomain(objects[i].domain_id).color;
            } else if(objects[i].device_id) {
                objects[i].color = this.getDomain(this._nodes.get('dev'+objects[i].device_id).domainId).color;
            }
        }
        nodes.push({
            id: type + objects[i].id,
            label: objects[i].name,
            type: type,
            physics: physics,
            domainId: objects[i].domain_id,
            x: loadPosition ? objects[i].graph_x : null,
            y: loadPosition ? objects[i].graph_y : null,
            color : {
                background: objects[i].color,
                border: "#808080" 
            }
        });
    };
    this._nodes.add(nodes);
}

VGraph.prototype.addNode = function(id, name, type, domainId, x,y, color) {
    var physics = true;
    if(x && y) physics = false;
    this._nodes.add({
        id: id, 
        label: name, 
        physics: physics, 
        type: type,
        x: x, 
        y: y,
        color: {
            background: color,
            border: "#808080" 
        }
    });
}

VGraph.prototype.getNode = function(id) {
    return this._nodes.get(id);
}

VGraph.prototype.addLinks = function(objects, type) {
    var size = objects.length;
    for (var src in objects) {
        for (var i = 0; i < objects[src].length; i++) {
            this.addLink(type+src,type+objects[src][i], type);
        }
    }
}

VGraph.prototype.addLink = function(srcId, dstId, type) {
    this._links.add({
        type: type,
        from: srcId, 
        to: dstId,
        arrows: {
            to: {
                enabled: true
            },
        },
    });
}

VGraph.prototype.focusNode = function(nodeId) {
    this._graph.selectNodes([nodeId]);
    this._graph.focus(nodeId);
}

VGraph.prototype.setNodeType = function(type) {
    if(this._nodeType == type)
        return;

    this._nodeType = type;

    var nodes = this._nodes.get();
    for (var i = 0; i < nodes.length; i++) {
        if(nodes[i].type == type) {
            this._nodes.update({id: nodes[i].id, hidden: false});
        } else {
            this._nodes.update({id: nodes[i].id, hidden: true});
        }
    };

    var links = this._links.get();
    for (var i = 0; i < links.length; i++) {
        if(links[i].type == type) {
            this._links.update({id: links[i].id, hidden: false});
        } else {
            this._links.update({id: links[i].id, hidden: true});
        }
    };
}

VGraph.prototype.fit = function() {
    this._graph.fit();
}

VGraph.prototype.build = function(divId) {
    $("#"+divId).show();

    var container = document.getElementById(divId);
    var data = {
        nodes: this._nodes,
        edges: this._links
    };
    var options = {
        layout: {
            improvedLayout: false,
        },
        edges: {
            color: "#2B7CE9",
            width: 1,
          },
        physics: {
            enabled: true,
            solver: 'repulsion',
            repulsion: {
                nodeDistance: 200,
                springLength: 300
            }
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
            navigationButtons: true,
        }
    };
    this._graph = new vis.Network(container, data, options);
    var currentGraph = this;
    this._graph.on("stabilized", function (params) {
        currentGraph._graph.setOptions({physics: false});
    });
    this._popup = $('<div/>').qtip({
        node: false,
        content: {
            text: 'Domain: <b>cipo.rnp.br</b>'
        },
        position: {
            at: "top center",
            my: "bottom center",
            effect: false,
            adjust: {
                x: 50,
                y: 50
            },
            target: 'event',
            container: $('#graph-v'),
            viewport: $(window)
        },
        show: false,
        hide: false,
        style: {
            classes: 'qtip-light qtip-shadow'
        }
    }).qtip('api');
    this._popup.set('visible', false);
    this._graph.on("click", function (params) {
        if(params['nodes'].length > 0) {
            //console.log(' click node:', params);
            $( "#"+currentGraph._canvasDivId ).trigger( "vgraph.nodeClick",  currentGraph._nodes.get(params.nodes[0]).id);
        } else {
            currentGraph._popup.hide();
            //console.log('click fora');
        }
    });
    this._graph.on("zoom", function () {
        currentGraph._popup.hide();        
    });
    this._graph.on("dragEnd", function () {
        currentGraph._popup.hide();        
    });
    this._graph.on("dragStart", function () {
        currentGraph._popup.hide();            
    });
    this._graph.on("resize", function () {
        if(currentGraph._popup.get('visible')) {
            currentGraph._popup.hide();    
            currentGraph._popup.set('visible', false);
        }     
    });
    
    /*this._graph.on("click", function (params) {
        if(params.nodes.length > 0) {
            $( "#"+divId ).trigger( "nodeClick",  currentGraph._nodes.get(params.nodes[0]).id);
            var nodePos = currentGraph.getPositions(params.nodes[0]);
            var d = document.getElementById('tooltip');
            var pos = currentGraph.canvasToDOM({x:nodePos[params.nodes[0]].x,y:nodePos[params.nodes[0]].y});
            d.style.left = pos.x + 900 +'px';
            d.style.top = pos.y - 85 +'px';
            $("#tooltip").html('<b>Name</b>: ' + nodes.get(params.nodes[0]).label + '<br><a href="#">Show details</a>');
            $("#tooltip").show();
        } else if (params.edges.length > 0) {
            var d = document.getElementById('tooltip');
            var edge = edges.get(params.edges[0]);
            d.style.left = params.pointer.DOM.x + 5 + 'px';
            d.style.top = params.pointer.DOM.y - 50 +'px';
            $("#tooltip").text((edge.count > 1) ? edge.count + " links" : edge.count + " link");
            $("#tooltip").show();
        } else $("#tooltip").hide();
    });
    network.on("dragging", function (params) {
        $("#tooltip").hide();
    });
    network.on("zoom", function (params) {
        $("#tooltip").hide();
    });*/
}

VGraph.prototype.showPopup = function(nodeId, content) {
    this._popup.set('visible', true);
    var pos = this._graph.getPositions(nodeId);
    pos = this._graph.canvasToDOM(pos[nodeId]);
    if(content != null) this._popup.set('content.text', content);
    this._popup.set('position.target', [ pos.x, pos.y ]).show();
}

VGraph.prototype.closePopups = function() {
    this._popup.set('visible', false);
    this._popup.hide();    
}
