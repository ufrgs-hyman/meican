<h1><?= Yii::t('aaa', 'Add new user');?></h1>

<?php
use yii\widgets\ActiveForm; 
use yii\helpers\Html;
use yii\helpers\Url; 
use yii\helpers\ArrayHelper;

$form=ActiveForm::begin(array(
	'enableClientValidation'=>false,
)); ?>

	<h4>
	<font color="#3a5879">
		<div class="form input">
			<?= $form->field($user,'login'); ?>
		</div>

		<div class="form input">
			<?= $form->field($user,'password')->passwordInput(); ?>
		</div>
	</font>
	</h4>
	
	<h1><?= Yii::t('aaa', 'Information');?></h1>
	
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

	<div class="buttonsForm">
		<?= Html::submitButton(Yii::t('aaa', 'Create')); ?>
		<a href="<?= Url::toRoute('index');?>"><?= Html::Button(Yii::t('aaa', 'Cancel')); ?></a>
	</div>

<?php ActiveForm::end(); ?>