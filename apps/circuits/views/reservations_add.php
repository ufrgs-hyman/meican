<?php

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

$args = $this->passedArgs;
$timers_exist = isset($args->timers) ? TRUE : FALSE;

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

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

?>

<link type="text/css" href="layouts/jquery-ui-1.8.13.custom.css" rel="stylesheet" />

<h1><?php echo _("Reservation Creation Wizard"); ?></h1>

<form id="reservation_add" method="POST" action="<?php echo $this->buildLink(array('action' => 'submit')); ?>" onsubmit="validateReservationForm();">
    
    <?php $this->addElement('reservation_tab1'); ?>
         
    <ul class="tabs inactive" id="ul-tabs">
        <li id="t1" class="ui-state-disabled"><a href="#tab1" class="link_tab"><?php echo _('Endpoints & Bandwidth'); ?></a></li>
        <li id="t2" class="ui-state-disabled"><a href="#tab2" class="link_tab"><?php echo _('Timer'); ?></a></li>
        <li id="t3" class="ui-state-disabled confirm"><a id="l3" href="#tab3" class="confirm"><?php echo _('Confirmation'); ?></a></li>
    </ul>
    
    <div class="tab_container inactive" id="div-tabs">
        
        <div id="tab1" class="cont_tab" style="display: none">
            
            <br/>         
            <table style="width:100%">
                <tr>
                    <td style="width:1%"></td>
                    <td>
                        <?php echo _("<h1>Endpoints</h1>"); ?>
                    </td>
                    <td style="width:1%"></td>
                </tr>
                <tr>
                    <td style="width:1%"></td>
                    <td>
                        <?php echo _("Select the Origin and Destination Networks by clicking on the Map Markers with any button and then choosing an option from the pop-up menu."); ?>
                    </td>
                    <td style="width:1%"></td>
                </tr>
                <tr>
                    <td style="width:1%"></td>
                    <td>
                        <?php echo _("Once you select the Endpoints, choose the Device and Port settings on the right side."); ?>
                    </td>
                    <td style="width:1%"></td>
                </tr>                
            </table>
            
            <?php $this->addElement('reservation_tab2'); ?>

            <br/><br/>
            
            <div id="div-bandwidth">
                <table style="width:100%">
                    <tr>
                        <td style="width:1%"></td>
                        <td>
                            <?php echo _("<h1>Bandwidth</h1>"); ?>
                        </td>
                        <td style="width:1%"></td>
                    </tr>                    
                </table>
            
                <table style="width: 100%"> 
                    <tr>
                        <td style="width:1%"></td>
                        <td style ="width: 45.5%">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 10%">100</td>
                                    <td style="width: 10%">200</td>
                                    <td style="width: 10%">300</td>
                                    <td style="width: 10%">400</td>
                                    <td style="width: 10%">500</td>                                                        
                                    <td style="width: 10%">600</td>
                                    <td style="width: 10%">700</td>
                                    <td style="width: 10%">800</td>
                                    <td style="width: 10%">900</td>
                                    <td style="width: 10%">1000</td>                                                                                        
                                </tr>
                            </table>
                        </td>                    
                        <td style="width: 54.5%">                        
                        </td>                   
                    </tr>
                </table>
                
                <table style="width:100%">
                    <tr>
                        <td style="width:4%"></td>
                        <td>
                            <?php $this->addElement('reservation_tab3'); ?> 
                        </td>
                    </tr>                    
                </table>
                
                <table style="width:100%">
                    <tr>
                        <td style="width:1%"></td>
                        <td>
                            <label id="amount_label" for="amount"></label>
                            <input type="text" id="amount" style="border:0; color:#000; font-weight:bold;" size="100"/>            
                        </td>
                    </tr>
                </table>
            </div>  
            
            <div class="controls">
                <input type="button" id="bc1" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>
                <input type="button" id="bn1" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);"/>
            </div>    
            
        </div>
        
        <?php $this->addElement('timer_recurrence'); ?>
            
        <div id="tab2" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab4', $this->passedArgs); ?>       
        </div>
        <div id="tab3" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab5'); ?>            
        </div>
    </div>
    
</form>
