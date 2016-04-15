<div style="clear: none; float: left; z-index: 90; position: absolute; margin-left: 2px; margin-top: 3px;">
    <div id="search-box" style="float: left; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 2px;" hidden>
    <input type="text" id="search-field" size="40">
    <button id="search-button"><span class="ui-icon-to-button-without-background ui-icon ui-icon-search" style="margin-left: 35%;"></span></button>
    </div>
    <div style="margin-left: 10px; margin-right: 6px; float: left;">
    <select id="viewer-mode-select" class="select-menu" style="width: 80px;" hidden>
        <optgroup label="<?= Yii::t("topology", "Geographic"); ?>">
            <option value="mg-s"><?= Yii::t("topology", "Map 1: Standard"); ?></option>
            <option value="mg-c"><?= Yii::t("topology", "Map 1: Clean"); ?></option>
            <option value="mg-t"><?= Yii::t("topology", "Map 1: Terrain"); ?></option>
            <option value="mg-s"><?= Yii::t("topology", "Map 1: Satellite"); ?></option>
            <option value="mg-h"><?= Yii::t("topology", "Map 1: Hybrid"); ?></option>
            <option value="ml-osm"><?= Yii::t("topology", "Map 2: OSM"); ?></option>
            <option value="ml-mq"><?= Yii::t("topology", "Map 2: MQ"); ?></option>
        </optgroup>
        <optgroup label="<?= Yii::t("topology", "Logic"); ?>">
            <option value="gv"><?= Yii::t("topology", "Graph"); ?></option>
        </optgroup>
    </select>
    </div>

    <select id="node-type-select" class="select-menu" style="width: 95px;" hidden>
        <optgroup label="<?= Yii::t("topology", "Dataplane"); ?>">
            <option value="dom"><?= Yii::t("topology", "Domains (only graph)"); ?></option>
            <option value="net"><?= Yii::t("topology", "Networks"); ?></option>
            <option value="dev"><?= Yii::t("topology", "Devices"); ?></option>
            <option value="port"><?= Yii::t("topology", "Ports (only graph)"); ?></option>
        </optgroup>
        <optgroup label="<?= Yii::t("topology", "Controlplane"); ?>">
            <option value="prov"><?= Yii::t("topology", "Providers"); ?></option>
        </optgroup>        
    </select>

    <button id="save-button">Save</button>
 
</div>