<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\i18n\Formatter;
	use yii\helpers\BaseHtml as Html;
	use yii\widgets\ActiveForm;
	
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
			'id' => 'gridNetowrks',
			'layout' => "{items}",
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
					'latitude',
					'longitude',
					[
						'format' => 'html',
						'label' => "#".Yii::t("topology", 'Devices'),
						'value' => function($net){
							return Html::a($net->getDevices()->count(), ['/topology/device', 'id' => $net->id]);
						}
					],
					[
						'label' => Yii::t("topology", 'Domain'),
						'value' => function($net){
							return $net->getDomain()->one()->name;
						}
					],
			),
		]);
	?>
	
	<?php
	ActiveForm::end();
?>
