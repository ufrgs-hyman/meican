<?php $timers = $this->passedArgs; ?>

<h1><?php echo _("Timers"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <?php $this->addElement('list_timers', $timers); ?>

    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected timers will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>