<?php
//$res_name = isset($this->passedArgs->res_name) ? $this->passedArgs->res_name : null;

$args = $this->passedArgs;
//$timers_exist = isset($args->timers) ? TRUE : FALSE;
?>

<form id="reservation_add" method="POST" action="<?= $this->buildLink(array('action' => 'submit')) ?>">
    <h1>
        <label for="res_name"><?= _("Reservation name") . ": " ?></label>
        <input type="text" name="res_name" id="res_name" size="50" placeholder="<?= _('Typing a name for the reservation will unlock the map') ?>" title="<?= _('To create a reservation, first type a name') ?>"/>
    </h1>
    <div id="tabs-res" class="reservation-tabs" style="position:relative;">
        <div class="tab-overlay fade-overlay"> </div>
        <!--<div id="code">
            <img id="curl" src="webroot/img/fold.png"> -->
            <div id="tabs-1">
                <p style='color:black;display: none;'><?= _("Select source and destination networks by clicking on the map markers with any button and then choosing an option from the pop-up menu. After selecting the endpoints, choose the device and port on the right pane.") ?></p>
                <div id="subtab-map" class="tab_subcontent shadow-box">
                    <div id="edit_map_canvas" style="width:700px; height: 480px;"></div>
                </div>
                <div id="subtab-points" class="tab_subcontent" style="float: right; padding-left:2px;">
                    <?= $this->element('reservation_tab_point', array('type' => 'source')); ?>
                    <div id="bandwidth_bar">
                        <div id="bandwidth_bar_text">
                            <div style="text-align:center;">
                                <input type="text" name="bandwidth" id="bandwidth" value="100" class="integer-input" size="4" step="100" disabled="disabled"/>
                            </div>
                            <label id="bandwidth_un" for="bandwidth"><?= _("Mbps") ?></label>
                        </div>
                        <div id="bandwidth_bar_inside"></div>
                    </div>
                    <?= $this->element('reservation_tab_point', array('type' => 'destination')); ?>
                </div>
                <div style="clear:both;"></div>
            </div>            
        <!--</div>-->
        <div id="tabs-2" class="tab_content">
            <?php $this->addElement('timer_form', $args); ?>
        </div>
        <div id="tabs-4" class="control_tab">
            <input type="submit" id="bf"  class="ok" value="<?= _('Submit') ?>"/>
            <input type="button" id="bc1" class="cancel" value="<?= _('Cancel') ?>" onclick="redir('<?= $this->buildLink(array("action" => "history")) ?>');"/>
        </div>

    </div>
        <ul id="waypoints_order">
        </ul>    
        <input type="hidden" id="path_urn" name="path_urn"/>
</form>

<form>
    <div id="edp_dialog_form" title="<?= _("Search for endpoint"); ?>">
        <label for="edp_reference"><?= _("Fill in with a hostname, IP address or URN") ?></label>
        <br/>
        <input type="text" name="edp_reference" id="edp_reference" size="50" style="margin-top: 10px; margin-bottom: 7px;" placeholder="<?= _('Enter text') ?>" title="<?= _('Hostname, IP address or URN'); ?>"/>
        <input type="hidden" id="edp_dialog"/>
        <br/>
        <label id="dialog_msg"></label>
    </div>
</form>

<div id="copy_edp_dialog" title="<?= _("Copy endpoint link"); ?>">
    <label for="edp_link"><?= _("Copy and paste the link below") ?></label>
    <br/>
    <input type="text" name="edp_link" id="edp_link" size="50" style="margin-top: 10px;" title="<?= _('URN'); ?>" value="urn"/>
</div>