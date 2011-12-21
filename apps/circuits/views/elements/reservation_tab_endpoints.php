    <p style='color:black;display: none;'><?php echo _("Select source and destination networks by clicking on the map markers with any button and then choosing an option from the pop-up menu. After selecting the endpoints, choose the device and port on the right pane."); ?></p>
    <div class="tab_subcontent" style="border: solid #BBB 1px; box-shadow: 2px 2px 4px #888; border-image: initial;">
        <div id="edit_map_canvas" style="width:700px; height: 480px;"></div>
    </div>
    <div class="tab_subcontent" style="padding-left:10px;">
        <?= $this->element('reservation_tab_point', array('type' => 'source')); ?>
        <div id="bandwidth_bar">
            <div id="bandwidth_bar_text">
            <label id="amount_label" for="amount"></label>
            <label id="amount" style="border:0; color:#000; font-weight:bold; width:100%"></label>
            <?php /*<label id="bandwidth_label" for="bandwidth"><?php echo _("Bandwidth");?></label>*/ ?>
            <input type="text" name="bandwidth" id="bandwidth" value="200" class="integer-input" size="4" step="100" style="width:50px;" disabled="disabled"/>
            <label id="bandwidth_un" for="bandwidth"><?php echo _("Mbps");?></label>
            <?php //<input type="hidden" name="bandwidth" id="bandwidth" value="200"/> ?>
            </div>
            <div id="bandwidth_bar_inside"></div><?php //var sizepx = 5; $('#bandwidth_bar').click(function(){ sizepx = sizepx+5;  $('#bandwidth_bar div').animate({width: sizepx+'%'}, 100); });?>
        </div>
        <?= $this->element('reservation_tab_point', array('type' => 'destination')); ?>
        
<?php /*<div style="width: 100%; margin-top: 5px">
            <label id="amount_label" for="amount"></label>
            <label id="amount" style="border:0; color:#000; font-weight:bold; width:100%"></label>
        </div>
        <input disabled="disabled" type="checkbox" name="vlan_options" id="showVlan_checkbox" onclick="showVlanConf()"/><p style="vertical-align: middle; display:inline"><label for="showVlan_checkbox"><?php echo _("Show VLAN configuration"); ?></label></p>
        <div id="div_vlan" style="display:none">
            <?php $this->addElement('vlan'); ?><br/>
        </div>*/?>
    </div>
    <?php //$this->addElement('reservation_tab3'); ?>
    <div style="clear:both;"></div>
