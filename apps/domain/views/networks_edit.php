<?php $args = $this->passedArgs; ?>

<h1><?php echo _("Edit Network"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "update", "param" => "net_id:".$args->network->net_id)); ?>">

    <?php $this->addElement('network_form', $args); ?>

    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Save'); ?>">
        <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
    </div>

</form>