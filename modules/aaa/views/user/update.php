<?php 

use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\grid\LinkColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DropDownUtils;
use app\models\Domain;

?>

<h1><?= Yii::t('aaa', 'Edit user'); ?></h1>

<?php

$form=ActiveForm::begin(array(
	'enableClientValidation'=>false,
)); ?>

	<h4>
	<font color="#3a5879">
		<div class="form input">
			<?= $form->field($user,'login'); ?>
		</div>
	
		<div class="form input">
			<?= $form->field($user,'password', [
			    'inputOptions' => [
			        'placeholder' => Yii::t('aaa', 'Unchanged'),
			    ],
			])->passwordInput(); ?>
		</div>
	</font>
	</h4>
	
	<h1><?= Yii::t('aaa', 'Information'); ?></h1>
	
	<h4>
	<font color="#3a5879">
		<div class="form input">
			<?= $form->field($user,'name'); ?>
		</div>
	
		<div class="form input">
			<?= $form->field($user,'email'); ?>
		</div>
	</font>
	</h4>
	
	<div style="clear: both"></div>
	<br>
	
	<div class="buttonsForm">
		<?= Html::submitButton(Yii::t('aaa', 'Save')); ?>
		<a href="<?= Url::toRoute('index');?>"><?= Html::Button(Yii::t('aaa', 'Cancel')); ?></a>
	</div>

<?php 
	ActiveForm::end();
?>
