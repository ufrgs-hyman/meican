<?php

$gris = $this->passedArgs->gris;
$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;
$res_id = $this->passedArgs->res_id;
$request = $this->passedArgs->request;
$bandwidth = $this->passedArgs->bandwidth;
$refresh = $this->passedArgs->refresh;

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
            
                <?php if ($refresh): ?>
                <div class="controls">
                    <input type="button" class="refresh" value="<?php echo _("Refresh") ?>" onClick="griRefreshStatus(<?php echo $res_id; ?>);" />
                </div>
                <br style="clear: both"/>
                <?php endif; ?>
            
            <table class="list" style="width: 100%">

            <thead>
                <tr>
                    <th class="listHeader"></th>
                    <th class="listHeader"><?php echo _("Tool"); ?></th>
                    <th class="listHeader"><?php echo _("Global Reservation Identifier (GRI)"); ?></th>
                    <th class="listHeader" align="center">
                        <?php echo _("Status"); ?>
                        <img alt="<?php echo _("loading"); ?>" style="display:none" id="load_dynamic" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif"/>
                    </th>
                    <th class="listHeader"><?php echo _("Initial Date/Time"); ?></th>
                    <th class="listHeader"><?php echo _("Final Date/Time"); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($gris as $i => $g): ?>
                    <tr id="line<?php echo $i; ?>">
                        <td>
                            <input style="display: none" type="checkbox" id="cancel<?php echo $i; ?>" disabled name="cancel_checkbox[]" value="<?php echo $g->id; ?>" onClick="disabelCancelButton(this);"/>
                        </td>
                        <td>
                            OSCARS
                        </td>
                        <td>
                            <?php echo $g->id; ?>
                        </td>
                        <td>
                            <label id="status<?php echo $i; ?>"><?php echo $g->status; ?></label>
                            <img alt="<?php echo _("loading"); ?>" style="display:none" id="loading" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif"/>
                        </td>
                        <td>
                            <?php echo $g->start; ?>
                        </td>
                        <td>
                            <?php echo $g->finish; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </td>
    </tr>
</table>



<br/>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'cancel', 'param' => "res_id:$res_id")); ?>">    

    <div style="clear: both" class="controls">
        <input class="back" type="button" onClick="redir('<?php $action = ($refresh) ? "status" : "history"; echo $this->buildLink(array("action" => $action)); ?>');" value="<?php echo _("Back to reservations"); ?>" style="float: left"/>
        <input class="cancel" type="submit" disabled id="cancel_button" style="display: none; opacity:0.4; float: right;" value="<?php echo _("Cancel reservations"); ?>" onClick="return confirm('<?php echo _('Cancel the selected reservations?'); ?>')"/>
    </div>

</form>

<?php else : ?>

<div class="controls">
    <input class="back" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');" value="<?php echo _("Back to reservations"); ?>"/>
</div>

<?php endif; ?>