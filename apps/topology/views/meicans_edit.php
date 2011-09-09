<?php

$meican = $this->passedArgs;

?>

<h1><?php echo _("Edit MEICAN"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "update", "param" => "meican_id:$meican->meican_id")); ?>">

    <div style="width: 40%">
        <?php $this->addElement('meican_form', $meican); ?>

        <div class="controls">
            <input class="save" type="submit" value="<?php echo _('Update'); ?>"/>
            <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
        </div>
    </div>
    
</form>