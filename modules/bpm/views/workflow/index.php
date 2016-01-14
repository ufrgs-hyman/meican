<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\bootstrap\Modal;
use meican\base\components\LinkColumn;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\data\ActiveDataProvider;
use meican\bpm\models\BpmWorkflow;

use yii\helpers\ArrayHelper;

use meican\bpm\assets\IndexAsset;
IndexAsset::register($this);

$this->params['header'] = ["Workflows", ['Home', 'Workflows']];

?>

<?= Html::csrfMetaTags() ?>

<div class="box box-default">
    <div class="box-body">               
	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data,
			'filterModel' => $searchModel,
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridDevices',
			'layout' => "{items}{summary}{pager}",
			'columns' => array(
					[
						'format' => 'raw',
						'value' => function ($work){
							return Html::img('@web'.'/images/remove.png', ['title'=> Yii::t("bpm", 'Delete Workflow'), 'onclick' => "deleteWorkflow($work->id)"]);
						},
						'contentOptions'=>['style'=>'cursor: pointer;'],
						'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'class'=> LinkColumn::className(),
						'image'=>'/images/eye.png',
						'title'=> Yii::t("bpm", 'View Workflow'),
						'url' => '/bpm/workflow/viewer',
						'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'format' => 'raw',
						'value' => function ($work){
							if($work->isDisabled()) return Html::img('@web'.'/images/edit_1.png', ['title' => Yii::t("bpm", 'Update Workflow'), 'onclick' => "update($work->id)"]);
							else return Html::img('@web'.'/images/edit_1.png', ['title' => Yii::t("bpm", 'Update Workflow')]);
						},
						'contentOptions' => function ($work){
							return ['style'=>'display: table-cell;', 'disabled'=>!$work->isDisabled(), 'class'=>'btn'];
			        	},
			        	'headerOptions'=>['style'=>'width: 2%;'],
					],
			        [
			        	'class'=> LinkColumn::className(),
			        	'image'=>'/images/copy2.png',
			        	'title'=> Yii::t("bpm", 'Create a copy of Workflow'),
			        	'url' => '/bpm/workflow/copy',
			        	'headerOptions'=>['style'=>'width: 2%;'],
	        		],
			        [
			        	'format' => 'raw',
			        	'value' => function ($work){
			        		if(!$work->isDisabled()) return Html::img('@web'.'/images/desactivate1.png', ['title' => Yii::t("bpm", 'Disable Workflow'), 'onclick' => "disableWorkflow($work->id)"]);
			        		else return Html::img('@web'.'/images/desactivate1.png', ['title' => Yii::t("bpm", 'Disable Workflow')]);
			        	},
			        	'contentOptions' => function ($work){
			        		return ['style'=>'display: table-cell;', 'disabled'=>$work->isDisabled(), 'class'=>'btn'];
			        	},
			        	'headerOptions'=>['style'=>'width: 2%;'],
			        ],
			        [
				        'format' => 'raw',
				        'value' => function ($work){
				        	if($work->isDisabled()) return Html::img('@web'.'/images/accept.png', ['title' => Yii::t("bpm", 'Enable Workflow'), 'onclick' => "enableWorkflow($work->id)"]);
				        	else return Html::img('@web'.'/images/accept.png', ['title' => Yii::t("bpm", 'Enable Workflow')]);
				        },
				        'contentOptions' => function ($work){
				        	return ['style'=>'display: table-cell;', 'disabled'=>!$work->isDisabled(), 'class'=>'btn'];
				        },
				        'headerOptions'=>['style'=>'width: 2%;'],
			        ],
			        [
						'label' => Yii::t("bpm", 'Name'),
						'value' => 'name',
			        	'headerOptions'=>['style'=>'width: 35%;'],
			        ],
					[
						'label' => Yii::t("bpm", 'Domain'),
						'value' => 'domain',
						'filter' => Html::activeDropDownList($searchModel, 'domain',
								ArrayHelper::map(
									BpmWorkflow::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
								['class'=>'form-control','prompt' => Yii::t("bpm", 'any')]
						),
						'headerOptions'=>['style'=>'width: 35%;'],
					],
					[
						'format' => 'raw',
						'label' => 'Status',
						'value' => function ($work){
							if($work->active==1) return Yii::t("bpm", "Enabled");
							else return Yii::t("bpm", "Disabled");
						},
						'filter' => Html::activeDropDownList($searchModel, 'status',
								["enabled" => Yii::t("bpm", "Enabled"), "disabled"=> Yii::t("bpm", "Disabled")],
								['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
						),
						'headerOptions'=>['style'=>'width: 18%;'],
					],
					
				),
		]);
	?>
	</div>
</div>

<?php 

Modal::begin([
    'id' => 'dialog',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default" data-dismiss="modal">'.Yii::t("bpm", "Cancel").'</button><button id="delete-btn" class="btn btn-danger">'.Yii::t("bpm", "Delete").'</button><button id="ok-btn" class="btn btn-primary">Ok</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';

Modal::end(); 

?>