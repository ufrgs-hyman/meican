<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	
	use app\components\LinkColumn;
	
	use yii\helpers\Html;
	
	use yii\i18n\Formatter;

	use app\modules\topology\assets\DeviceAsset;
	
	use yii\helpers\ArrayHelper;
	
	use yii\widgets\ActiveForm;
	use yii\data\ActiveDataProvider;
	use app\models\Device;
	
	DeviceAsset::register($this);
?>

<h1><?= Yii::t('topology', 'Devices'); ?></h1>

<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'device-form',	
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	])
?>
	
<?= $this->render('//formButtons'); ?>
	
	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'dataProvider' => $devices,
			'filterModel' => $searchModel,
			'id' => 'gridNetowrks',
			'layout' => "{items}{pager}",
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
			        	'url' => '/topology/device/update',
			        	'headerOptions'=>['style'=>'width: 2%;'],
			        ],
					[
						'label' => Yii::t("topology", 'Name'),
			        	'value' => 'name',
						'headerOptions'=>['style'=>'width: 24%;'],
					],
					[
						'label' => Yii::t("topology", 'Ip'),
			        	'value' => 'ip',
						'headerOptions'=>['style'=>'width: 8%;'],
					],
					[
						'label' => Yii::t("topology", 'Address'),
						'value' => 'address',
						'headerOptions'=>['style'=>'width: 10%;'],
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
						'label' => Yii::t("topology", 'Node'),
						'value' => 'node',
						'headerOptions'=>['style'=>'width: 9%;'],
					],
					[
						'label' => Yii::t("topology", 'Domain'),
						'value' => function($dev){
							return $dev->getDomain()->one()->name;
						},
						'filter' => Html::activeDropDownList($searchModel, 'domain_name',
							ArrayHelper::map(
								$allowedDomains, 'name', 'name'),
							['class'=>'form-control','prompt' => Yii::t("topology", 'any')]	
						),
						'headerOptions'=>['style'=>'width: 23%;'],
					],
					[
						'format' => 'html',
						'label' => Yii::t('topology', '#EndPoints'),
						'value' => function($dev){
							return Html::a($dev->getPorts()->count(), ['/topology/port', 'id' => $dev->domain_id]);
						},
						'headerOptions'=>['style'=>'width: 4%;'],
					],
			),
		]);
	?>
	
	<?php
	ActiveForm::end();
?>