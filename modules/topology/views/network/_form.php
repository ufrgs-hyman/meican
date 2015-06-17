<?php 

	use yii\widgets\ActiveForm;
	use yii\helpers\Html;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Url;
?>

<?php $form= ActiveForm::begin([
	'id'=>'network-form',
	'method' => 'post',
	'enableClientValidation' => false,
]); ?>
	
	<h4>
	<font color="#3a5879">
	
	<div class="form input">
		<?= $form->field($network, 'id')->hiddenInput()->label('');?>
	</div>
	<div class="form input">
		<?= $form->field($network,'name')->textInput(['size'=>30,'maxlength'=>30]); ?>
	</div>

	<div class="form input">
		<?= $form->field($network,'latitude')->textInput(['size'=>30,'maxlength'=>100]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($network,'longitude')->textInput(['size'=>30,'maxlength'=>100]); ?>
	</div>

	<div class="form input">
		<?= $form->field($network,'domain_id')->dropDownList(ArrayHelper::map($domains, 'id', 'name')); ?>
	</div>
	
	</font>
	</h4>
	
	<div class="buttonsForm">
		<?= Html::submitButton(Yii::t("topology", 'Save')) ?>
		<a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
	</div>

<?php ActiveForm::end(); ?>
