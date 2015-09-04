<?php 

	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	
	use app\modules\circuits\assets\AutomatedTestAsset;
	
	AutomatedTestAsset::register($this);

	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => 'delete',
			'id' => 'test-form',
	]);
?>

<h1><?= Yii::t("circuits", "Automated Tests"); ?></h1>

<button id="add-button"><?= Yii::t("circuits", "Add"); ?></button>
<button id="refresh-button"><?= Yii::t("circuits", "Disable auto refresh"); ?></button>
<button id="deleteButton" style="display: none;"><?= Yii::t("circuits", "Delete"); ?></button>

<?php Pjax::begin([
    'id' => 'test-pjax',
]); ?>
		
<?=
	GridView::widget([
		'options' => [
			'id'=>'test-grid',
			'class' => 'list'],
		'dataProvider' => $data,
		'layout' => "{items}{summary}{pager}",
		'columns' => array(
				array(
						'class'=>CheckboxColumn::className(),
						'name'=>'delete',
						'checkboxOptions'=> function() {
							return [
								'class'=>'deleteCheckbox'
							];
						},
						'headerOptions'=>['style'=>'width: 2%;'],
						'multiple'=>false,
				),
				[
					'format' => 'raw',
					'value' => function ($model){
						return '<a href="#">'.Html::img('@web/images/edit_1.png', ['class' => "edit-button"])."</a>";
					},
					'headerOptions'=>['style'=>'width: 2%;'],
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
						return $model->getSourceDevice()->select(['id', 'name'])->one()->name;
					},
					'contentOptions'=> function ($model){
						return [
							'class' => "src-device",
							'data'=>$model->getSourceDevice()->select(['id', 'name'])->one()->id];
					},
				],
				[
					'header' => '<div style="margin-top:24px;">'.Yii::t("circuits", "Port").'</div>',
					'value' => function($model){
						return $model->getSourcePort()->select(['id','name'])->one()->name;
					},
					'contentOptions'=> function ($model){
						return [
							'class' => 'src-port',
							'data'=>$model->getSourcePort()->select(['id','name'])->one()->id];
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
						return $model->getDestinationDevice()->select(['id', 'name'])->one()->name;
					},
					'contentOptions'=> function ($model){
						return [
							'class'=> 'dst-device',
							'data'=>$model->getDestinationDevice()->select(['id', 'name'])->one()->id];
					},
				],
				[
					'header' => '<div style="margin-top:24px;">'.Yii::t("circuits", "Port").'</div>',
					'value' => function($model){
						return $model->getDestinationPort()->select(['id','name'])->one()->name;
					},
					'contentOptions'=> function ($model){
						return [
							'class' => 'dst-port',
							'data'=>$model->getDestinationPort()->select(['id','name'])->one()->id];
					},
				],
				[
					'header' => '',
					'value' => function($model){
						return ""; 
					},
					'contentOptions'=> function ($model, $key, $index, $column){
						return [
						'class' => 'cron-value',
						'data'=>$model->getCronValue()];
					},
				],
				[
					'attribute' => 'status',
					'value' => function ($model) {
						return $model->getStatus();
					},
					'contentOptions'=> function ($model){
						return [
							'class' => 'src-vlan',
							'data'=>$model->getSourceVlanValue()];
					},
				],
				[
					'attribute' => 'last_run_at',
					'value' => function ($model) {
						$cron = $model->getCron()->one();
						return $cron->last_run_at ? Yii::$app->formatter->asDatetime($cron->last_run_at) : Yii::t("circuits", "Never");
					},
					'contentOptions'=> function ($model){
						return [
							'class' => 'dst-vlan',
							'data'=>$model->getDestinationVlanValue()];
					},
				],
				[
					'label' => Yii::t("circuits", "Last result"),
					'value' => function ($model) {
						return $model->getConnectionStatus();
					}
				],
			),
		]);
?>
		
<?php Pjax::end(); ?>

<?php
	ActiveForm::end();
?>

<div id="test-dialog" title="<?= Yii::t("circuits", "Circuit"); ?>" hidden>
	<?php $form = ActiveForm::begin([
			'method' => 'post',
			'id' => 'test-details-form',
	]); ?>
	<div id="tabs">
	  <ul>
	    <li><a href="#tabs-1">Source</a></li>
	    <li><a href="#tabs-2">Destination</a></li>
	    <li><a href="#tabs-3">Recurrence</a></li>
	  </ul>
	  <div id="tabs-1">
	  	<p>
	    <dl>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Domain"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="src-domain"></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Network"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="src-network" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Device"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="src-device" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Port"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="src-port" name="AutomatedTestForm[src_port]" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "VLAN"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="src-vlan" name="AutomatedTestForm[src_vlan]" disabled></select>
            </dd>
         </dl>
     	</p>
	  </div>
	  <div id="tabs-2">
	  	<p>
	  	<dl>
	  	    <dt>
                <label class="label-description"><?= Yii::t("circuits", "Domain"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="dst-domain"></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Network"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="dst-network" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Device"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="dst-device" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Port"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="dst-port" name="AutomatedTestForm[dst_port]" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "VLAN"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="dst-vlan" name="AutomatedTestForm[dst_vlan]" disabled></select>
            </dd>
        </dl>
    	</p>
	  </div>
	  <div id="tabs-3">
	  	<p><div class="label-description" id="cron-widget"></div></p><input id="cron-value" name="AutomatedTestForm[cron_value]" hidden/></div>
	  </div>
	</div>

	<?php
		ActiveForm::end();
	?>
</div>

<label id="domains" hidden><?= $domains; ?>
</label>