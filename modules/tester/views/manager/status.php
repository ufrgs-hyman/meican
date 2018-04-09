<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;

use meican\base\grid\IcheckboxColumn;
use meican\base\grid\Grid;
use meican\base\grid\GridButtons;

\meican\tester\assets\Status::register($this);

$this->params['header'] = [Yii::t('tester', 'Automated Tests'), [Yii::t('tester', 'Home'), 'Automated Tests']];

?>

<data id="tester-mode" value="<?= $mode ?>"></data>

<div class="box box-default">
    <div class="box-header with-border">
        <?= GridButtons::widget(); ?>
        <div class="box-tools">
            <button id="refresh-button" class="btn btn-default"><?= Yii::t("tester", "Enable auto refresh"); ?></button>
        </div>
    </div>
    <div class="box-body">

    <?php

    $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['delete'],
        'id' => 'delete-test-form',
        'enableClientScript'=>false,
        'enableClientValidation' => false,
    ]);

    Pjax::begin([
        'id' => 'test-pjax',
    ]); 

	echo Grid::widget([
        'id'=>'test-grid',
		'dataProvider' => $data,
		'columns' => array(
				array(
					'class'=>IcheckboxColumn::className(),
					'name'=>'delete',
					'headerOptions'=>['style'=>'width: 2%;'],
					'multiple'=>false,
				),
				[
					'header' => Yii::t("tester", "Source"),
					'value' => function($model){
						return $model->getFirstPath()->one()->port_urn; 
					},
                    'headerOptions'=>['style'=>'width: 20%;'],
				],
                [
                    'header' => Yii::t("tester", "VLAN"),
                    'value' => function($model){
                        return $model->getFirstPath()->one()->vlan; 
                    },
                    'headerOptions'=>['style'=>'width: 5%;'],
                ],
				[
					'header' => Yii::t("tester", "Destination"),
					'value' => function($model){
						return $model->getLastPath()->one()->port_urn; 
					},
                    'headerOptions'=>['style'=>'width: 20%;'],
				],
                [
                    'header' => Yii::t("tester", "VLAN"),
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
						'data'=>$model->getScheduledTask()->freq];
					},
                    'headerOptions'=>['style'=>'width: 1%;'],
				],
				[
					'attribute' => 'last_run_at',
					'value' => function ($model) {
						$cron = $model->getScheduledTask();
						return $cron->last_run_at ? Yii::$app->formatter->asDatetime($cron->last_run_at) : Yii::t("tester", "Never");
					},
                    'headerOptions'=>['style'=>'width: 10%;'],
				],
				[
					'label' => Yii::t("tester", "Last result"),
					'value' => function ($model) {
						return $model->getConnectionStatus();
					},
                    'headerOptions'=>['style'=>'width: 10%;'],
				],
			),
		]);

        Pjax::end(); 

    	ActiveForm::end();

        ?>

    </div>
</div>

<?php Modal::begin([
    'id' => 'test-modal',
    'header' => 'Create test',
    'footer' => '<button type="button" class="confirm-btn btn btn-primary">Confirm</button><button type="button" class="btn btn-default close-btn">Close</button>'
]); ?>

<?php $form = ActiveForm::begin([
    'method' => 'post',
    'id' => 'test-form',
    'layout' => 'horizontal'
]); ?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#src" data-toggle="tab"><?= Yii::t("tester", "Source"); ?></a></li>
        <li><a href="#dst" data-toggle="tab"><?= Yii::t("tester", "Destination"); ?></a></li>
        <li><a href="#rec" data-toggle="tab"><?= Yii::t("tester", "Recurrence"); ?></a></li>
    </ul>
    <div class="tab-content no-padding">
        <div class="tab-pane active" id="src">
            <br>
            <?php 

            $test = new \meican\tester\forms\TestForm;

            echo $form->field($test, 'src_dom')->dropDownList([],['disabled'=>true]); 
            echo $form->field($test, 'src_net')->dropDownList([],['disabled'=>true]); 
            echo $form->field($test, 'src_port')->dropDownList([],['disabled'=>true]); 
            echo $form->field($test, 'src_vlan')->dropDownList([],['disabled'=>true]); 

            ?>
        </div>
        <div class="tab-pane" id="dst">
            <br>
            <?php 

            echo $form->field($test, 'dst_dom')->dropDownList([],['disabled'=>true]); 
            echo $form->field($test, 'dst_net')->dropDownList([],['disabled'=>true]); 
            echo $form->field($test, 'dst_port')->dropDownList([],['disabled'=>true]); 
            echo $form->field($test, 'dst_vlan')->dropDownList([],['disabled'=>true]); 

            ?>
        </div>
        <div class="tab-pane" id="rec">
            <br><div id="cron-widget"></div><br>
            <input id="cron-value" name="TestForm[cron_value]" hidden/>
        </div>
    </div>
</div>

<?php
    ActiveForm::end();
?>

<?php Modal::end(); ?>

<label id="domains" hidden><?= $domains; ?>
</label>
