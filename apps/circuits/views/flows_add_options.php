<?php

$res_wizard = $this->passedArgs;

?>

<h1><?php echo _("Add Flow"); ?></h1>

<?php if ($res_wizard): ?>
    <h2><?php echo _("Reservation Creation Wizard"); ?></h2>
<?php endif; ?>

<h3><?php echo _("Choose view type"); ?>:</h3>

<div class="controls">
    <input type="button" class="mapview" value="<?php echo _('Map View') ?>" onclick="redir('<?php echo $this->buildLink(array('controller' => 'map', 'action' => 'show')) ?>');"/>
    <input type="button" class="advancedview" value="<?php echo _('Advanced View') ?> " onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')) ?>');"/>

    <?php if ($res_wizard): ?>
        <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'reservations', 'action' => 'page1')); ?>')"/>
    <?php else: ?>
        <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'flows', 'action' => 'show')); ?>')"/>
    <?php endif; ?>

</div>