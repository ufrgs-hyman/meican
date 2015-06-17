<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;

use app\components\LinkColumn;

use app\modules\aaa\assets\GroupAsset;

GroupAsset::register($this);

?>

<h1><?= Yii::t("aaa", 'Groups'); ?></h1>

<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'group-form',
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	]);
	
 	echo $this->render('//formButtons');

	echo GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $groups,
		'summary' => '',
		'columns' => array(
			array(
				'class'=> CheckboxColumn::className(),
				'name'=>'delete',
				'checkboxOptions'=> [
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
				'name',
			),
	]);
	
	ActiveForm::end();
?>