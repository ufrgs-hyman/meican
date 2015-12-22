<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\i18n\Formatter;
	use yii\helpers\BaseHtml as Html;
	use yii\widgets\ActiveForm;
	
	use yii\helpers\ArrayHelper;
	
	use meican\components\LinkColumn;
	use meican\modules\topology\assets\NetworkAsset;
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
			'layout' => "{items}{summary}{pager}",
			'columns' => array(
		    		[
		    			'class'=>CheckboxColumn::className(),
				        'name'=>'delete',         
				        'checkboxOptions'=>[
				        	'class'=>'deleteCheckbox',
				        ],
				        'multiple'=>false,
				        'headerOptions'=>['style'=>'width: 2%;'],
			        ],
			        [
			        	'class'=> LinkColumn::className(),
			        	'image'=>'/images/edit_1.png',
			        	'label' => '',
			        	'title'=> Yii::t("topology", 'Update'),
			        	'url' => '/topology/network/update',
			        	'headerOptions'=>['style'=>'width: 2%;'],
			        ],
					[
						'label' => Yii::t("topology", 'Name'),
			        	'value' => 'name',
						'headerOptions'=>['style'=>'width: 25%;'],
					],
					[
						'label' => Yii::t("topology", 'Urn'),
			        	'value' => 'urn',
						'headerOptions'=>['style'=>'width: 30%;'],
					],
					[
						'label' => Yii::t("topology", 'Latitude'),
						'value' => 'latitude',
						'headerOptions'=>['style'=>'width: 8%;'],
					],
					[
						'label' => Yii::t("topology", 'Longitude'),
						'value' => 'longitude',
						'headerOptions'=>['style'=>'width: 8%;'],
					],
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
						'headerOptions'=>['style'=>'width: 25%;'],
					],
			),
		]);
	?>
	
	<?php
	ActiveForm::end();
?>
