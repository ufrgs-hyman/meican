<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;

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
        <div class="lsidebar-pane" id="home">
            <h1 class="lsidebar-header">
                Topology Viewer options<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span>
            </h1>
            <br>
            <br><br>
            <div class="form-group">
                <label class="control-label" for="node-type-select">Node type:</label>
                <select id="node-type-select" class="form-control">
                    <option value="dom">Domain</option>
                    <option value="prov">Provider</option>
                    <option value="net">Network</option>
                    <option value="dev">Device</option>
                </select>
            </div>
            <br>
            <br>
            <div class="form-group">
                <label class="control-label" for="mode-select">Mode:</label>
                <select id="mode-select" class="form-control">
                    <option value="map">Map</option>
                    <option value="graph">Graph</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div id="canvas" class="lsidebar-map"></div>
