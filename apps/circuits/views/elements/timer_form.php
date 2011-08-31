<?php 

$start_date = $argsToElement->start_date;
$finish_date = $argsToElement->finish_date;
$start_time = $argsToElement->start_time;
$finish_time = $argsToElement->finish_time;
$timer = (isset($argsToElement->timer)) ? $argsToElement->timer : NULL;

?>
<table>
    <tr>
        <th class="left large" colspan="2">
            <?php echo _("Start") ?>
        </th>
        <th>&nbsp;</th>
        <th class="left large" colspan="2">
            <?php echo _("Finish") ?>
        </th>
        <th class="left large">
            <?php echo _("Duration") ?>
        </th>
    </tr>

    <tr>
        <td class="left">
            <?php echo _("Date"); ?> <input type="text" name="start_date" size="9" value="<?php echo $start_date; ?>" readonly class="datePicker" id="initialDate"/>
        </td>
        <td class="left">
            <?php echo _("Time"); ?> <input type="text" name="start_time" size="7" value="<?php echo $start_time; ?>" class="hourPicker" id="initialTime"/>
        </td>
        <td class="left">
            <?php echo _("until"); ?>
        </td>
        <td class="left">
            <?php echo _("Date"); ?> <input type="text" name="finish_date" size="9" value="<?php echo $finish_date; ?>" readonly class="datePicker" id="finalDate"/>
        </td>
        <td class="left">
            <?php echo _("Time"); ?> <input type="text" name="finish_time" size="7" value="<?php echo $finish_time; ?>" class="hourPicker" id="finalTime"/>
        </td>
        <td class="left">
            <label id="duration"></label>
        </td>
    </tr>

    <tr>
        <td class="left" colspan="6">
            <input type="checkbox" name="repeat_chkbox" id="repeat_chkbox" onClick="showRecurrenceBox();" />
            <p style="display: inline; vertical-align: middle"> <?php echo _("Repeat..."); ?> <a href="#" id="recurrence-edit" onclick="showRecurrenceBox();"><?php echo _("Edit"); ?></a> </p>
            <?php $this->addElement('timer_recurrence'); ?>
        </td>
    </tr>
    <tr>
        <th style="text-align: left;" colspan="6"></th>
    </tr>
    <tr>
        <td class="left" colspan="6">
            <p style="display:inline; color:#3a5879; font-weight: bold"><?php echo _("Summary"); ?></p>:&nbsp;<label id="recurrence_summary"></label>
            <label id="summary"></label>
            <input type="hidden" id="summary_input" name="summary" value=""/>
        </td>
    </tr>
</table>