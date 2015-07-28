<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;

use app\components\LinkColumn;

use app\modules\aaa\assets\UserAsset;

UserAsset::register($this);

?>

<h1><?= Yii::t("aaa", 'Users'); ?></h1>

<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'user-form',
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	]);
	
 	echo $this->render('//formButtons');

	echo GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $users,
		'layout' => "{items}{summary}{pager}",
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
				'url' => 'update',
				'contentOptions'=>['style'=>'width: 15px;'],
			),
			array(
				'class'=> LinkColumn::className(),
				'image'=>'/images/role.png',
				'title' => Yii::t('aaa', 'Manage Access Roles'),
				'url' => 'role/index',
				'contentOptions'=>['style'=>'width: 15px;'],
			),
				'login',
				'name',
			),
	]);
	
	ActiveForm::end();
?>