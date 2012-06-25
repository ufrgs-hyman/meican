<?php

$start_date = $argsToElement->start_date;
$finish_date = $argsToElement->finish_date;
$start_time = $argsToElement->start_time;
$finish_time = $argsToElement->finish_time;
//$timer = (isset($argsToElement->timer)) ? $argsToElement->timer : NULL;

?>

<label for="initialTime"><?php echo _("Start") ?>: </label>
<input type="text" name="start_time" size="7" value="<?php echo $start_time; ?>" class="hourPicker" id="initialTime"/>
<input type="text" name="start_date" size="9" value="<?php echo $start_date; ?>" readonly class="datePicker" id="initialDate"/>

<label for="finalTime"><?php echo _("Finish") ?>:</label>
<input type="text" name="finish_time" size="7" value="<?php echo $finish_time; ?>" class="hourPicker" id="finalTime"/>
<input type="text" name="finish_date" size="9" value="<?php echo $finish_date; ?>" readonly class="datePicker" id="finalDate"/>
&nbsp;&nbsp;&nbsp;
<label id="duration" str="<?php echo _("Duration") ?>: "></label>
<input type="checkbox" name="repeat_chkbox" id="repeat_chkbox"/>
<label for="repeat_chkbox"> <?php echo _("Repeat..."); ?></label>

<?php $this->addElement('timer_recurrence'); ?>

<div style="padding-top:1em;">            
    <p style="display:inline; color:#3a5879; font-weight: bold"><?php echo _("Summary"); ?></p>:&nbsp;
    
    <!-- label for summary of circuit without recurrence, time when it would be active -->
    <label id="summary"></label>
    
    <!-- set of labels to describe the recurrence summary -->
    <label id="short_desc"></label>
    <label id="Sunday_desc"></label>
    <label id="Monday_desc"></label>
    <label id="Tuesday_desc"></label>
    <label id="Wednesday_desc"></label>
    <label id="Thursday_desc"></label>
    <label id="Friday_desc"></label>
    <label id="Saturday_desc"></label>
    <label id="until_desc"></label>
    
    <!-- input to post the summary description -->
    <input type="hidden" id="summary_input" name="summary" value=""/>
</div>
