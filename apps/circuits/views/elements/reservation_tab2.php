<h2><?php echo _("Step 2 - Defining the Endpoints"); ?></h2>

<?php
if ($flows_exist)
    $this->addElement('list_flows', $args);
else
    $this->addElement("empty_db", $args);
?>

<div class="controls">
    <?php if ($args->sel_flow): ?>
        <input id="bn2" class="next" type="button" value="<?php echo _("Next"); ?>" onClick="nextTab(this);">
    <?php else: ?>
        <input id="bn2" class="next" type="button" disabled="disabled" value="<?php echo _("Next"); ?>" >
    <?php endif; ?>
    <input type="button" id="bc2" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"          
    <input type="button" id="bp2" class="back" value="<?php echo _('Previous'); ?>" onClick="previousTab(this);"                    
</div>