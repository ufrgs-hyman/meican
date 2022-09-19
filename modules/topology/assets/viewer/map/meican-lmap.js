/**
 * Meican LMap 1.1
 *
 * A DCN topology viewer based on Leaflet Javascript library.
 *
 * @copyright Copyright (c) 2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

var flagPortLocation = false;
var flagNetworkWasClicked = false;

function LMap(canvasDivId) {
    this._canvasDivId = canvasDivId;
    this._map;                          // Leaflet Map
    this._nodes = [];                   // markers/nodes container
    this._stackDomainsToBeExpanded = [];
    this._nodesL = []; 
    this._nodesN = []; 
    this._links = [];                   // polylines/links/edges container
    this._nodeType;                     // current node type visible
    this._topology = [];                // topology
    this._lastShowedMarker; 
    this._cluster;
    this._portsSize = 0;
    this._nodeAutoInc = 1;
    this._linkAutoInc = 1;
    this._expandedDomainNodes = [];
    this._lockRoutine = false;
};

LMap.prototype.show = function(instantRefresh) {
    let mapId = "map-n";
    
    if(flagPortLocation)
    mapId = "map-l";
    
    if($('#'+mapId).length == 1) {
        $('#'+mapId).show();
    } else {
        $("#"+this._canvasDivId).append('<div id="'+mapId+'" style="width:100%; height:100%;"></div>');
        this.build(mapId);
    }
    
    var currentMap = this._map;
    
    if(instantRefresh)
    this._map.invalidateSize(true);
    else
    setTimeout(function() {
        currentMap.invalidateSize(true);
    }, 200);
        
}

LMap.prototype.hide = function() {
    let mapIds = ['#map-n', '#map-l'];
    mapIds.forEach(function(mapId){
        if($(mapId).length == 1) {
            $(mapId).hide();
        }
    });
}

LMap.prototype.getPortsSize = function() {
    return this._portsSize;
}

LMap.prototype.addPort = function(id, name, dir, cap, nodeId, aliasNodeId, aliasPortId, type) {

    var node = this.getNode(nodeId);
    this._portsSize++;

    node.options.ports[id] = {
        name: name,
        device: node,
        dir: dir,
        cap: cap,
        circuits: [],
        linkIn: null,
        linkOut: null,
        status: 0
    };

    if(aliasNodeId != null) {
        var dstNode = this.getNode(aliasNodeId);
        var dstPort = dstNode.options.ports[aliasPortId];
        var linkIn = this.getLink(aliasNodeId+nodeId);
        if(linkIn == null) {
            linkIn = this.addLink(aliasNodeId+nodeId, aliasNodeId, nodeId, type, true, cap);
        } 

        linkIn.options.fromPort = dstPort;
        linkIn.options.toPort = node.options.ports[id];
        
        var linkOut = this.getLink(nodeId+aliasNodeId);
        if(linkOut == null) {
            linkOut = this.addLink(nodeId+aliasNodeId, nodeId, aliasNodeId, type, true, cap);
        }

        linkOut.options.fromPort = node.options.ports[id];
        linkOut.options.toPort = dstPort;
        
        node.options.ports[id].linkIn = linkIn;
        node.options.ports[id].linkOut = linkOut;
    }

    return node.options.ports[id];
}

LMap.prototype.getLink = function(id) {
    var size = this._links.length;
    for(var i = 0; i < size; i++){
        if ((this._links[i].options.id.toString()) == (id.toString())) {
            return this._links[i];
        }
    }
    
    return null;
}

LMap.prototype.getPortsByLocation = function(location_name)  {
    let locations = [];

    for (let i = this._topology['ports'].length - 1; i >= 0; i--) {
        if (this._topology['ports'][i].location_name == location_name)
            locations.push(this._topology['ports'][i]);
    }

    return locations;
}

LMap.prototype.addIntraLink = function(location_link)    {
    if(!location_link.source || !location_link.destination) 
        return null;
    let color = '#cccccc';
    let latLngList = [];

    if(!location_link.source_coords)
        return;
    latLngList.push(location_link.source_coords);

    if(!location_link.destination_coords)
        return;
    latLngList.push(location_link.destination_coords);

    let cap = (location_link.capacity >= 1000)? (location_link.capacity/1000.) + ' Gbps' : location_link.capacity + ' Mbps';
    
    if (latLngList.length > 1) {
        var link = L.polyline(
            latLngList, 
            {
                id: this._linkAutoInc++,
                from: location_link.source,
                to: location_link.destination,
                traffic: 0,
                directedCircuits: [],
                color: color,
                opacity: 0.7,
                weight: 5,
            }).addTo(this._map).bindPopup(
                        'Link between <b>' + 
                        location_link.source_location +
                        '</b> and <b>' +
                        location_link.destination_location +
                        '</b><br><b>Capacity</b>: '+ cap);

        this._links.push(link);
    } else return null;

    var current = this;

    link.on('click', function(e) {
        $("#"+current._canvasDivId).trigger("lmap.linkClick", link);
    });

    link.on('mouseover', function(e) {
        $("#"+current._canvasDivId).trigger("lmap.linkHover", link);
    })

    return link;

}

LMap.prototype.addLink = function(from, to, partial, cap, color, mode) {
    if(!from || !to) return null;
    if(!color) color = '#b1b1b1';
    var latLngList = [];

    var src = this.getNodeByPort(from);
    if(src != null)
        latLngList.push(src.getLatLng());
    else {
        return;
    }

    var dst = this.getNodeByPort(to);
    if(dst != null)
        latLngList.push(dst.getLatLng());
    else {
        return;
    }

    if(partial) {
        latLngList[1] = L.latLngBounds(latLngList[0], latLngList[1]).getCenter();
    }


    let capUnit = (cap > 1000) ? (cap/1000.) + '</b> Gbps' :  cap + '</b> Mbps';
    capText = (cap)? '- Max reservable capacity: <b>' + capUnit : '';

    if (latLngList.length > 1) {
        let srcName = null;
        let dstName = null;
        let srcPortName = null;
        let dstPortName = null;
        let showDetailedInfo = 'none';

        srcName = meicanMap.getNodeByPort(from).options.name.split(':')[0];
        dstName = meicanMap.getNodeByPort(to).options.name.split(':')[0];

        
        let ports = meicanMap.getNodeByPort(from).options.ports

        for (var i = ports.length - 1; i >= 0; i--) {
            if(ports[i].id == from){
                srcPortName = ports[i].name;
                break;
            }
        }

        ports = meicanMap.getNodeByPort(to).options.ports
        
        for (var i = ports.length - 1; i >= 0; i--) {
            if(ports[i].id == to){
                dstPortName = ports[i].name;
                break;
            }
        }

        if(mode == 'viewer')
            showDetailedInfo = 'show';

        var link = L.polyline(
            latLngList, 
            {
                id: this._linkAutoInc++,
                from: from,
                to: to,
                traffic: 0,
                directedCircuits: [],
                color: color,
                opacity: 0.7,
                weight: 4,
            }).addTo(this._map).bindPopup( this._createPopupContent(srcName, dstName, srcPortName, dstPortName, capText, showDetailedInfo) );

        this._links.push(link);
    } else return null;

    var current = this;

    link.on('click', function(e) {
        $("#"+current._canvasDivId).trigger("lmap.linkClick", link);
    });
    
    link.on('mouseover', function(e) {
        $("#"+current._canvasDivId).trigger("lmap.linkHover", link);
    });

    return link;
}

LMap.prototype.removeLink = function(link) {
    this._map.removeLayer(link);
    ///to do
}

LMap.prototype.hideLink = function(link) {
    this._map.removeLayer(link);
}

LMap.prototype.showLink = function(link) {
    this._map.addLayer(link);
}

LMap.prototype.getLinks = function() {
    return this._links;
}

LMap.prototype.removeLinks = function() {
    if (this._links.length > 0) {
        for (var i = 0; i < this._links.length; i++) {
            this.removeLink(this._links[i]);
        };
    }
}

LMap.prototype.getNodeByPosition = function(position, domain, location_name) {
    for (var i = this._nodes.length - 1; i >= 0; i--) {
        if ((this._nodes[i].getLatLng().lat === position.lat) && 
            (this._nodes[i].getLatLng().lng === position.lng)) {
            if (this._nodes[i].options.ports[0].network.domain == domain && (this._nodes[i].options.ports[0].location_name == undefined || !flagPortLocation))
                return this._nodes[i];
            else if(this._nodes[i].options.ports[0].location_name == location_name)
                return this._nodes[i];
            else
                return this.getNodeByPosition(L.latLng(position.lat + 0.001, position.lng), domain, location_name);
        }
    }

    return null;
}

LMap.prototype.getParentPosition = function(port) {
    if (port.network.latitude != null)
        return L.latLng([port.network.latitude, port.network.longitude]);

    if(port.network.domain.providers)   {
        for (let i = port.network.domain.providers.length - 1; i >= 0; i--) {
            if (port.network.domain.providers[i].latitude != null)
                return L.latLng([port.network.domain.providers[i].latitude, 
                    port.network.domain.providers[i].longitude]);
        }
    }
    return L.latLng([0,0]);
}

LMap.prototype.addNode = function(port, color, mode) {
    if(!port.network)
        return;
    
    if (!color) 
    color = port.network.domain.color;
    
    if(flagPortLocation && port.lat != null && port.lng != null) {
        var pos = L.latLng([port.lat,port.lng]);
    } else if (!flagPortLocation && typeof port.network !== 'undefined') {
        var pos = this.getParentPosition(port);
    } else {
        var pos = L.latLng([0, 0]);
    }
    if(pos.lat == 0 && pos.lng == 0){
        return;
    }

    pos.lng = this.getAdjustedLng(pos.lng);

    var node = this.getNodeByPosition(pos, port.network.domain, port.location_name);

    if (node == null) {
        var icon = L.divIcon({
            iconSize: [22,22],
            iconAnchor: [11, 22],
            popupAnchor: [0,-24],
            tooltipAnchor: [12, -12],
            html: '<svg width="25" height="26" xmlns="http://www.w3.org/2000/svg">' + 
            '<g>' +
            '<path stroke="black" fill="' + color + '" d="m1,5l20,0l-10,20l-10,-20z"/>' + 
            '</g>' + 
            '</svg>',
            className: 'marker-icon-svg',
        });


        let nodeType = "domain";
        if(flagPortLocation)
            nodeType = "location"
        
        node = L.marker(
            this.buildNodePosition(pos), 
            {
                id: this._nodeAutoInc++, 
                icon: icon,
                name: port.urn,
                type: nodeType,
                ports: [port]
            }
        ).bindPopup("#");

        if(mode != 'status'){
            if(flagPortLocation)
                node.bindTooltip(port.location_name, {permanent:true, direction: 'top'}).openTooltip();
            else
                node.bindTooltip(port.network.domain.name, {permanent:true, direction: 'left'}).openTooltip();
        }

        this._nodes.push(node);
        this._cluster.addLayer(node);
        if( !flagPortLocation ){
            if (port.network.domain.grouped_nodes == 0){
                this._stackDomainsToBeExpanded.push(node.options.id);
            }
        }

        var currentMap = this;

        node.on('click', function(e) {
            $("#"+currentMap._canvasDivId).trigger("lmap.nodeClick", node);
        });
       

        // if( !this._lockRoutine ){
            // if (port.network.domain.grouped_nodes == false ){
                //         this._lockRoutine = true;                
                //         //this.expandLocations(node.options.id)
                //         console.log("Expanded domain: " + node.options.id );
                //         this._lockRoutine = false;                
            // }            
        // }                    
        // return node.options.id;
    } else {        
        node.options.ports.push(port);
        let configIcon = '" d="M1,11a10,10 0 1,0 20,0a10,10 0 1,0 -20,0"/>';

        if(node.options.type == "location")
            configIcon = '" d="M1,15a10,10 0 1,0 20,0a10,10 0 1,0 -20,0"/>';

        node.setIcon(L.divIcon({
            iconSize: [22,22],
            iconAnchor: [11, 22],
            popupAnchor: [0,-24],
            tooltipAnchor: [14, -12],
            // html: '<img src="t.svg" alt="tyy">',
            html: '<svg width="250" height="270" xmlns="http://www.w3.org/2000/svg">' + 
            //http://complexdan.com/svg-circleellipse-to-path-converter/
            // '<image href="t2h.png" height="50px" width="60px"/>' +
            // '<image xlink:href="t2h.png" type="image/png" height="50px" width="60px"></image>' +
            // '<image href="/images/swt.png" height="50px" width="60px"></image>' +
            // '<image xlink:href="/media/narciso/NarcisoHD/UFRGS/OpenRan/meican_development/meican/modules/topology/assets/viewer/map/t.svg" height="50px" width="60px"/>' +
            // '<image xlink:href="/media/narciso/NarcisoHD/UFRGS/OpenRan/meican_development/meican/modules/topology/assets/viewer/map/t2h.png" type="image/png" height="50px" width="60px"/>' +
            // '<image href="file:/media/narciso/NarcisoHD/UFRGS/OpenRan/meican_development/meican/modules/topology/assets/viewer/map/t2h.png" height="50px" width="60px"/>' +
            // '<image href="file:t2h.png" height="50px" width="60px"/>' +
            // '<image href="https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Simple_icon_time.svg/2560px-Simple_icon_time.svg.png" height="50px" width="60px"></image>' +
            // '<image href="https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Simple_icon_time.svg/2560px-Simple_icon_time.svg.png" height="50px" width="60px"/>' +
            // '<image href="https://www.shihoriobata.com/wp-content/uploads/2021/09/fox-drawing-easy-web.jpg" height="50px" width="60px"/>' +
            // '<image href="t.svg" height="200px" width="200px"/>' +
            '<path stroke="black" fill="' + color + configIcon + 
            '</svg>',

            // html: '<svg width="25" height="27" xmlns="http://www.w3.org/2000/svg">' + 
            // '<g>' +
            // //http://complexdan.com/svg-circleellipse-to-path-converter/
            // '<path stroke="black" fill="' + color + configIcon + 
            // '</g>' + 
            // '</svg>',
            className: 'marker-icon-svg',
        }));
        
        // return -1;
    }
}

LMap.prototype.prepareLabels = function() {
    let expandedDomainsNodes = this.getExpandedDomainNodes();

    this.removeLabels();

    for (var i = this._nodes.length - 1; i >= 0; i--) {
        var label = 'error';
        labels = [];
        for (var k = this._nodes[i].options.ports.length - 1; k >= 0; k--) {
            if(expandedDomainsNodes.length == 0){
                if(flagPortLocation){
                    if(this._nodes[i].options.ports[k].lat != null && this._nodes[i].options.ports[k].lng != null)
                        labels.push(this._nodes[i].options.ports[k].location_name);                  
                }else{
                    labels.push(this._nodes[i].options.ports[k].network.domain.name);
                }
            }else{
                for(var j = expandedDomainsNodes.length - 1; j >=0; j--) {
                    if(flagPortLocation && this._nodes[i].options.ports[k].network.domain.name == expandedDomainsNodes[j].options.ports[0].network.domain.name){
                        if(this._nodes[i].options.ports[k].lat != null && this._nodes[i].options.ports[k].lng != null)
                            labels.push(this._nodes[i].options.ports[k].location_name);                  
                    }else{
                        labels.push(this._nodes[i].options.ports[k].network.domain.name);
                    }
                }
            }
        }
        label = groupByDomain(labels);
        this._nodes[i].bindTooltip(label, {permanent:true, direction: 'left'}).openTooltip();
        
        if(flagNetworkWasClicked){ 
            this._nodes[i].setTooltipContent(label);
        }
    }
}

LMap.prototype.removeLabels = function() {
    for (var i = this._nodes.length - 1; i >= 0; i--) {
        this._nodes[i].unbindTooltip();
    }

}

LMap.prototype.setTopology = function(topology) {
    this._topology = topology;
}

LMap.prototype.getTopology = function() {
    return this._topology;
}

LMap.prototype.buildNodePosition = function(position) {
    size = this._nodes.length;
    lat = position.lat;
    lng = position.lng;

    for(var i = 0; i < size; i++){
        if ((this._nodes[i].getLatLng().lat === lat) && 
                (this._nodes[i].getLatLng().lng === lng)) {
            return this.buildNodePosition(L.latLng(position.lat + 0.001, position.lng));
        }
    }
    
    return position;
}

LMap.prototype.closePopups = function() {
    this._map.closePopup();
}

LMap.prototype.addPopup = function(infoWindow) {
}

LMap.prototype.openPopup = function(latLng, info) {
}

LMap.prototype.getNode = function(id) {
    var size = this._nodes.length;
    for(var i = 0; i < size; i++){
        if ((this._nodes[i].options.id.toString()) == (id.toString())) {
            return this._nodes[i];
        }
    }
    
    return null;
}

LMap.prototype.getNodeByPort = function(urn) {
    if (typeof urn == 'number') {
        for (var i = this._nodes.length - 1; i >= 0; i--) {
            for (var k = this._nodes[i].options.ports.length - 1; k >= 0; k--) {
                if (this._nodes[i].options.ports[k].id == urn)
                    return this._nodes[i];
            }
        }
    } else {
        for (var i = this._nodes.length - 1; i >= 0; i--) {
            for (var k = this._nodes[i].options.ports.length - 1; k >= 0; k--) {
                if (this._nodes[i].options.ports[k].urn == urn)
                    return this._nodes[i];
            }
        }
    }
    
    return null;
}

LMap.prototype.getLocationByPort = function(urn) {
    for (var i = this._topology['ports'].length - 1; i >= 0; i--) {
        if (this._topology['ports'][i].id == urn)
            return this._topology['ports'][i];
    }
}

LMap.prototype.getNodeByName = function(name) {
    var size = this._nodes.length;
    for(var i = 0; i < size; i++){
        if ((this._nodes[i].options.name) == name) {
            return this._nodes[i];
        }
    }
    
    return null;
}

LMap.prototype.searchMarkerByNameOrDomain = function (type, name) {
    results = [];
    name = name.toLowerCase();
    var domainId;

    if (!domainsList) domainsList = JSON.parse($("#domains-list").text());
    for (var i = 0; i < domainsList.length; i++) {
        if(domainsList[i].name.toLowerCase().indexOf(name) > -1){
            domainId = domainsList[i].id;
            break;
        }
    };

    var size = this._nodes.length;
    for(var i = 0; i < size; i++){
        if (this._nodes[i].type == type) {
            if ((this._nodes[i].name.toLowerCase()).indexOf(name) > -1) {
                results.push(this._nodes[i]);
            } else if (domainId == this._nodes[i].domainId) {
                results.push(this._nodes[i]);
            }
        }
    }
    
    return results;
}

LMap.prototype.getMarkerByDomain = function(type, domainId) {
    var size = this._nodes.length;
    for(var i = 0; i < size; i++){
        if (this._nodes[i].type == type && this._nodes[i].domainId == domainId) {
            return this._nodes[i];
        }
    }
    
    return null;
}

LMap.prototype.setInitialMapPosition = function(){
    var current = this;
    let lat = 10;
    let lng = -95;
    let viewport = {height: $(window).height(), width: $(window).width()};
    $.ajax({
        url: baseUrl+'/aaa/role/get-allowed-domains',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            let allowedDomains = response;

            let validAllowedDomains = allowedDomains.filter(function(elem){
                return (elem['lat'] != null && elem['lng'] != null);
            });

            if(validAllowedDomains.length <= 5 && validAllowedDomains.length != 0){
                if(validAllowedDomains.length == 1){
                    lat = validAllowedDomains[0].lat;
                    lng = validAllowedDomains[0].lng;
                    lng = current.getAdjustedLng(lng);
                    current._map.setView(L.latLng(lat,lng), 4);
                }else{
                    lat = 0;
                    lng = 0;
                    for(let i = validAllowedDomains.length-1; i >= 0; i--){
                        lat += parseFloat(validAllowedDomains[i].lat);
                        lng += current.getAdjustedLng(parseFloat(validAllowedDomains[i].lng));
                    }
                    lat /= validAllowedDomains.length;
                    lng /= validAllowedDomains.length;
                    current._map.setView(L.latLng(lat,lng), 3);
                } 
            } else{
                if(viewport.width < 1400)
                    current._map.setView(L.latLng(lat,lng), 2);
                else
                    current._map.setView(L.latLng(lat,lng), 3);
            }
        }
    });   
}

LMap.prototype.getAdjustedLng = function(lng){
    if(lng > 120){
        lng -= 360;
    }
    return lng;
}

LMap.prototype.setNodeType = function(type) {
    if(this._nodeType == type)
        return;

    this._nodeType = type;

    var size = this._nodes.length;
    
    for(var i = 0; i < size; i++){ 
        if (this._nodes[i].options.type == type) {
            this.showNode(this._nodes[i]);
        } else {
            this.hideNode(this._nodes[i]);
        }
    }  

    for (var i = 0; i < this._links.length; i++) {
        if (this._links[i].options.type == type) {
            this.showLink(this._links[i]);
        } else {
            this.hideLink(this._links[i]);
        }
    }
}

LMap.prototype.build = function(mapDiv) {
    this._map = L.map(mapDiv, {
        zoomControl: true
    });

    this.setInitialMapPosition();
    this._map.setMaxBounds( [[-90,-320], [90, 160]] );

    this._cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 20,
        zoomToBoundsOnClick: true,
        spiderfyOnMaxZoom: false,
    });

    this._map.addLayer(this._cluster);

    this.setType('osm3');

    $('#' + mapDiv).show();   
}

function sharedStart(array){
    if (array.length < 1)
        return removeInvalidChar(array[0]);

    var a= array.concat().sort(), 
    a1= a[0], a2= a[a.length-1], L= a1.length, i= 0;
    while(i<L && a1.charAt(i)=== a2.charAt(i)) i++;
    return removeInvalidChar(a1.substring(0, i));
}

function groupByDomain(array)   {
    var a = array.concat().sort();
    return a[0].split(':')[0];
}

function removeInvalidChar(str) {
    if (str.length < 2)
        return str;

    if (str.slice(-1) == '-' || 
        str.slice(-1) == ':' || 
        str.slice(-1) == '+' ||
        str.slice(-1) == '_')
        return removeInvalidChar(str.substring(0, str.length - 1));

    return str;
}

LMap.prototype.getNodes = function() {
    return this._nodes;
}

LMap.prototype.saveNodesL = function() {
    this._nodesL = this._nodes.slice();
}

LMap.prototype.saveNodesN = function() {
    this._nodesN = this._nodes.slice();
}

LMap.prototype.restoreNodesL = function() {
    this._nodes = this._nodesL.slice();
}

LMap.prototype.restoreNodesN = function() {
    this._nodes = this._nodesN.slice();
}

LMap.prototype.hideNode = function(node) {
    this._cluster.removeLayer(node);
}

LMap.prototype.showNode = function(node) {
    this._cluster.addLayer(node);
}

LMap.prototype.removeNodes = function() {
    var size = this._nodes.length;
    for (var i = 0; i < size; i++) {
        this._nodes[i].setMap(null);
    }
    this._nodes = [];
}

LMap.prototype.setType = function(mapType) {
    switch(mapType) {
        case "osm" : 
            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                minZoom: 2,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                subdomains: ['a','b','c']
            }).addTo( this._map );
            break;
        case "mq" : 
            L.tileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.jpg', {
                attribution: '&copy; MapQuest',
                subdomains: ['1','2','3','4']
                }).addTo(this._map);
            break;
        case 'rnp': 
            L.tileLayer('http://viaipe.rnp.br/mapa/{z}/{x}/{y}.png',{
                attribution: 'MEICAN Project | UFRGS | Map data &copy; 2019 <a href="http://www.rnp.br">RNP</a>',
                maxZoom: 18,
                minZoom: 2
            }).addTo(this._map);
            break;
        case "osm2" : 
            L.tileLayer('http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png', {
                maxZoom: 18,
                minZoom: 2,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                subdomains: ['a','b','c']
            }).addTo( this._map );
            break;
        case "osm3" : 
            L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {
                attribution: 'MEICAN Project | UFRGS | Map data &copy; 2021 <a href="http://www.rnp.br">RNP</a>',
                maxZoom: 18,
                minZoom: 2,
                subdomains: ['a','b','c']
            }).addTo( this._map );
            break;
    }
}

LMap.prototype.focusNode = function(id) {
    var marker = this.getNode(id);
    if(marker) {
        var bounds = new google.maps.LatLngBounds();

        bounds.extend(marker.getPosition()); 

        this._map.fitBounds(bounds);
        this._map.setZoom(11);

        this.closeWindows();
        this.openWindow(marker);
        this._lastShowedMarker = id;
    }
}

LMap.prototype.focusNodes = function() {
    var latLngs = [];
    for (var i = 0; i < this._nodes.length; i++) {
        latLngs.push(this._nodes[i].getLatLng());
    };
    this._map.fitBounds(L.latLngBounds(latLngs));
    var currentMap = this;
    setTimeout(function () {
        currentMap._map.setZoom(currentMap._map.getZoom() - 1);
    }, 300);
}

LMap.prototype.loadTopology = function(withLinks) {
    var current = this;
    
    this._loadDomains(withLinks);

    // Expansion of locations of domains with this property
    $( document ).ajaxStop(function() {
        while(current._stackDomainsToBeExpanded.length > 0){
            current.expandLocations( current._stackDomainsToBeExpanded.pop() );
            current.removeLinks();
            current._loadLinks();
        }
    });
}

LMap.prototype._loadDomains = function(withLinks) {
    var current = this;
    $.ajax({
        url: baseUrl+'/topology/domain/get-all',
        dataType: 'json',
        method: "GET",        
        success: function(response) {
            current._topology['domains'] = response;
            for (var i = current._topology['domains'].length - 1; i >= 0; i--) {
                current._topology['domains'][i]['providers'] = [];
            }
            current._loadProviders(withLinks);           
        }
    });    
}

LMap.prototype._loadProviders = function(withLinks) {
    var current = this;
    $.ajax({
        url: baseUrl+'/topology/provider/get-all',
        dataType: 'json',
        method: "GET",
        data: {
            cols: JSON.stringify(['id','name','latitude','longitude', 'domain_id'])
        },
        success: function(response) {
            current._topology['providers'] = response;
            for (var i = current._topology['providers'].length - 1; i >= 0; i--) {
                for (var k = current._topology['domains'].length - 1; k >= 0; k--) {
                    if (current._topology['providers'][i]['domain_id'] == current._topology['domains'][k]['id']) {
                        current._topology['domains'][k]['providers'].push(current._topology['providers'][i]);
                    }
                }
            }
            current._loadNetworks(withLinks);
        }
    });
}

LMap.prototype._loadNetworks = function(withLinks) {
    var current = this;
    $.ajax({
        url: baseUrl+'/topology/network/get-all',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            current._topology['networks'] = response;
            current._loadLocations();
            current._loadPorts(withLinks);
            for (var i = current._topology['networks'].length - 1; i >= 0; i--) {
                for (var k = current._topology['domains'].length - 1; k >= 0; k--) {
                    if (current._topology['networks'][i]['domain_id'] == current._topology['domains'][k]['id']) {
                        current._topology['networks'][i]['domain'] = current._topology['domains'][k];
                    }
                }
            }
        }
    });
}

LMap.prototype._loadPorts = function(withLinks) {
    var current = this;

    $.ajax({
        url: baseUrl+'/topology/port/json?dir=BI&type=ALL',
        method: "GET",        
        success: function(response) {
            current._topology['ports'] = response;
            for (var i = current._topology['ports'].length - 1; i >= 0; i--) {
                for (var k = current._topology['networks'].length - 1; k >= 0; k--) {
                    if (current._topology['ports'][i]['network_id'] == current._topology['networks'][k]['id']) {
                        current._topology['ports'][i]['network'] = current._topology['networks'][k];
                    }
                }
                if (current._topology['ports'][i].lat != null) {
                    current._topology['ports'][i].lat = parseFloat(current._topology['ports'][i].lat);
                    current._topology['ports'][i].lng = parseFloat(current._topology['ports'][i].lng);
                }
            }

            for (var i = current._topology['ports'].length - 1; i >= 0; i--) {
                if(current._topology['ports'][i].type == 'NSI'){
                    current.addNode(current._topology['ports'][i]);
                }
            }
            current.prepareLabels();
            if(withLinks)
                current._loadLinks();
        }
    });
}

LMap.prototype._loadLocations = function()  {
    let current = this;
    $.ajax({
        url: baseUrl + '/topology/location/get-location',
        method: "GET",
        success: function(response) {
            current._topology['location'] = response;
        }
    });
}


LMap.prototype._groupLinks = function(links, context)    {
    let link_capacity = [];

    links.forEach(function(x)   {
        let src = context.getLocationByPort(x[0]);
        let dst = context.getLocationByPort(x[1]);

        let link = {
            source: x[0],
            destination: x[1],
            source_location: src.location_name,
            destination_location: dst.location_name,
            source_coords: null,
            destination_coords: null,
            capacity: parseInt(src.max_capacity)
        };

        if(src.location_name != null && dst.location_name != null)  {
            let src_ports = context.getPortsByLocation(src.location_name);
            for(let i = src_ports.length-1; i >= 0; i--)  {
                if(src_ports[i].lat != null)    {
                    link.source_coords = L.latLng(src_ports[i].lat, src_ports[i].lng);
                }
            }

            let dst_ports = context.getPortsByLocation(dst.location_name);
            for(let i = dst_ports.length-1; i >= 0; i--)  {
                if(dst_ports[i].lat != null)    {
                    link.destination_coords = L.latLng(dst_ports[i].lat, dst_ports[i].lng);
                }
            }

            if(src.capacity != dst.capacity)    {
                console.log('Error'); // Show difference between capacities
            }

            let j = link_capacity.length -1;
            for(; j >= 0; j--)  {
                if(link_capacity[j].source_location == link.source_location && link_capacity[j].destination_location == link.destination_location)    {
                    link_capacity[j].capacity += link.capacity;
                    break;
                }
            }
            if(j == -1) {
                link_capacity.push(link);
            }
        }
    });

    return link_capacity;
}

LMap.prototype._loadLinks = function() {
    var current = this;
    $.ajax({
        url: baseUrl+'/topology/viewer/get-port-cap-links',
        dataType: 'json',
        method: "GET",
        success: function(response) {
            let links = [];
            for (var src in response) {
                for (let i = 0; i < response[src].length; i++)
                    current.addLink(parseInt(src),parseInt(response[src][i].port), false, response[src][i].max_capacity, null, 'viewer');
            }
            links = current._groupLinks(links, current);
            links.forEach(function(x){
                current.addIntraLink(x);
            });
        }
    });
}



LMap.prototype.getExpandedDomainNodes = function() {
    return this._expandedDomainNodes;
}

LMap.prototype.addExpandedDomainNode = function(node) {
    return this._expandedDomainNodes.push(node);
}

LMap.prototype.removeExpandedDomainNode = function(domainId) {
    let indexToRemove = this._expandedDomainNodes.findIndex(function(element){
        return element.options.ports[0].network.domain.id == domainId;
    });

    if (indexToRemove != -1)
        this._expandedDomainNodes.splice(indexToRemove, 1);

    return this._expandedDomainNodes.length;
}

LMap.prototype.getNodeIdByDomainId = function(domainId) {
    let expandedDomainNodes = this.getExpandedDomainNodes();

    for (var i = expandedDomainNodes.length - 1; i >=0; i--) {
        if(expandedDomainNodes[i].options.ports[0].network.domain.id == domainId)
            return expandedDomainNodes[i].options.id;
    }
}

LMap.prototype.removeNode = function(domainId, type) {
    let nodes = meicanMap.getNodes();

    for (var i = nodes.length - 1; i >= 0; i--) {
        if( (nodes[i].options.ports[0].network.domain_id == domainId) && (nodes[i].options.type == type) ){
            this._cluster.removeLayer(nodes[i]);
            this._nodes.splice(i, 1);
        }
    }
}

LMap.prototype.hasLocation = function(networkId) {
    let locations = this.getTopology()['location'];
    if (networkId != "" && networkId != null) {
        for (var i = locations.length - 1; i >= 0; i--) {
            if(locations[i].network_id == networkId){
                return true;
            }
        }
    }
    return false;
}

LMap.prototype.expandLocations = function(nodeId) {
    flagPortLocation = true;
    
    let node = this.getNode(nodeId);
    let nodes = this.getNodes();
    let ports = this.getTopology()['ports'];
    let domainId = node.options.ports[0].network.domain_id;
    
    this.addExpandedDomainNode(node);
    
    for (var i = ports.length - 1; i >= 0; i--) {
        if(ports[i].network.domain_id == domainId){
            this.addNode(
                ports[i]
                );
            }
        }
    this.hideNode(node);
    
    flagPortLocation = false;
}

LMap.prototype.groupLocations = function(nodeId) {
    flagPortLocation = false;
    let node = this.getNode(nodeId);
    let nodes = this.getNodes();
    let domainId = node.options.ports[0].network.domain_id;

    this.removeNode(domainId, "location");   
    this.showNode(meicanMap.getNode(meicanMap.getNodeIdByDomainId(domainId)));
    this.removeExpandedDomainNode(domainId);
}

LMap.prototype._createPopupContent = function(srcName, dstName, srcPortName, dstPortName, capText, showDetailedInfo) {
    return '<div style="width: 130%">' +
                'Link between <b>' + 
                srcName +
                '</b> and <b>' +
                dstName +
                '</b>&nbsp;&nbsp;<button style="display:' + showDetailedInfo + '" class="show-link-details" title="Show Link Details"><i class="fa fa-info"></i></button>'+'<br>' +
                '<div id="detailedLinkInformation" style="display:none">' +
                    '<u>Detailed Link Information:</u>' +
                    '<div style="padding-left: 10px">- '+
                        srcPortName + 
                        '&nbsp;<i class="fa fa-arrows-h" aria-hidden="true"></i>&nbsp;' + 
                        dstPortName + 
                        '<br>'+
                        capText +
                    '</div>' +
                '</div>' +
            '</div>'
}
