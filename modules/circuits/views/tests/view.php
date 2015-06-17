<?php 

	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	use yii\helpers\Html;
	
	use app\modules\circuits\assets\AutomatedTestAsset;
	
	AutomatedTestAsset::register($this);
?>

<h1><?= Yii::t("circuits", "Automated Tests"); ?></h1>

<button id="add-button"><?= Yii::t("circuits", "Add"); ?></button>
<button id="refresh-button"><?= Yii::t("circuits", "Disable auto refresh"); ?></button>
<button id="deleteButton" style="display: none;"><?= Yii::t("circuits", "Delete"); ?></button>

<form id="automated-test-form" method="POST" action="<?= Url::to(['delete']); ?>">

<?php Pjax::begin([
		    'id' => 'test-pjax',
]); ?>
		
<?=
			GridView::widget([
				'options' => [
						'id'=>'test-grid',
						'class' => 'list'],
				'dataProvider' => $data,
				'summary' => false,
				'columns' => array(
						array(
								'class'=>CheckboxColumn::className(),
								'name'=>'delete',
								'checkboxOptions'=> function() {
    								return [
										'class'=>'deleteCheckbox'
									];
								},
								'multiple'=>false,
						),
						[
							'format' => 'raw',
							'value' => function ($model){
								return '<a href="#">'.Html::img('@web/images/edit_1.png', ['class' => "edit-button"])."</a>";
							},
						],
						[
							'attribute' => 'status',
							'value' => function ($model) {
								return $model->getStatus();
							}
						],
						[
							'label' => Yii::t("circuits", "Last result"),
							'value' => function ($model) {
								return $model->getConnectionStatus();
							}
						],
						[
							'attribute' => 'last_execution',
							'value' => function ($model) {
								return $model->last_execution ? Yii::$app->formatter->asDatetime($model->last_execution) : Yii::t("circuits", "Never");
							}
						],
						[
							'header' => '<div style="margin-top:24px;">'.Yii::t("circuits", "Domain").'</div>',
							'value' => function($model){
								return $model->getSourceDomain()->one()->name; 
							},
							'contentOptions'=> function ($model){
								return [
									'class'=> "src-domain",
									'data'=>$model->getSourceDomain()->one()->id];
							},
						],
						[
						    'header' => Yii::t("circuits", "Source").'<div style="margin-top:10px;">'.Yii::t("circuits", "Device").'</div>',
							'value' => function($model){
								return $model->getSourceDevice()->one()->name; 
							},
							'contentOptions'=> function ($model){
								return [
									'class' => "src-device",
									'data'=>$model->getSourceDevice()->one()->id];
							},
						],
						[
							'header' => '<div style="margin-top:24px;">'.Yii::t("circuits", "Port").'</div>',
							'value' => function($model){
								return $model->getSourceUrn()->one()->port; 
							},
							'contentOptions'=> function ($model){
								return [
									'class' => 'src-port',
									'data'=>$model->getSourceUrn()->one()->id];
							},
						],
						[
							'header' => '<div style="margin-top:24px;">'.Yii::t("circuits", "Domain").'</div>',
							'value' => function($model){
								return $model->getDestinationDomain()->one()->name; 
							},
							'contentOptions'=> function ($model){
								return [
									'class' => 'dst-domain',
									'data'=>$model->getDestinationDomain()->one()->id];
							},
						],
						[
							'header' => Yii::t("circuits", "Destination").'<div style="margin-top:10px;">'.Yii::t("circuits", "Device").'</div>',
							'value' => function($model){
								return $model->getDestinationDevice()->one()->name; 
							},
							'contentOptions'=> function ($model){
								return [
									'class'=> 'dst-device',
									'data'=>$model->getDestinationDevice()->one()->id];
							},
						],
						[
							'header' => '<div style="margin-top:24px;">'.Yii::t("circuits", "Port").'</div>',
							'value' => function($model){
								return $model->getDestinationUrn()->one()->port; 
							},
							'contentOptions'=> function ($model){
								return [
									'class' => 'dst-port',
									'data'=>$model->getDestinationUrn()->one()->id];
							},
						],
						[
							'label' => Yii::t("circuits", "Provider"),
							'value' => function($model){
								$prov = $model->getReservation()->one()->getProvider()->one();
								return explode(":", $prov->nsa)[3]." - ".ucfirst(strtolower($prov->type == "DUMMY" ? "AGGREGATOR" : $prov->type)); 
							},
							'contentOptions'=> function ($model, $key, $index, $column){
								return [
									'class' => 'provider',
									'data'=>$model->getReservation()->one()->getProvider()->one()->id];
							},
						],
						[
							'attribute' => "frequency_type",
							'value' => function($model){
								return $model->getFrequencyType(); 
							},
							'contentOptions'=> function ($model, $key, $index, $column){
								return [
									'class' => 'freq-type',
									'data'=>$model->frequency_type];
							},	
						],
						[
							'header' => '<div style="padding-right:5px;width:2px;"><img id="loader-img" src="'.Url::base().'/images/ajax-loader-blue.gif"></div>',
							'value' => function($model){
								return ""; 
							},
							'contentOptions'=> function ($model, $key, $index, $column){
								return [
								'class' => 'freq-value',
								'data'=>$model->crontab_frequency];
							},
						],
						[
							'label' => '',
							'value' => function($model){
								return "";
							},
						],
					),
			]);
?>
		
<?php Pjax::end(); ?>

<div class="frequency_form" id="daily-form" title="<?= Yii::t("circuits", "Daily Frequency"); ?>" style="display:none">
		<div id="daily_form_info">
			<span><?= Yii::t("circuits", "Hour"); ?></span>
			<input tabindex="-1" type="text" style="float:left" name="start_time" size="7" value="<?= $start_time; ?>" class="hourPicker" id="dailyTime"/><br>
		</div>
	</div>
	
	<div class="frequency_form" id="weekly-form" title="<?= Yii::t("circuits", "Weekly Frequency"); ?>" style="display:none">
		<div id="daily_form_info">
			<span><?= Yii::t("circuits", "Hour"); ?></span>
			<input tabindex="-1" type="text" style="float:left" name="start_time" size="7" value="<?=  $start_time; ?>" class="hourPicker" id="weeklyTime"/><br><br>
			<span><?= Yii::t("circuits", "Weekday"); ?></span><br><br>
			<table>
                <tr>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="sun_chkbox" value="7" title="<?= "Sunday"; ?>" id="Sunday"/><label for="Sunday"><?= Yii::t("circuits", "Sun"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="mon_chkbox" value="1" title="<?= "Monday"; ?>" id="Monday"/><label for="Monday"><?= Yii::t("circuits", "Mon"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="tue_chkbox" value="2" title="<?= "Tuesday"; ?>" id="Tuesday"/><label for="Tuesday"><?= Yii::t("circuits", "Tue"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="wed_chkbox" value="3" title="<?= "Wednesday"; ?>" id="Wednesday"/><label for="Wednesday"><?= Yii::t("circuits", "Wed"); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="thu_chkbox" value="4" title="<?= "Thursday"; ?>" id="Thursday"/><label for="Thursday"><?= Yii::t("circuits", "Thu"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="fri_chkbox" value="5" title="<?= "Friday"; ?>" id="Friday"/><label for="Friday"><?= Yii::t("circuits", "Fri"); ?></label>
                    </td>
                    <td class="recurrence_table">
                        <input class="weekDays" type="checkbox" name="sat_chkbox" value="6" title="<?= "Saturday"; ?>" id="Saturday"/><label for="Saturday"><?= Yii::t("circuits", "Sat"); ?></label>
                    </td>                                                    
                </tr>
            </table>
		</div>
	</div>
	
	<div class="frequency_form" id="monthly-form" title="<?= Yii::t("circuits", "Monthly Frequency"); ?>" style="display:none">
		<div id="daily_form_info">
			<span><?= Yii::t("circuits", "Hour"); ?></span>
			<input tabindex="-1" type="text" style="float:left" name="start_time" size="7" value="<?=  $start_time; ?>" class="hourPicker" id="monthlyTime"/><br><br>
			<span><?= Yii::t("circuits", "Day"); ?></span>
			<select id="month-day-freq-select">
				<?php 
					for ($day = 1; $day <= 31; $day++) {
						echo '<option value="'.$day.'">'.$day.'</option>';
					}
				?>
			</select>
		</div>
	</div>
	</form>

<label id="daily-label" hidden><?= Yii::t("circuits", "Daily"); ?></label>
	<label id="weekly-label" hidden><?= Yii::t("circuits", "Weekly"); ?></label>
	<label id="monthly-label" hidden><?= Yii::t("circuits", "Monthly"); ?></label>
	<label id="domains" hidden><?= $domains; ?>
	</label>