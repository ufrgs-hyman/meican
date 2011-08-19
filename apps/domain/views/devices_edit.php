<?php $args = $this->passedArgs; ?>

<h1><?php echo _("Edit device"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "update", "param" => "dev_id:".$args->device->dev_id)); ?>" onsubmit="validateDeviceForm();">

    <?php $this->addElement('device_form', $args); ?>

    <div class="controls">
        <input type="submit" class="add" value="<?php echo _('Update'); ?>"/>
        <input type="button" class="cancel" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
    </div>

</form>