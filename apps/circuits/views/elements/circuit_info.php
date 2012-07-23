<div id="subtab-points" class="tab_subcontent" style="float: right; padding-left:2px;">
    <?= $this->element('view_point', array('app' => 'circuits', 'type' => 'source', 'flow' => $flow)); ?>
    <div id="bandwidth_bar">
        <div id="bandwidth_bar_text">
            <div style="text-align:center;">
                <input type="text" id="lb_bandwidth" value="<?php echo $bandwidth . " " . _("Mbps") ?>" disabled="disabled" class="ui-widget ui-spinner-input"/>
            </div>
        </div>
        <div id="bandwidth_bar_inside" style="width: <?= round($bandwidth * 100 / 1000); ?>%"></div>
    </div>
    <?= $this->element('view_point', array('app' => 'circuits', 'type' => 'destination', 'flow' => $flow)); ?>
</div>