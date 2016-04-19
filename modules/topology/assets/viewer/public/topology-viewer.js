/**
 * Topology Viewer interface
 *
 * A DCN topology viewer
 *
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

function TopologyViewer(canvasDivId) {
    
    //RECOMENDATION
    //this._canvasDivId = canvasDivId;
    //this._view;                       // base view system
    //this._nodes;                      // nodes container
    //this._links;                      // links container
    //this._visibleNodeType;            // current node type visible
    //this._domains;                    // domains list reference;
};

TopologyViewer.prototype.show = function(viewType, nodeType) {
}

TopologyViewer.prototype.hide = function() {
}

TopologyViewer.prototype.addLink = function(path, nodeType) {
}

TopologyViewer.prototype.removeLink = function(link) {
}

TopologyViewer.prototype.getLinks = function() {
}

TopologyViewer.prototype.removeLinks = function() {
}

TopologyViewer.prototype.addNode = function(object, type, color) {
}

TopologyViewer.prototype.getDomain = function(id) {
}

TopologyViewer.prototype.setDomains = function(list) {
}

TopologyViewer.prototype.changeNodeColor = function(node, color) {
} 

TopologyViewer.prototype.closePopups = function() {
}

TopologyViewer.prototype.addPopup = function(infoWindow) {
}

TopologyViewer.prototype.openPopup = function(marker, extra) {
}

TopologyViewer.prototype.getNode = function(id) {
}

TopologyViewer.prototype.getNodeByDomain = function(type, domainId) {
}

TopologyViewer.prototype.getNodes = function() {
}

TopologyViewer.prototype.removeNodes = function() {
}

TopologyViewer.prototype.getVisibleNodeType = function() {
}

TopologyViewer.prototype.setVisibleNodeType = function(type) {
}

TopologyViewer.prototype.build = function(div) {
}

TopologyViewer.prototype.getView = function() {
}

TopologyViewer.prototype.showNode = function(id) {
}
