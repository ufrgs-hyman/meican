<?php

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

$args = $this->passedArgs;
$timers_exist = isset($args->timers) ? TRUE : FALSE;

$freq_types = array();
unset($freq);
$freq->value = "DAILY";
$freq->descr = _("Everyday");
$freq_types[] = $freq;

unset($freq);
$freq->value = "WEEKLY";
$freq->descr = _("Weekly");
$freq_types[] = $freq;

unset($freq);
$freq->value = "MONTHLY";
$freq->descr = _("Monthly");
$freq_types[] = $freq;

//<link type="text/css" rel="stylesheet" href="<?php echo $this->url(); >webroot/css/jquery-ui-1.8.13.custom.css" />
?>



<h1><?php echo _("Circuit reservation wizard"); ?></h1>

<form id="reservation_add" method="POST" action="<?php echo $this->buildLink(array('action' => 'submit')); ?>" onsubmit="validateReservationForm();">
    
    <?php $this->addElement('reservation_tab1'); ?>
         
    <ul class="tabs inactive" id="ul-tabs">
        <li id="t1" class="ui-state-disabled"><a href="#tab1" class="link_tab"><?php echo htmlentities(_('Endpoints & Bandwidth')); ?></a></li>
        <li id="t2" class="ui-state-disabled"><a href="#tab2" class="link_tab"><?php echo _('Timer'); ?></a></li>
        <li id="t3" class="ui-state-disabled confirm"><a id="l3" href="#tab3" class="confirm"><?php echo _('Confirmation'); ?></a></li>
    </ul>
    
    <div class="tab_container inactive" id="div-tabs">
        
        <div id="tab1" class="cont_tab" style="display: none">
            
            <br/>         
            <table class="withoutBorder" style="width:100%">
                <tr>
                    <td style="width:1%"></td>
                    <td class="left">
                        <p style='color:black;'><?php echo _("Select source and destination networks by clicking on the map markers with any button and then choosing an option from the pop-up menu. After selecting the endpoints, choose the device and port on the right pane."); ?></p>
                    </td>
                    <td style="width:1%"></td>
                </tr>                
            </table>
            
            <?php $this->addElement('reservation_tab_endpoints'); ?>

            <br/><br/>            
            
            <div class="control_tab">
                <input type="button" id="bn1" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);"/>        
                <input type="button" style="float:right" id="clearpath" class="clear" value="<?php echo _('Clear'); ?>" onClick="edit_clearAll();"/>
                <input type="button" style="float:right" id="bc1" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>
            </div>
            
        </div>
        
        <?php //$this->addElement('timer_recurrence'); ?>
            
        <div id="tab2" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab4', $this->passedArgs); ?>       
        </div>
        <div id="tab3" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab5'); ?>            
        </div>
    </div>
    
</form>
