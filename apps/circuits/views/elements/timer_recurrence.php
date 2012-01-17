<?php

$freq_types = array();
$freq = new stdClass();
$freq->value = "DAILY";
$freq->descr = _("Everyday");
$freq_types[] = $freq;

$freq = new stdClass();
$freq->value = "WEEKLY";
$freq->descr = _("Weekly");
$freq_types[] = $freq;

$freq = new stdClass();
$freq->value = "MONTHLY";
$freq->descr = _("Monthly");
$freq_types[] = $freq;

?>

<div id="recurrence" style="display:none;">


    <h2><?php echo _("Recurrence pattern"); ?></h2>

    <div class="recurrence-item">
        <?php
        if (!empty($timer)) {
            foreach ($freq_types as $f) {
                if ($timer->freq == $f->value)
                    echo "<div><input type=\"radio\" name=\"freq\" onchange=\"setFreq();\" value=\"$f->value\" id=\"Freq$f->value\" checked/><label for=\"Freq$f->value\">$f->descr</label></div>";
                else
                    echo "<div><input type=\"radio\" name=\"freq\" onchange=\"setFreq();\" value=\"$f->value\" id=\"Freq$f->value\"/><label for=\"Freq$f->value\">$f->descr</label></div>";
            }
        } else
            foreach ($freq_types as $f)
                echo "<div><input type=\"radio\" name=\"freq\" onchange=\"setFreq();\" value=\"$f->value\" id=\"Freq$f->value\"/><label for=\"Freq$f->value\">$f->descr</label></div>";
        ?> 
    </div>

    <div class="recurrence-item">
        <label for="interval"><?php echo _("Repeats every "); ?></label>
        <select id="interval" name="interval" onchange="setFreq()">
            <?php
            if (!empty($timer)) {
                for ($i = 1; $i < 8; $i++)
                    if ($timer->interval == $i)
                        echo "<option selected='true' value='$i'>$i</option>";
                    else
                        echo "<option value='$i'>$i</option>";
            } else
                for ($i = 1; $i < 8; $i++)
                    echo "<option value='$i'>$i</option>";
            ?>
        </select>
        <label id="interval_type"></label>

        <div id="weekdays" style="display: none">
            <table cellspacing="0" cellpadding="0" style="width: 100%" class="withoutBorder">
                <tr>
                    <td class="recurrence_table">
                        <input type="checkbox" name="sun_chkbox" value="SU" title="<?php echo _("Sunday"); ?>" id="Sunday"/><label for="Sunday"><?php echo _("Sun"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input type="checkbox" name="mon_chkbox" value="MO" title="<?php echo _("Monday"); ?>" id="Monday"/><label for="Monday"><?php echo _("Mon"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input type="checkbox" name="tue_chkbox" value="TU" title="<?php echo _("Tuesday"); ?>" id="Tuesday"/><label for="Tuesday"><?php echo _("Tue"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input type="checkbox" name="wed_chkbox" value="WE" title="<?php echo _("Wednesday"); ?>" id="Wednesday"/><label for="Wednesday"><?php echo _("Wed"); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="recurrence_table">
                        <input type="checkbox" name="thu_chkbox" value="TH" title="<?php echo _("Thursday"); ?>" id="Thursday"/><label for="Thursday"><?php echo _("Thu"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input type="checkbox" name="fri_chkbox" value="FR" title="<?php echo _("Friday"); ?>" id="Friday"/><label for="Friday"><?php echo _("Fri"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input type="checkbox" name="sat_chkbox" value="SA" title="<?php echo _("Saturday"); ?>" id="Saturday"/><label for="Saturday"><?php echo _("Sat"); ?></label>
                    </td>                                                    
                </tr>
            </table>
        </div>      

    </div>   
    <div style="clear: both;"></div>
    <h2><?php echo _("Range of recurrence"); ?></h2>
    <div class="recurrence-item">

        <?php echo _("Starts on"); ?>:
        <input type="text" size="9" id="initialRecurrence" value="<?php echo!empty($start_date) ? $start_date : null; ?>">
    </div>
    <div class="recurrence-item">




        <span><?php echo _("Ends:"); ?></span>
        <input type="radio" name="until" id="recur_radio" checked="yes" value="NROCCURR"/><label for="recur_radio">
            <?php echo _("After"); ?>
            <!-- input type="text" size="4" id="nr_occurr" value="5" onchange="changeUntilType(recur_radio)" onblur="changeUntilType(recur_radio)" -->
            <select id="nr_occurr" name="count">
                <?php
                if (!empty($timer) && $timer->count) {
                    for ($i = 1; $i < 30; $i++)
                        if ($timer->count == $i)
                            echo "<option selected='true' value='$i'>$i</option>";
                        else
                            echo "<option value='$i'>$i</option>";
                } else
                    for ($i = 1; $i < 8; $i++)
                        echo "<option value='$i'>$i</option>";
                ?>
            </select>
            <?php echo _("occurrences"); ?></label>

        <input type="radio" name="until" id="date_radio" value="DATE" onchange="setUntilType();"/><label for="date_radio">
            <?php echo _("On"); ?>
            <input type="text" name="until_date" size="9" readonly disabled class="datePicker" id="untilDate" value="<?php
            if (!empty($timer) && $timer->until)
                echo $timer->until; else
                echo!empty($finish_date) ? $finish_date : null;
            ?>"/></label>   
    </div>         
    <div style="clear:both;"></div>               


</div>
