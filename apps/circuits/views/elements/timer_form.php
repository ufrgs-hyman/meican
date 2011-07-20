<table>
    <tr>
        <th colspan="2">
            <?php echo _("Start") ?>
        </th>
        <th></th>
        <th colspan="2">
            <?php echo _("Finish") ?>
        </th>
        <th>
            <?php echo _("Duration") ?>
        </th>
    </tr>

    <tr>
        <td>
            <?php echo _("Date"); ?> <input type="text" size="9" value="<?php echo $start_date; ?>" readonly class="datePicker" id="initialDate"/>
        </td>
        <td>
            <?php echo _("Time"); ?> <input type="text" size="7" value="<?php echo $start_time; ?>" class="hourPicker" id="initialTime"/>
        </td>
        <td>
            <?php echo _("until"); ?>
        </td>
        <td>
            <?php echo _("Date"); ?> <input type="text" size="9" value="<?php echo $finish_date; ?>" readonly class="datePicker" id="finalDate"/>
        </td>
        <td>
            <?php echo _("Time"); ?> <input type="text" size="7" value="<?php echo $finish_time; ?>" class="hourPicker" id="finalTime"/>
        </td>
    </tr>

    <tr>
        <td colspan="5">
            <input type="checkbox" id="repeat_chkbox" onClick="showRecurrenceBox();"/>
            <?php echo _("Repeat..."); ?> <a href="#" id="recurrence-edit"><?php echo _("Edit"); ?></a> 
        </td>        
    </tr>
</table>
<div align="center">
    <label id="short_summary"></label>
</div>