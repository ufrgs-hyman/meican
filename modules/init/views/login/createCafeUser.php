<?php

use yii\widgets\ActiveForm; 
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form=ActiveForm::begin(array(
	'id'=>'create-cafe-user-form',
	'validateOnSubmit'=>true,
	'options'=>array(
        'class'=>'login',
    ),
)); ?>
	<h4>
    	<div><?= Yii::t('init', 'In the first access to the service is required create a user to identify you in the system.'); ?></div>
	</h4>
	<div id="message"><?= $form->errorSummary($model); ?></div>
	<div style="width: 100%;">
    	<div class="input">
         	<?= $form->field($model,'login'); ?>
		</div>
		<div class="input password">
            <?= $form->field($model,'password')->passwordInput(); ?>
		</div>
        <div class="input password">
            <?= $form->field($model,'passConfirm')->passwordInput(); ?>
        </div>
		<div class="submit">
        	<input class="next ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?= Yii::t('init', 'Sign up') ?>" role="button" aria-disabled="false" tabindex="3"/>
		</div>
 	</div>
<?php ActiveForm::end(); ?>