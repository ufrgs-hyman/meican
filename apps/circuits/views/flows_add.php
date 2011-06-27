<?php

$args = $this->passedArgs;
$bandwidthTip = $args->bandwidthTip;
$res_wizard = $args->res_wizard;

?>

<input type="button" class="mapview" value="<?php echo _('Map View')?>" onclick="redir('<?php echo $this->buildLink(array('controller'=> 'map', 'action'=>'show')) ?>');"/>

<h1><?php echo _("Add Flow - Advanced View"); ?></h1>

<div style="clear: both"></div>

<?php if ($res_wizard): ?>
    <h2><?php echo _("Reservation Creation Wizard"); ?></h2>
<?php endif; ?>

<form>

    <table cellspacing="10" cellpadding="3">
        <tr>
            <th>
                <?php echo _("Flow name"); ?>
            </th>
            <td colspan="2" align="left">
                <input type="text" size="70" id="name" title="<?php echo _("Set the flow name"); ?>"/>
            </td>
        </tr>
    </table>

    <?php $this->addElement('source_dest', $args); ?>

    <?php $this->addElement('vlan'); ?>
        
    <table>
        <tr>
            <th>
                <?php echo _("Bandwidth"); ?>
            </th>
            <td colspan="2" align="left">
                <input type="text" id="bandwidth">
                <?php echo $bandwidthTip; ?>
            </td>
        </tr>
    </table>
        
    <div class="controls">
        <input type="button" class="save" value="<?php echo _("OK"); ?>" onclick="saveFlow();">
            
        <?php if ($res_wizard): ?>
            <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'reservations', 'action' => 'page1')); ?>')"/>
        <?php else: ?>
            <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'flows', 'action' => 'show')); ?>')"/>
        <?php endif; ?>
    </div>
                    
</form>