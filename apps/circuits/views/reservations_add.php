<?php
$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

$args = $this->passedArgs;
$timers_exist = isset($args->timers) ? TRUE : FALSE;

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;
?>
<link type="text/css" href="layouts/jquery-ui-1.8.13.custom.css" rel="stylesheet" />

<h1><?php echo _("Reservation Creation Wizard"); ?></h1>

<form id="reservation_add" method="POST" action="<?php echo $this->buildLink(array('action' => 'submit')); ?>">

    <ul class="tabs">
        <li id="t1"><a href="#tab1"><?php echo _('Reservation Name'); ?></a></li>
        <li id="t2" style="display: none"><a href="#tab2"><?php echo _('Endpoints'); ?></a></li>
        <li id="t3" style="display: none"><a href="#tab3"><?php echo _('Bandwidth'); ?></a></li>
        <li id="t4" style="display: none"><a href="#tab4"><?php echo _('Timer'); ?></a></li>
        <li id="t5" style="display: none"><a href="#tab5"><?php echo _('Confirmation'); ?></a></li>      
    </ul>

    <div class="tab_container">
        <div id="tab1" class="cont_tab">
            <?php $this->addElement('reservation_tab1');?>
        </div>
        
        <div id="tab2" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab2');?>
        </div>
        <div id="tab3" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab3');?>            
        </div>
        <div id="tab4" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab4');?>            
        </div>       
        <div id="tab5" style="display: none" class="cont_tab">
            <?php $this->addElement('reservation_tab5');?>            
        </div>          
    </div>
</form>
