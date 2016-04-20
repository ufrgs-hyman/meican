<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

\meican\topology\assets\viewer\Index::register($this);

$this->params['hide-content-section'] = true;
$this->params['hide-footer'] = true;

?>

<div id="lsidebar" class="lsidebar collapsed">
    <!-- Nav tabs -->
    <div class="lsidebar-tabs">
        <ul role="tablist">
            <li><a title="Topology Viewer options" href="#home" role="tab"><i class="fa fa-gear"></i></a></li>
        </ul>
    </div>

    <!-- Tab panes -->
    <div class="lsidebar-content">
        <div class="lsidebar-pane icheck" id="home">
            <h1 class="lsidebar-header">
                Topology Viewer options<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span>
            </h1>
            <br>
            <div class="form-group">
                <label>Mode:</label><br>
                <input type="radio" name="mode" value="map" checked> Map
                <input type="radio" name="mode" value="graph" > Graph
            </div>
            <div class="form-group">
                <label>Node type:</label><br>
                <input type="radio" name="node-type" value="dom"> Domain
                <input type="radio" name="node-type" value="prov"> Provider
                <input type="radio" name="node-type" value="net"> Network
                <input type="radio" name="node-type" value="dev" checked> Device
            </div>
            <div class="form-group">
                <label>Topology type:</label><br>
                <input type="radio" name="topo-type" value="nsi"> NSI
                <input type="radio" name="topo-type" value="nmwg"> NMWG
                <input type="radio" name="topo-type" value="all" checked> All
            </div>
            <button id="save-positions-btn" class="btn btn-default">Save graph positions</button>
        </div>
    </div>
</div>

<div id="canvas" class="lsidebar-map"></div>
