<?php 
	use app\modules\circuits\assets\ViewReservationAsset;
	use app\modules\circuits\assets\GoogleMapsAsset;
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	
	ViewReservationAsset::register($this);
	GoogleMapsAsset::register($this);
?>

<h1 style="clear: none; float: left; z-index: 999999; position: absolute;">
	<data id="res-id" hidden><?= $reservation->id; ?></data>
	<div class="reservation-name"><?= $reservation->name . " (" . ($reservation->getRequesterUser()->one()->login) . ")"?></div>
</h1>

<div id="subtab-points" class="tab_subcontent"">
	<?= $this->render('_formEndpoints', array('label' => Yii::t("circuits", "Source"), 'prefix' => 'src', 
	)); ?>
	<div id="bandwidth_bar">
        <div id="reservation-view-bandwidth-bar">
            <input type="text" id="reservation-view-bandwidth-field" value="<?php echo $reservation->bandwidth . " " . "Mbps" ?>" disabled="disabled" class="ui-widget ui-spinner-input"/>
        </div>
        <div id="bandwidth_bar_inside" style="width: <?= round($reservation->bandwidth * 100 / 1000); ?>%"></div>
    </div>
	<?= $this->render('_formEndpoints', array('label' => Yii::t("circuits", "Destination"), 'prefix' => 'dst',
	)); ?>
</div>

<div id="reservation-tab">
	<div id="reservation-connections">
		<div class="controls">
        	<button id="refresh-button" value="true"><?= Yii::t("circuits", "Disable auto refresh"); ?></button>
            <button disabled="disabled" id="cancel-button"><?= Yii::t("circuits", "Cancel connections"); ?></button>
        </div>
        <?php Pjax::begin([
		    'id' => 'connections-pjax',
		]); ?>
		
        <?=
			GridView::widget([
				'options' => [
						'id'=>'connections-grid',
						'class' => 'list'],
				'dataProvider' => $connections,
				'summary' => false,
				'columns' => array(
						array(
								'class'=>CheckboxColumn::className(),
								'name'=>'selected_connections',
								'checkboxOptions'=> function($model, $key, $index, $column) {
    								return [
										'disabled' => $model->isCancelStatus(),
										'class'=>'connection-checkbox'
									];
								},
								'multiple'=>false,
						),
						'external_id',
						[
							'attribute' => 'start',
							'format' 	=> 'datetime',		
						],
						[
							'attribute' => 'finish',
							'format' 	=> 'datetime',		
						],
						[
							'attribute' => 'status',
							'value' => function($model){
								return $model->getStatus(); 
							},
						],
						[
							'attribute' => 'auth_status',
							'value' => function($model){
								return $model->getAuthStatus();
							 },
							'contentOptions'=> function ($model, $key, $index, $column){
								return ['class' => strtolower($model->auth_status)];
							},
						],
						[
							'attribute' => 'dataplane_status',
							'value' => function($model){
								return $model->getDataStatus(); },
						],
						[
							'header' => '<div style="padding-left:20px;height:16px;width:16px;"><img id="loader-img" src="'.Url::base().'/images/ajax-loader-blue.gif"></div>',
							'value' => function(){
								return ''; },
						],
					),
			]);
		?>
		<?php Pjax::end(); ?>
	</div>
	<div id="reservation-waypoints" hidden><?= $this->render('_formWaypoints'); ?></div>
	<div id="reservation-request"></div>
</div>

<div id="copy-urn-dialog" title="<?= Yii::t("circuits", "Copy the endpoint identifier");?>" hidden>
    <label for="copy-urn-field">URN:</label>
    <br/>
    <input readonly="true" type="text" name="copy-urn-field" id="copy-urn-field" size="50" style="margin-top: 10px;" value="urn"/>
</div>

<div id="cancel-dialog" title="<?= Yii::t("circuits", "Cancel"); ?>" hidden>
	<br>
    <label><?= Yii::t("circuits", "Do you want to cancel this connection(s)?"); ?></label>
    <br/>
</div>