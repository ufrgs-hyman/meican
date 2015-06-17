<?php 

	use yii\widgets\ActiveForm;
	use yii\helpers\Html;
	use yii\helpers\Url;
?>

<?php $form= ActiveForm::begin([
	'id'=>'aggregator-form',
	'method' => 'post',
	'enableClientValidation' => false,
]); ?>
	<h4>
	<font color="#3a5879">

	<div class="form input">
		<?= $form->field($aggregator,'nsa')->textInput(['size'=>50]); ?>
	</div>

	<div class="form input">
		<?= $form->field($aggregator,'connection_url')->textInput(['size'=>50]); ?>
	</div>
	
	<div class="form input">
		<?= $form->field($aggregator,'discovery_url')->textInput(['size'=>50]); ?>
	</div>
	
	</font>
	</h4>

	<div class="buttonsForm">
		<?= Html::submitButton(Yii::t("topology", 'Save')); ?>
		<a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
	</div>

	
<?php ActiveForm::end(); ?>
