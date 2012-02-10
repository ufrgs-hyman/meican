<?php

$gris = $this->passedArgs->gris;
$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;
$res_id = $this->passedArgs->res_id;
$request = $this->passedArgs->request;
$bandwidth = $this->passedArgs->bandwidth;
$refresh = $this->passedArgs->refresh;
$usr_login = $this->passedArgs->usr_login;

?>

<h1><?php echo _("Reservation details"); ?></h1>

<table class="withoutBorder" style="width: 100%">
    <tr>
        <td style="width: 48%; vertical-align: top">
            <div id="res_mapCanvas" style="width:100%; height:365px;"></div>        
        </td>
        <td style="width: 48%; padding-left: 15px; vertical-align: top">
            <table style="min-width: 30%" class="withoutBorder">
                <tr>
                    <th style="border-bottom:none; padding-right: 5px"><?php echo _("Reservation name"); ?>:</th>
                    <th style="border-bottom:none; padding-left: 5px; color: #3a5879"><?php echo $name; ?></th>
                </tr>
                <tr>
                    <th style="border-bottom:none; padding-right: 5px; text-align: left"><?php echo _("User"); ?>: <?php echo $usr_login; ?></th>
                </tr>
            </table>
            
            <br/>
            <?php $this->addElement('view_flow', $flow); ?>
            
            <br/>
            <?php $this->addElement('view_bandwidth', $bandwidth); ?>
            
            <br/><br/>
            <?php $this->addElement('view_timer', $timer); ?>
            <br/>
            
            <?php if ($request) $this->addElement('view_request', $request); ?>

            <?php if ($gris): ?>
            
            <form method="POST" action="<?php echo $this->buildLink(array('action' => 'cancel', 'param' => "res_id:$res_id,refresh:1")); ?>">    
            
                <?php if ($refresh): ?>
                <div class="controls">
                    <input type="button" class="refresh" value="<?php echo _("Refresh") ?>" onclick="griRefreshStatus(<?php echo $res_id; ?>);" />
                    <input type="submit" class="cancel" disabled="disabled" id="cancel_button" value="<?php echo _("Cancel reservations"); ?>" onclick="return confirm('<?php echo _('Cancel the selected reservations?'); ?>')"/>
                </div>
                <br style="clear: both"/>
                <?php endif; ?>
            
            <?php $this->addElement('list_gris', array('gris' => $gris, 'refresh' => $refresh)); ?>
                
            </form>
            
        <?php endif; ?>
                
        </td>
    </tr>
</table>

<br/>

<div class="controls">
    <input class="back" type="button" onClick="redir('<?php $action = ($refresh) ? "status" : "history"; echo $this->buildLink(array("action" => $action)); ?>');" value="<?php echo _("Back to reservations"); ?>"/>
</div>