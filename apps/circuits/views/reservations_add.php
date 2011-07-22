<?php

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

$args = $this->passedArgs;
$timers_exist = isset($args->timers) ? TRUE : FALSE;

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

$start_date = $argsToElement->start_date;
$finish_date = $argsToElement->finish_date;
$start_time = $argsToElement->start_time;
$finish_time = $argsToElement->finish_time;
$timer = (isset($argsToElement->timer)) ? $argsToElement->timer : NULL;

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

<form id="reservation_add" method="POST" action="<?php echo $this->buildLink(array('action' => 'submit')); ?>">
    
    <?php $this->addElement('reservation_tab1'); ?>
         
    <ul class="tabs inactive" id="ul-tabs">
        <li id="t1" class="ui-state-disabled"><a href="#tab1" class="link_tab"><?php echo _('Endpoints & Bandwidth'); ?></a></li>
        <li id="t2" class="ui-state-disabled"><a href="#tab2" class="link_tab"><?php echo _('Timer'); ?></a></li>
        <li id="t3" class="ui-state-disabled"><a href="#tab3" class="link_tab"><?php echo _('Confirmation'); ?></a></li>
    </ul>
    
    <div class="tab_container inactive" id="div-tabs">
        
        <div id="tab1" class="cont_tab">
            
            <?php $this->addElement('reservation_tab2'); ?>
            <div align="center">
                <input type="button" id="clearpath" class="clear" value="<?php echo _('Clear'); ?>" onClick="edit_clearAll();"/>
            </div>
            <br/><br/>
                      
            <table style="width: 100%"> 
                <tr>
                    <td style="width: 27.5%">                        
                    </td>
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
                    <td style="width: 27%">                        
                    </td>                   
                </tr>
            </table>
                
              
            <?php $this->addElement('reservation_tab3'); ?>    
            
            <br/>
            <div align="center" style="width:100%">
                <label id="amount_label" for="amount"></label>
                <input type="text" id="amount" style="text-align: center; border:0; color:#000; font-weight:bold;" size="100"/>            
            </div>
            <br/>
            
            <div class="controls">
                <input type="button" id="bc1" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>
                <input type="button" id="bn1" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);"/>
            </div>    
            
        </div>
        
        <?php $this->addElement('timer_recurrence'); ?>
            
        <div id="tab2" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab4'); ?>       
            <input type ="button" id="validatimer" value="valida" onclick="testTimer()"/>
        </div>
        <div id="tab3" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab5'); ?>            
        </div>
    </div>
    
</form>
