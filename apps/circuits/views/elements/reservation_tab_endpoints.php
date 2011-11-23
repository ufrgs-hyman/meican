<br/>

<div class="tab_content">
    <div class="tab_subcontent" style="border: solid #BBB 1px; box-shadow: 2px 2px 4px #888; border-image: initial;">
        <div id="edit_map_canvas" style="width:400px; height: 400px;"></div>
    </div>
    <div class="tab_subcontent" style="padding:10px;width:180px;">
        <?= $this->element('reservation_tab_point', array('type' => 'source')); ?>
        <div id="bandwidth_bar">
            <div>
            <label id="amount_label" for="amount"></label>
            <label id="amount" style="border:0; color:#000; font-weight:bold; width:100%"></label>
            <input type="hidden" name="bandwidth" id="bandwidth" value="200"/></div><?php //var sizepx = 5; $('#bandwidth_bar').click(function(){ sizepx = sizepx+5;  $('#bandwidth_bar div').animate({width: sizepx+'%'}, 100); });?>
        </div>
        <?= $this->element('reservation_tab_point', array('type' => 'destination')); ?>
        <div style="width: 100%; margin-top: 5px">
            <label id="amount_label" for="amount"></label>
            <label id="amount" style="border:0; color:#000; font-weight:bold; width:100%"></label>
            <input type="hidden" name="bandwidth" id="bandwidth" value="200"/>
        </div>

        <input disabled="disabled" type="checkbox" name="vlan_options" id="showVlan_checkbox" onclick="showVlanConf()"/><p style="vertical-align: middle; display:inline"><label for="showVlan_checkbox"><?php echo _("Show VLAN configuration"); ?></label></p>
        <div id="div_vlan" style="display:none">
            <br/>
            <?php $this->addElement('vlan'); ?>
            <br/><br/>
        </div>
    </div>
    <?php $this->addElement('reservation_tab3'); ?>
</div>
<br/>
