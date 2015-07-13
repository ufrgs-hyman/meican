<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\i18n\Formatter;
	use yii\helpers\BaseHtml as Html;
	use yii\widgets\ActiveForm;
	
	use yii\helpers\ArrayHelper;
	
	use app\components\LinkColumn;
	use app\modules\topology\assets\NetworkAsset;
	NetworkAsset::register($this);
?>

<h1><?= Yii::t('topology', 'Networks'); ?></h1>
<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'network-form',	
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	])
?>
	
<?= $this->render('//formButtons'); ?>
	
	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'dataProvider' => $networks,
			'filterModel' => $searchModel,
			'id' => 'gridNetowrks',
			'layout' => "{items}{pager}",
			'columns' => array(
		    		array(
		    			'class'=>CheckboxColumn::className(),
				        'name'=>'delete',         
				        'checkboxOptions'=>[
				        	'class'=>'deleteCheckbox',
				        ],
				        'multiple'=>false,
				        'contentOptions'=>['style'=>'width: 15px;']
			        ),
			        array(
			        	'class'=> LinkColumn::className(),
			        	'image'=>'/images/edit_1.png',
			        	'label' => '',
			        	'title'=> Yii::t("topology", 'Update'),
			        	'url' => '/topology/network/update',
			        	'contentOptions'=>['style'=>'width: 15px;']
			        ),
			        'name',
			        'urn',
					'latitude',
					'longitude',
					[
						'label' => Yii::t("topology", 'Domain'),
						'value' => function($net){
							return $net->getDomain()->one()->name;
						},
						'filter' => Html::activeDropDownList($searchModel, 'domain_name',
							ArrayHelper::map(
								$allowedDomains, 'name', 'name'),
							['class'=>'form-control','prompt' => Yii::t("topology", 'any')]		
						),
					],
			),
		]);
	?>
	
	<?php
	ActiveForm::end();
?>
