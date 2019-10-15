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
                <?= Yii::t("topology", "Topology Viewer options"); ?><span class="lsidebar-close"><i class="fa fa-caret-left"></i></span>
            </h1>
            <br>
            <div class="form-group">
                <label><?= Yii::t("topology", "Mode"); ?>:</label><br>
                <input type="radio" name="mode" value="map" checked> <?= Yii::t("topology", "Map"); ?>
                <input type="radio" name="mode" value="graph"> <?= Yii::t("topology", "Graph"); ?>
            </div>
        </div>
    </div>
</div>

<div id="canvas" class="lsidebar-map"></div>
