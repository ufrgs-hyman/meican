<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use app\components\LinkColumn;
	use yii\helpers\Html;
	use yii\i18n\Formatter;
	use yii\widgets\ActiveForm;
	
	use app\modules\topology\assets\DomainAsset;
	
	DomainAsset::register($this);
?>

<h1><?= Yii::t('topology', 'Domains'); ?></h1>

<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'domain-form',
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	]);
	
 	echo $this->render('//formButtons'); ?>

	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $domains,
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridDomains',
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
			        	'title'=> Yii::t("topology", 'Update'),
			        	'url' => '/topology/domain/update',
			        	'contentOptions'=>['style'=>'width: 15px;'],
			        ),
			        'name',
			        [
			        'label' => Yii::t('topology', 'Default Policy'),
			        	'value' => function($dom){
			        		return $dom->getPolicy();
			        	},	
					],
				),
		]);
	?>
	<?php	
		ActiveForm::end();
	?>
