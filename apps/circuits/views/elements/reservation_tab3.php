<h2><?php echo _("Step 3 - Defining the Bandwidth"); ?></h2>

<!-- Slider -->
<label style="left:25px" for="amount"></label>
<input type="text" id="amount" style="left:25px; border:0; color:#0; font-weight:bold;" size="100"/>
<div id="slider" style="left: 25px; width: 40%" ></div>


<div class="controls">
    <input type="button" id="bc3" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"          
           <input type="button" id="bp3" class="back" value="<?php echo _('Previous'); ?>" onClick="previousTab(this);"          
           <input type="button" id="bn3" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);">
</div>            