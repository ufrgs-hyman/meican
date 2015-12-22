<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\jui\Dialog;
	use meican\components\LinkColumn;
	use yii\helpers\Html;
	use yii\i18n\Formatter;
	use yii\data\ActiveDataProvider;
	use meican\models\BpmWorkflow;
	
	use yii\helpers\ArrayHelper;
	
	use meican\modules\bpm\assets\IndexAsset;
	IndexAsset::register($this);
?>

<?= Html::csrfMetaTags() ?>


<h1><?= Yii::t("bpm", 'Workflows'); ?></h1>
	                
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
						return Html::img('@web'.'/images/edit_1.png', ['title' => Yii::t("bpm", 'Update Workflow'), 'onclick' => "update($work->id)"]);
					},
					'contentOptions' => function ($work){
						return ['style'=>'cursor: pointer;', "disabled"=> !$work->isDisabled()];
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
		        		return Html::img('@web'.'/images/desactivate1.png', ['title' => Yii::t("bpm", 'Disable Workflow'), 'onclick' => "disableWorkflow($work->id)"]);
		        	},
		        	'contentOptions' => function ($work){
		        		return ['style'=>'cursor: pointer;', "disabled"=> $work->isDisabled()];
		        	},
		        	'headerOptions'=>['style'=>'width: 2%;'],
		        ],
		        [
			        'format' => 'raw',
			        'value' => function ($work){
			        	return Html::img('@web'.'/images/accept.png', ['title' => Yii::t("bpm", 'Enable Workflow'), 'onclick' => "enableWorkflow($work->id)"]);
			        },
			        'contentOptions' => function ($work){
			        	return ['style'=>'cursor: pointer;', "disabled"=> !$work->isDisabled()];
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

<div style="display: none">
<?php Dialog::begin([
		'id' => 'dialog',
    	'clientOptions' => [
        	'modal' => true,
        	'autoOpen' => false,
        	'title' => "Workflows",
    	],
	]);

	echo '<br></br>';
    echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';
    
	Dialog::end(); 
?>
</div>