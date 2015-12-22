<?php 

	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	
	use meican\modules\circuits\assets\AutomatedTestAsset;
	
	AutomatedTestAsset::register($this);

	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'test-form',
            'enableClientScript'=>false,
            'enableClientValidation' => false,
	]);
?>

<h1><?= Yii::t("circuits", "Automated Tests"); ?></h1>

<button id="add-button"><?= Yii::t("circuits", "Add"); ?></button>
<button id="refresh-button"><?= Yii::t("circuits", "Enable auto refresh"); ?></button>
<button id="deleteButton" style="display: none;"><?= Yii::t("circuits", "Delete"); ?></button>
<data id="at-mode" value="<?= $mode ?>"></data>

<?php Pjax::begin([
    'id' => 'test-pjax',
]); ?>
		
<?=
	GridView::widget([
        'id'=>'test-grid',
		'options' => ['class' => 'list'],
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
					'header' => Yii::t("circuits", "Source"),
					'value' => function($model){
						return $model->getFirstPath()->one()->port_urn; 
					},
                    'headerOptions'=>['style'=>'width: 20%;'],
				],
                [
                    'header' => Yii::t("circuits", "Source VLAN"),
                    'value' => function($model){
                        return $model->getFirstPath()->one()->vlan; 
                    },
                    'headerOptions'=>['style'=>'width: 5%;'],
                ],
				[
					'header' => Yii::t("circuits", "Destination"),
					'value' => function($model){
						return $model->getLastPath()->one()->port_urn; 
					},
                    'headerOptions'=>['style'=>'width: 20%;'],
				],
                [
                    'header' => Yii::t("circuits", "Destination VLAN"),
                    'value' => function($model){
                        return $model->getLastPath()->one()->vlan; 
                    },
                    'headerOptions'=>['style'=>'width: 5%;'],
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
                    'headerOptions'=>['style'=>'width: 1%;'],
				],
				[
					'attribute' => 'status',
					'value' => function ($model) {
						return $model->getStatus();
					},
                    'headerOptions'=>['style'=>'width: 10%;'],
				],
				[
					'attribute' => 'last_run_at',
					'value' => function ($model) {
						$cron = $model->getCron()->one();
						return $cron->last_run_at ? Yii::$app->formatter->asDatetime($cron->last_run_at) : Yii::t("circuits", "Never");
					},
                    'headerOptions'=>['style'=>'width: 10%;'],
				],
				[
					'label' => Yii::t("circuits", "Last result"),
					'value' => function ($model) {
						return $model->getConnectionStatus();
					},
                    'headerOptions'=>['style'=>'width: 10%;'],
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
            'enableClientScript'=>false,
            'enableClientValidation' => false,
    ]); ?>
    <div id="tabs">
      <ul>
        <li><a href="#tabs-0"><?= Yii::t("circuits", "Source"); ?></a></li>
        <li><a href="#tabs-1"><?= Yii::t("circuits", "Destination"); ?></a></li>
        <li><a href="#tabs-2"><?= Yii::t("circuits", "Recurrence"); ?></a></li>
      </ul>
      <div id="tabs-0">
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
      <div id="tabs-1">
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
      <div id="tabs-2">
        <p><div class="label-description" id="cron-widget"></div></p>
        <input id="cron-value" name="AutomatedTestForm[cron_value]" hidden/>
      </div>
    </div>

    <?php
        ActiveForm::end();
    ?>

</div>

<label id="domains" hidden><?= $domains; ?>
</label>