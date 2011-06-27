<h2><?php echo _("Step 1 - Defining the reservation name"); ?></h2>

<br>

<table>
    <tr>
        <th>
            <?php echo _("Reservation name"); ?>
        </th>
        <th>
            <input type="text" size="50" value="<?php echo $name; ?>" onchange="changeName(this);">
        </th>
    </tr>
</table>
<br/><br/>
<div class="controls">
    <input type="button" id="bc1" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"          
    <input type="button" id="bn1" class="next" value="<?php echo _('Next'); ?>" onClick="nextTab(this);">
</div>            