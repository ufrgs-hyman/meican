/**
 * MeicanGraph 1.0
 *
 * A DCN topology visualization based on Vis.js library.
 *
 * @copyright (c) 2015, Maurício Quatrin Guerreiro @mqgmaster
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this
 * list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

function MeicanGraph(canvasDivId) {
    this._graph;                     // vis.js network
    this._canvasDivId = canvasDivId; 
    this._nodes = new vis.DataSet(); // nodes
    this._links = new vis.DataSet(); // edges
    this._currentNodeType;           // current node type visible
    this._domainsList;               // domains list reference;
    this._tooltip;
};

MeicanGraph.prototype.show = function() {
    if($("#graph-v").length == 1) {
        $("#graph-v").show();
    } else {
        $("#"+this._canvasDivId).append('<div id="graph-v" style="width:100%; height:100%;"></div>');
        this.build("graph-v");
    }
}

MeicanGraph.prototype.hide = function() {
    if($("#graph-v").length == 1) {
        this._graph.destroy();
        $("#graph-v").remove();
    }
}

MeicanGraph.prototype.setDomains = function(list) {
    this._domainsList = list;
}

MeicanGraph.prototype.getDomain = function(id) {
    for (var i = 0; i < this._domainsList.length; i++) {
        if (this._domainsList[i].id == id) return this._domainsList[i];
    }
}

MeicanGraph.prototype.addNodes = function(objects, type, loadPosition) {
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
    this._graph.stabilize();
}

MeicanGraph.prototype.addNode = function(id, name, type, domainId, x,y, color) {
    var physics = true;
    if(x && y) physics = false;
    this._nodes.add({
        id: type + id, 
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

MeicanGraph.prototype.addLinks = function(objects, type) {
    var size = objects.length;
    for (var src in objects) {
        for (var i = 0; i < objects[src].length; i++) {
            this.addLink(type+src,type+objects[src][i], type);
        }
    }
}

MeicanGraph.prototype.addLink = function(srcId, dstId, type) {
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

MeicanGraph.prototype.showNode = function(nodeId) {
    this._graph.selectNodes([nodeId]);
    this._graph.focus(nodeId);
}

MeicanGraph.prototype.setTypeVisible = function(type) {
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

MeicanGraph.prototype.fit = function() {
    this._graph.fit();
}

MeicanGraph.prototype.build = function(divId) {
    $("#"+divId).show();
    var container = document.getElementById(divId);
    var data = {
        nodes: this._nodes,
        edges: this._links
    };
    var options = {
        height: "98%",
        edges: {
            color: "#2B7CE9",
            width: 1,
            "smooth": {
              "type": "continuous",
              "forceDirection": "none"
            }
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
        }
    };
    this._graph = new vis.Network(container, data, options);
    var currentGraph = this;
    this._graph.on("stabilized", function (params) {
        currentGraph._graph.setOptions({physics: false});
    });
    this._tooltip = $('<div/>').qtip({
        node: false,
        content: {
            text: 'Domain: <b>cipo.rnp.br</b>'
        },
        position: {
            at: "top center",
            my: "bottom center",
            effect: false,
            adjust: {
                x: 134,
                y: 32
            },
            target: 'event',
            container: $('#graph_canvas'),
            viewport: $(window)
        },
        show: false,
        hide: false,
        style: {
            classes: 'qtip-light qtip-shadow'
        }
    }).qtip('api');
    this._graph.on("click", function (params) {
        if(params['nodes'].length > 0) {
            console.log(' click node:', params);
            $( "#"+currentGraph._divId ).trigger( "nodeClick",  currentGraph._nodes.get(params.nodes[0]).id);
            /*var pos = currentGraph._graph.getPositions(params['nodes'][0]);
            pos = currentGraph._graph.canvasToDOM(pos[params['nodes'][0]]);
            currentGraph._tooltip.set('position.target', [ pos.x, pos.y ]).show();
            currentGraph._tooltip.set('node', params['nodes'][0]);
        } else {
            currentGraph._tooltip.set('node', false);
            currentGraph._tooltip.hide();
            console.log('click fora');*/
        }
    });
   /* this._graph.on("zoom", function () {
        currentGraph.showTooltip();
        console.log('zoom');
    });
    this._graph.on("dragEnd", function () {
        currentGraph.showTooltip();
    });
    this._graph.on("dragStart", function () {
        currentGraph._tooltip.hide();            
    });*/
    
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
    network.on("resize", function (params) {
        $("#tooltip").hide();
    });
    network.on("zoom", function (params) {
        $("#tooltip").hide();
    });*/
}

MeicanGraph.prototype.showTooltip = function() {
        if (this._tooltip.get('node')) {
            var pos = this._graph.getPositions(this._tooltip.get("node"));
            pos = this._graph.canvasToDOM(pos[this._tooltip.get("node")]);
            this._tooltip.set('position.target', [ pos.x, pos.y ]).show();
        }
    }
