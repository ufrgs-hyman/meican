<h1><?php echo _("Add Federation"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "add")); ?>">
    
    <?php $this->addElement('federation_form'); ?>

    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Save'); ?>">
        <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
    </div>
    
</form>