<h2><?php echo _("Step 3 - Defining the Bandwidth"); ?></h2>

<!-- Slider -->
<label for="amount"></label>
<div id="slider"></div>


<div class="controls">
    <input type="button" id="bc3" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"          
           <input type="button" id="bp3" class="back" value="<?php echo _('Previous'); ?>" onClick="previousTab(this);"          
           <input type="button" id="bn3" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);">
</div>            