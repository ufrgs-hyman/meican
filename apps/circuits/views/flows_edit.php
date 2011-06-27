<?php

$args = $this->passedArgs;
$domains = $args->domains;
$bandwidthTip = $args->bandwidthTip;
$flow = $args->flow;
$res_wizard = $args->res_wizard;

?>

<h1><?php echo _("Edit Flow"); ?></h1>

<?php if ($res_wizard): ?>
    <h2><?php echo _("Reservation Creation Wizard"); ?></h2>
<?php endif; ?>

<form>

    <table>
        <tr>
            <th>
                <?php echo _("Flow name"); ?>
            </th>
            <td colspan="2">
                <input type="text" size="70" id="name" value="<?php echo $flow->name; ?>">
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
            <td colspan="2">
                <input type="text" id="bandwidth" value="<?php echo $flow->bandwidth; ?>">
                <?php echo $bandwidthTip; ?>
            </td>
        </tr>
    </table>
       
    <div class="controls">
        <input class="ok" type="button" value="<?php echo _("OK"); ?>" onclick="saveFlow(<?php echo $flow->id; ?>);">
            
        <?php if ($res_wizard): ?>
            <input class="cancel" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'reservations', 'action' => 'page1')); ?>')">
        <?php else: ?>
            <input class="cancel" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>')">
        <?php endif; ?>
    </div>

</form>