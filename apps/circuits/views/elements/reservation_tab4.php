<h2><?php echo _("Step 4 - Defining the timer"); ?></h2>

<br>

<?php
if ($timers_exist)
    $this->addElement('list_timers', $args);
else
    $this->addElement("empty_db", $args);
?>

<br/><br/>

<div class="controls">

    <?php //if ($args->sel_timer): ?>
        <input type="button" id="bn4" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);"
    <?php //else: ?>
    <!--    <input class="next" type="button" id="bn4" disabled="disabled" value="<?php echo _("Next"); ?>"/> -->
    <?php //endif; ?>
    <input type="button" id="bc4" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"          
    <input type="button" id="bp4" class="back" value="<?php echo _('Previous'); ?>" onClick="previousTab(this);"                  




</div>
