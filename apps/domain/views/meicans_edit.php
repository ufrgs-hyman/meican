<?php

$federation = $this->passedArgs;

?>

<h1><?php echo _("Edit Federation"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "update", "param" => "fed_id:$federation->meican_id")); ?>">

    <?php $this->addElement('meican_form', $federation); ?>

    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Update'); ?>"/>
        <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
    </div>
    
</form>