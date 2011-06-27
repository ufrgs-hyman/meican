<?php $flows = $this->passedArgs; ?>

<h1><?php echo _("Flows"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <?php $this->addElement('list_flows', $flows); ?>

    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected flows will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>