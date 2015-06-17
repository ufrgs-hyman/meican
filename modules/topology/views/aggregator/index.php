<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	
	use app\components\LinkColumn;
	
	use yii\helpers\Html;
	
	use yii\widgets\ActiveForm;
	
	use app\modules\topology\assets\AggregatorAsset;
	
	AggregatorAsset::register($this);
?>

<h1><?= "Aggregators" ?></h1>
<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'aggregator-form',	
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	])
?>
	
<?= $this->render('//formButtons'); ?>

<?=
	GridView::widget([
		'options' => ['class' => 'list'],
		'layout' => "{items}",
		'dataProvider' => $aggregators,
		'columns' => array(
	    		array(
	    			'class'=>CheckboxColumn::className(),
			        'name'=>'delete',         
			        'checkboxOptions'=>[
			        	'class'=>'deleteCheckbox',
			        ],
			        'multiple'=>false,
			        'contentOptions'=>['style'=>'width: 15px;'],
		        ),
		        array(
		        	'class'=> LinkColumn::className(),
		        	'image'=>'/images/edit_1.png',
		        	'label' => '',
		        	'url' => 'update',
		        	'contentOptions'=>['style'=>'width: 15px;'],
		        ),
		        array(
		        		'class'=> LinkColumn::className(),
		        		'image'=>'/images/arrow_circle_double.png',
		        		'label' => '',
		        		'title'=>Yii::t("topology",'Import the topology of this Aggregator'),
		        		'url' => '/topology/import/index',
		        		'contentOptions'=>['style'=>'width: 15px;'],
		        ),
		        array(
		        		'class'=> LinkColumn::className(),
		        		'image'=>'/images/accept.png',
		        		'label' => '',
		        		'url' => 'set-default',
		        		'title'=> Yii::t("topology",'Set as default provider for the next reservations'),
		        		'contentOptions'=>function($model) {
		        			return ["disabled"=> $model->default ? true : false, 'style'=>'width: 15px;'];		
		        		},
		        ),
				[
		        	'label' => 'NSA ID',
					'value' => function($model) {
						return $model->getProvider()->one()->nsa;
					},
		        ],
				[
		        	'label' => 'Connection Service URL',
					'value' => function($model) {
						return $model->getProvider()->one()->connection_url;
					},
		        ],
		        [
			        'label' => 'Discovery Service URL',
			        'value' => function($model) {
			        	return $model->getProvider()->one()->discovery_url;
			        },
		        ],
			),
	]);
?>

<?php
	ActiveForm::end();
?>
