<?php 

$start_date = $argsToElement->start_date;
$finish_date = $argsToElement->finish_date;
$start_time = $argsToElement->start_time;
$finish_time = $argsToElement->finish_time;
$timer = (isset($argsToElement->timer)) ? $argsToElement->timer : NULL;

?>
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
            <?php echo _("Date"); ?> <input type="text" name="start_date" size="9" value="<?php echo $start_date; ?>" readonly class="datePicker" id="initialDate"/>
        </td>
        <td>
            <?php echo _("Time"); ?> <input type="text" name="start_time" size="7" value="<?php echo $start_time; ?>" class="hourPicker" id="initialTime"/>
        </td>
        <td>
            <?php echo _("until"); ?>
        </td>
        <td>
            <?php echo _("Date"); ?> <input type="text" name="finish_date" size="9" value="<?php echo $finish_date; ?>" readonly class="datePicker" id="finalDate"/>
        </td>
        <td>
            <?php echo _("Time"); ?> <input type="text" name="finish_time" size="7" value="<?php echo $finish_time; ?>" class="hourPicker" id="finalTime"/>
        </td>
        <td>
            <label id="duration"></label>
        </td>
    </tr>

    <tr>
        <td colspan="6">
            <input type="checkbox" name="repeat_chkbox" id="repeat_chkbox" onClick="showRecurrenceBox();"/>
            <?php echo _("Repeat..."); ?> <a href="#" id="recurrence-edit" onclick="showRecurrenceBox();"><?php echo _("Edit"); ?></a> 
        </td>
    </tr>
    <tr>
        <th colspan="6">
            <?php echo _("Summary"); ?>
        </th>
    </tr>
    <tr>
        <td colspan="6">
            <label id="recurrence_summary"></label>
            <label id="summary"></label>
        </td>
    </tr>
</table>