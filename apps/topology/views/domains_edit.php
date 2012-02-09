<?php

$domain = $this->passedArgs;
Framework::debug('PASSADO PRA VIEW', $domain);
?>

<h1><?php echo _("Edit domain"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "update", "param" => "dom_id:$domain->dom_id")); ?>">
    <div style="width: 42%">
        <?php $this->addElement('domain_form', $domain); ?>

        <div class="controls">
            <input class="save" type="submit" value="<?php echo _('Update'); ?>">
            <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
        </div>
    </div>
    
</form>