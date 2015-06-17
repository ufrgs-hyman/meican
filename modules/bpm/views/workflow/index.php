<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\jui\Dialog;
	use app\components\LinkColumn;
	use yii\helpers\Html;
	use yii\i18n\Formatter;
	use yii\data\ActiveDataProvider;
	use app\models\BpmWorkflow;
	
	use app\modules\bpm\assets\IndexAsset;
	IndexAsset::register($this);
?>

<?= Html::csrfMetaTags() ?>


<h1><?= Yii::t("bpm", 'Workflows'); ?></h1>
	                
<?=
	GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $workflows,
		'formatter' => new Formatter(['nullDisplay'=>'']),
		'id' => 'gridDevices',
		'layout' => "{items}",
		'columns' => array(
				[
					'format' => 'raw',
					'value' => function ($work){
						return Html::img('@web'.'/images/remove.png', ['title'=> Yii::t("bpm", 'Delete Workflow'), 'onclick' => "deleteWorkflow($work->id)"]);
					},
					'contentOptions'=>['style'=>'width: 15px; cursor: pointer;']
				],
				[
					'class'=> LinkColumn::className(),
					'image'=>'/images/eye.png',
					'title'=> Yii::t("bpm", 'View Workflow'),
					'url' => '/bpm/workflow/viewer',
					'contentOptions'=>['style'=>'width: 15px;'],
				],
				[
					'format' => 'raw',
					'value' => function ($work){
						return Html::img('@web'.'/images/edit_1.png', ['title' => Yii::t("bpm", 'Update Workflow'), 'onclick' => "update($work->id)"]);
					},
					'contentOptions' => function ($work){
						return ['style'=>'width: 15px; cursor: pointer;', "disabled"=> !$work->isDisabled()];
		        	},
				],
		        [
		        	'class'=> LinkColumn::className(),
		        	'image'=>'/images/copy2.png',
		        	'title'=> Yii::t("bpm", 'Create a copy of Workflow'),
		        	'url' => '/bpm/workflow/copy',
		        	'contentOptions'=>['style'=>'width: 15px;'],
        		],
		        [
		        	'format' => 'raw',
		        	'value' => function ($work){
		        		return Html::img('@web'.'/images/desactivate1.png', ['title' => Yii::t("bpm", 'Disable Workflow'), 'onclick' => "disableWorkflow($work->id)"]);
		        	},
		        	'contentOptions' => function ($work){
		        		return ['style'=>'width: 15px; cursor: pointer;', "disabled"=> $work->isDisabled()];
		        	},
		        ],
		        [
	        		'class'=> LinkColumn::className(),
	        		'image'=>'/images/accept.png',
		        	'title'=> Yii::t("bpm", 'Enable Workflow'),
	        		'url' => '/bpm/workflow/active',
	        		'contentOptions' => function ($work){
	        			return ['style'=>'width: 15px;', "disabled"=> !$work->isDisabled()];
	        		},
		        ],
				'name',
				[
					'label' => Yii::t("bpm", 'Domain'),
					'value' => function($work){
						return strtoupper($work->getDomain()->one()->name);
					}
				],
				[
					'format' => 'raw',
					'label' => 'Status',
					'value' => function ($work){
						if($work->active==1) return Yii::t("bpm", "Enabled");
						else return Yii::t("bpm", "Disabled");
					},
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