<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php

$form=ActiveForm::begin(array(
	'enableClientValidation'=>false,
)); ?>
	<div class="formAccessControl input">
		<?= Yii::t("aaa", 'User').' <b>'.$udr->getUser()->login.'</b>'; ?>
	</div><br>
	
	<h4>
	<font color="#3a5879">
		<div class="form input">
			<?= $form->field($udr,'domain_id')->dropDownList(ArrayHelper::map($domains, 'id', 'name')); ?>
		</div>
	
		<div class="form input">
			<?= $form->field($udr,'_groupRoleName')->dropDownList(ArrayHelper::map($groups, 'role_name', 'name')); ?>
		</div>
	</font>
	</h4>
	
	
	<div class="buttonsForm">
		<?= Html::submitButton(Yii::t("aaa", 'Save')); ?>
		<a href="<?= Url::toRoute(['index','id'=>$udr->user_id]);?>"><?= Html::Button(Yii::t("aaa", 'Cancel')); ?></a>
	</div>
	
<?php ActiveForm::end(); ?>