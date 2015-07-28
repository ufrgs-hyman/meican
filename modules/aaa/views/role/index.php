<?php 

use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;

use app\components\LinkColumn;

use yii\helpers\Html;

use app\modules\aaa\assets\RoleAsset;

RoleAsset::register($this);

?>

<h1><?= Yii::t("aaa", "Access Roles"); ?></h1>

<?php

$form = ActiveForm::begin([
		'method' => 'post',
		'action' => ['delete'],
		'id' => 'role-form',
		'enableClientScript'=>false,
		'enableClientValidation' => false,
	]); 

?>

	<div class="formAccessControl input">
		<?= Yii::t("aaa", "User").' <b>'.$user->name.'</b>'; ?>
	</div><br>
	
	<div class="controls">
	<?=
		Html::a(Yii::t('init', 'Add'), array('create','id'=>$user->id)); 
	?>
	<?=
		Html::submitButton(Yii::t('init', 'Delete'), ['id'=>'deleteButton',]); 	
	?>
	</div>
	
	<div style="clear: both"></div>

<?php echo GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $userDomainRoles,
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
				'label' => '',
				'url' => 'update',
				'contentOptions'=>['style'=>'width: 15px;'],
			),
			[
				'attribute' => 'domain_id', 
				'format' => 'raw',
				'value' => function($model) { 
					$dom = $model->getDomain();
					if ($dom) return $dom->name;
					return Yii::t("aaa", "Any");
				 }
			],
			[
				'attribute' => '_groupRoleName',
				'format' => 'raw',
				'value' => function($model) {
					return $model->getGroup()->name;
				}
			],
			),
	]);
	
	ActiveForm::end();
?>
