<?php

$start_date = $argsToElement->start_date;
$finish_date = $argsToElement->finish_date;
$start_time = $argsToElement->start_time;
$finish_time = $argsToElement->finish_time;
$timer = (isset($argsToElement->timer)) ? $argsToElement->timer : NULL;

$freq_types = array();
unset($freq);
$freq->value = "DAILY";
$freq->descr = _("Everyday");
$freq_types[] = $freq;

unset($freq);
$freq->value = "WEEKLY";
$freq->descr = _("Weekly");
$freq_types[] = $freq;

unset($freq);
$freq->value = "MONTHLY";
$freq->descr = _("Monthly");
$freq_types[] = $freq;

?>

<div id="recurrence">
    <table cellspacing="0" cellpadding="0" style="width:100%">
        <tr style="width:100%">
            <th style="width:100%">
                <?php echo _("Reservation Time"); ?>
            </th>
        </tr>
        <tr>
            <td>                            
                <table cellspacing="0" cellpadding="0" style="width:100%">
                    <tr style="width:100%">
                        <td>
                            <?php echo _("Start") ;?>:
                        </td>
                        <td>
                            <?php echo _("End"); ?>:
                        </td>
                        <td>
                            <?php echo _("Duration"); ?>:
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="width:100%">
            <th>
                <?php echo _("Recurrence Pattern"); ?>
            </th>
        </tr>
        <tr>
            <td>
                <table cellspacing="0" cellpadding="0" style="width: 100%">
                    <tr>
                        <td style="width:30%">
                            <table cellspacing="0" cellpadding="0" border="1" rules="cols" frame="rhs" style="width: 100%">
                                <tr>
                                    <td>
                                        <?php
                                            if ($timer) {
                                                foreach ($freq_types as $f) {
                                                    if ($timer->freq == $f->value)
                                                        echo "<input type='radio' name='freq' onchange='setFreq();' value='$f->value' checked> $f->descr <br>";    
                                                    else
                                                        echo "<input type='radio' name='freq' onchange='setFreq();' value='$f->value'> $f->descr <br>";    
                                                }
                                            } else
                                            foreach ($freq_types as $f)
                                                echo "<input type='radio' name='freq' onchange='setFreq();' value='$f->value'> $f->descr <br>";
                                        ?>                
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table cellspacing="0" cellpadding="0" style="width: 100%">
                                <tr>
                                    <td>
                                        <table cellspacing="0" cellpadding="0" style="width: 100%">
                                            <tr>
                                                <td>
                                                    <?php echo _("Repeats every "); ?>                                                    
                                                    <select id="interval" onchange="setFreq()">
                                                    <?php
                                                        if ($timer) {
                                                            for ($i=1; $i < 8; $i++)
                                                                if ($timer->interval == $i)
                                                                    echo "<option selected='true' value='$i'>$i</option>";
                                                                else
                                                                    echo "<option value='$i'>$i</option>";
                                                        } else
                                                            for ($i=1; $i < 8; $i++)
                                                                echo "<option value='$i'>$i</option>";
                                                    ?>
                                                    </select>
                                                    <label id="interval_type"></label>                                                    
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="weekdays" style="display: none">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" value="SU" title="<?php echo _("Sunday"); ?>" id="Sunday" onclick="checkWeekDay(this.id);"><?php echo _("Sun"); ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="MO" title="<?php echo _("Monday"); ?>" id="Monday" onclick="checkWeekDay(this.id);"><?php echo _("Mon"); ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="TU" title="<?php echo _("Tuesday"); ?>" id="Tuesday" onclick="checkWeekDay(this.id);"><?php echo _("Tue"); ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="WE" title="<?php echo _("Wednesday"); ?>" id="Wednesday" onclick="checkWeekDay(this.id);"><?php echo _("Wed"); ?>
                                                    </td>                                                    
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" value="TH" title="<?php echo _("Thursday"); ?>" id="Thursday" onclick="checkWeekDay(this.id);"><?php echo _("Thu"); ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="FR" title="<?php echo _("Friday"); ?>" id="Friday" onclick="checkWeekDay(this.id);"><?php echo _("Fri"); ?>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="SA" title="<?php echo _("Saturday"); ?>" id="Saturday" onclick="checkWeekDay(this.id);"><?php echo _("Sat"); ?>
                                                    </td>                                                    
                                                </tr>
                                            </table>
                                        </div>                                          
                                    </td>                                    
                                </tr>
                            </table>                                                             
                        </td>                            
                    </tr>
                </table>
            </td>
        </tr>

        <tr style="width:100%">
            <th>
                <?php echo _("Range of Recurrence"); ?>
            </th>
        </tr>        
        <tr>
            <td>
                <table cellspacing="0" cellpadding="0" style="width: 100%">
                    <tr>
                        <td>
                            <?php echo _("Starts on"); ?>
                            <input type="text" size="9" id="initialRecurrence" value="<?php echo $start_date; ?>">
                        </td>
                        <td>
                            <table cellspacing="0" cellpadding="0" style="width: 100%">
                                <tr>
                                    <td>
                                        <?php echo _("Ends:"); ?>
                                    </td>
                                    <td>
                                        <input type="radio" name="until" id="recur_radio" checked="yes" value="NROCCURR" onchange="setUntilType(); ">
                                        <?php echo _("After"); ?>
                                        <!-- input type="text" size="4" id="nr_occurr" value="5" onchange="changeUntilType(recur_radio)" onblur="changeUntilType(recur_radio)" -->
                                        <select id="nr_occurr" onchange="setUntilType()">
                                        <?php
                                            if ($timer && $timer->count) {
                                                for ($i=1; $i < 30; $i++)
                                                    if ($timer->count == $i)
                                                        echo "<option selected='true' value='$i'>$i</option>";
                                                    else
                                                        echo "<option value='$i'>$i</option>";
                                            } else
                                                for ($i=1; $i < 8; $i++)
                                                    echo "<option value='$i'>$i</option>";
                                        ?>
                                        </select>
                                        <?php echo _("occurrences"); ?>
                                            
                                        <br>
                                        
                                        <input type="radio" name="until" id="date_radio" value="DATE" onchange="setUntilType();">
                                        <?php echo _("On"); ?>
                                        <input type="text" size="9" readonly disabled class="datePicker" id="untilDate" value="<?php if ($timer && $timer->until) echo $timer->until; else echo $finish_date; ?>" onchange="setUntilType()">
                                        <input type="hidden" id="untilTime" value="23:59">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table> 
            </td>
        </tr>
        <tr>
            <th>
                <?php echo _("Summary"); ?>
            </th>
        </tr>
        <tr>            
            <td>
                <table cellspacing="0" cellpadding="0" style="width: 100%">
                    <tr>
                        <td>
                            <label id="short_desc"></label>
                            <label id="Sunday_desc"></label>
                            <label id="Monday_desc"></label>
                            <label id="Tuesday_desc"></label>
                            <label id="Wednesday_desc"></label>
                            <label id="Thursday_desc"></label>
                            <label id="Friday_desc"></label>
                            <label id="Saturday_desc"></label>
                            <label id="until_desc"></label>                            
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <div id="recurrence-flash">
        <label id="recurrence-warning"></label>
    </div>
    <div id="recurrence-footer">
        <input id="recurrence_cancel" type="button" class="cancel" value="<?php echo _("Cancel")?>" onclick="cancelRecurrence()"/>
        <input id="recurrence_ok" type="button" class="ok" value="<?php echo _("Save Recurrence")?>" onclick="saveRecurrence()"/>
    </div>    
    
</div>