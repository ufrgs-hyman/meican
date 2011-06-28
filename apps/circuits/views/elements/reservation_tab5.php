<h2><?php echo _("Step 5 - Confirmation"); ?></h2>

<br>

<table>
    <tr>
        <th>
            <?php echo _("Reservation name"); ?>
        </th>
        <td>
            <input type="text" name="res_name" size="50" value="<?php echo $name; ?>" onchange="changeName(this);">
        </td>
    </tr>
</table>

<h2><?php echo _('Flow'); ?></h2>
<?php $this->addElement('view_flow', $flow); ?>

<h2><?php echo _('Timer'); ?></h2>
<?php $this->addElement('view_timer', $timer); ?>


<div class="controls">
    <input type="button" id="bc5" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"
    <input type="button" id="bp5" class="back" value="<?php echo _('Previous'); ?>" onClick="previousTab(this);"
    <input type="submit" id="bn5" class="ok" value="<?php echo _('Finished'); ?>">
</div>