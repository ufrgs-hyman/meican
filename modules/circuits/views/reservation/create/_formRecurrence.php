<?php 
	/** Importing Classes **/
	use yii\widgets\MaskedInput;
	use yii\jui\DatePicker;
	use yii\helpers\Html;
?>

<label for="start-time" class="label-description"><?= Yii::t("circuits", "Start"); ?>: </label>
<input type="text" size="7" id="start-time" class="hourPicker" name="ReservationForm[start_time]"/>

<?= 

DatePicker::widget([
		'name' => 'ReservationForm[start_date]',
		'dateFormat' => 'dd/mm/yy',
		'options' => array(
			'class' => 'recurrence-datepicker',
			'id' => 'start-date',
			'size' => 9,
			'readonly' => true
		),
]);

?>

<label for="finish-time" class="label-description"><?= Yii::t("circuits", "Finish"); ?>:</label>
<input type="text" size="7" id="finish-time" class="hourPicker" name="ReservationForm[finish_time]"/>

<?=
	/** DATEPICKER **/
	DatePicker::widget([
		'name' => 'ReservationForm[finish_date]',
		'dateFormat' => 'dd/mm/yy',
		'options' => array(
			'class' => 'recurrence-datepicker',
			'id' => 'finish-date',
			'size' => 9,
			'readonly' => true
		),
	]);
?>

&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="ReservationForm[rec_enabled]" id="recurrence_enabled"></input><label for="recurrence_enabled"> <?= Yii::t("circuits", "Repeat..."); ?></label>

<div id="recurrence" style="display:none;">

	<h3><?= Yii::t("circuits", "Recurrence pattern"); ?></h3>
	<div class="recurrence-item">
    	<?=  
    		Html::radioList('ReservationForm[rec_type]', 'D', 
              array('D' => Yii::t("circuits", "Daily"), 'W' => Yii::t("circuits", "Weekly"), 'M' => Yii::t("circuits", "Monthly")));
    	?>
    </div>
    
    <div class="recurrence-reitem">
    <label for="rec-interval"><?= Yii::t("circuits", "Repeats every"); ?> </label>
        <?=
        	Html::dropDownList('ReservationForm[rec_interval]', '1',
				array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'),
        		array(
        			'id' => 'rec-interval'
        		)); 
		?>
		<label id="interval_type"><?= Yii::t("circuits", "day"); ?></label>
    </div>
    
    
    <div id="recurrence-weekdays">
    	<table>
			<tr>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="SU" title="Sunday" id="Sunday"/><label for="Sunday"><?= Yii::t("circuits", "Sun"); ?></label>
				</td>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="MO" title="Monday" id="Monday"/><label for="Monday"><?= Yii::t("circuits", "Mon"); ?></label>
				</td>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="TU" title="Tuesday" id="Tuesday"/><label for="Tuesday"><?= Yii::t("circuits", "Tue"); ?></label>
				</td>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="WE" title="Wednesday"" id="Wednesday"/><label for="Wednesday"><?= Yii::t("circuits", "Wed"); ?></label>
				</td>
			</tr>
			<tr>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="TH" title="Thursday" id="Thursday"/><label for="Thursday"><?= Yii::t("circuits", "Thu"); ?></label>
				</td>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="FR" title="Friday" id="Friday"/><label for="Friday"><?= Yii::t("circuits", "Fri"); ?></label>
				</td>
				<td class="recurrence_table">
					<input type="checkbox" name="ReservationForm[rec_weekdays][]" value="SA" title="Saturday" id="Saturday"/><label for="Saturday"><?= Yii::t("circuits", "Sat"); ?></label>
				</td>                                                    
			</tr>
		</table>
	</div>
	
	<div style="clear: both;"></div>
    
    <h3><?= Yii::t("circuits", "Range of recurrence"); ?></h3>
    
    <div class="recurrence-item">
        <?= Yii::t("circuits", "Starts on"); ?>:
        
        <input id="rec-start-date" class="recurrence-datepicker" readOnly="readonly"/>
		
    </div>
        
    <div class="recurrence-item">
        <span><?= Yii::t("circuits", "Ends:"); ?></span>
        
        <input type="radio" name="ReservationForm[rec_finish_type]" id="rec-finish-occur-limit-radio" checked="yes" value="occur-limit"/>
        <label for="rec-finish-occur-limit-radio">
            <?= Yii::t("circuits", "After of"); ?>
            <?= 
            Html::dropDownList('ReservationForm[rec_finish_occur_limit]', '1',
				array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7'),
        		array(
        			'id' => 'rec-finish-occur-limit'
        		)
			); 
			?>
            <?= Yii::t("circuits", "occurrences"); ?>
        </label>

        <input type="radio" name="ReservationForm[rec_finish_type]" id="rec-finish-date-radio" value="date"/>
        <label for="rec-finish-date-radio">
            <?= Yii::t("circuits", "On"); ?>
            
            <?=
             /** DATEPICKER **/ 
            DatePicker::widget([
	            'name' => 'ReservationForm[rec_finish_date]',
	            'dateFormat' => 'dd/mm/yy',
	            'options' => array(
		            'class' => 'recurrence-datepicker',
		            'id' => 'rec-finish-date',
		            'readonly' => true,
		            'disabled' => true,
		            'size' => 9,
	            ),
            ]);
            
            ?>
        </label>
    </div>
    
    <div style="clear:both;"></div>
    
</div>

<div style="padding-top:1em;">  
	<table id="reservation-summary-table">
		<tr>
			<td>          
		    <p style="display:inline; color:#3a5879; font-weight: bold"><?= Yii::t("circuits", "Summary"); ?></p>:
		    </td>
		    <td>
			    <label id="duration" str="<?= Yii::t("circuits", "Duration"); ?>: "></label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
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
		    </td>
	    </tr>
	</table>
</div>
