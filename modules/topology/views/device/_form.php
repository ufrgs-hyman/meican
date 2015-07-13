<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\helpers\ArrayHelper;
?>

<?php $form= ActiveForm::begin([
	'id'=>'device-form',
	'enableAjaxValidation'=>false,
	'method' => 'post',
	'enableClientValidation' => false,
]); ?>

	<h4>
	<font color="#3a5879">
	<div class="form input">
		<?= $form->field($device,'name')->textInput(['size'=>30,'maxlength'=>50]); ?>
	</div>

	<div class="form input">
		<?= $form->field($device,'ip')->textInput(['size'=>30,'maxlength'=>16]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($device,'trademark')->textInput(['size'=>30,'maxlength'=>50]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($device,'model')->textInput(['size'=>30,'maxlength'=>50]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($device,'address')->textInput(['size'=>30,'maxlength'=>50]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($device,'latitude')->textInput(['size'=>30,'maxlength'=>30]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($device,'longitude')->textInput(['size'=>30,'maxlength'=>30]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($device,'node')->textInput(['size'=>30,'maxlength'=>50]); ?>
	</div>

	<div class="form input">
		<?= $form->field($device,'domain_id')->dropDownList(ArrayHelper::map($domains, 'id', 'name')); ?>
	</div>
		
	</h4>
	</font>

	<div class="buttonsForm">
		<?= Html::submitButton(Yii::t('topology', 'Save')) ?>
		<a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t('topology', 'Cancel')); ?></a>
	</div>

<?php ActiveForm::end(); ?>
