<?php

$gris = $this->passedArgs->gris;
$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;
$res_id = $this->passedArgs->res_id;
$request = $this->passedArgs->request;

?>

<h1><?php echo _("Reservation Details"); ?></h1>

<table>
    <tr>
        <th><?php echo _("Reservation name"); ?></th>
        <th><?php echo $name; ?></th>
    </tr>
</table>

    <div style="float:inherit">
        <?php $this->addElement('view_flow', $flow); ?>
    </div>

    <div style="margin-left: 10px">
        <input type="button" class="clear" value="<?php echo _("Clear") ?>" onClick="clearAll();">
        <input type="button" class="clear" value="<?php echo _("Toggle") ?>" onClick="toggleTopology();">
        <div id="res_mapCanvas" style="width:300px;height:235px;"></div> 
    </div>

    <?php $this->addElement('view_timer', $timer); ?>

<h3><?php echo _('Request'); ?></h3>

<?php $this->addElement('view_request', $request); ?>

<?php if ($gris) :?>
<form method="POST" action="<?php echo $this->buildLink(array('action' => 'cancel', 'param' => "res_id:$res_id")); ?>">

    <table class="list">

        <thead>
            <tr>
                <th></th>
                <th><?php echo _("Tool"); ?></th>
                <th><?php echo _("Reservation ID"); ?></th>
                <th align="center">
                    <?php echo _("Status"); ?>
                    <img alt="<?php echo _("loading"); ?>" style="display:none" id="load_dynamic" src="includes/images/ajax-loader.gif">
                    <a href="#" onclick="return false;">
                        <img alt="<?php echo _("refresh"); ?>" border="0" id="load_static" src="includes/images/ajax-refresh.gif" onClick="refreshStatus(<?php echo $res_id; ?>);">
                    </a>
                </th>
                <th><?php echo _("Initial Date/Time"); ?></th>
                <th><?php echo _("Final Date/Time"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($gris as $i => $g): ?>
                <tr id="line<?php echo $i; ?>">
                    <td>
                        <input type="checkbox" id="cancel<?php echo $i; ?>" disabled name="cancel_checkbox[]" value="<?php echo $g->id; ?>" onClick="disabelCancelButton(this);"/>
                    </td>
                    <td>
                        OSCARS
                    </td>
                    <td>
                        <?php echo $g->id; ?>
                    </td>
                    <td>
                        <label id="status<?php echo $i; ?>"><?php echo $g->status; ?></label>
                        <img alt="<?php echo _("loading"); ?>" style="display:none" id="loading" src="includes/images/ajax-loader.gif"/>
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

    <div style="clear: both" class="controls">
        <input class="back" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');" value="<?php echo _("Back to reservations"); ?>"/>
        <input class="cancel" type="submit" disabled id="cancel_button" style="opacity:0.4" value="<?php echo _("Cancel reservations"); ?>" onClick="return confirm('<?php echo _('Cancel the selected reservations?'); ?>')"/>
    </div>



</form>
<?php else : ?>
<div class="controls">
        <input class="back" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');" value="<?php echo _("Back to reservations"); ?>"/>
</div>
<?php endif; ?>
