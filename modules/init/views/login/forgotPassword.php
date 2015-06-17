<?php

use yii\widgets\ActiveForm; 
use yii\helpers\Html;
use yii\helpers\Url;

?>

<script type="text/javascript">
var RecaptchaOptions = {
   lang : 'pt-BR',
};
</script>

<script src='https://www.google.com/recaptcha/api.js'></script>

<?php $form=ActiveForm::begin(array(
	'id'=>'password-form',
	'validateOnSubmit'=>true,
	'options'=>array(
			'class'=>'login',
	),
	
));

?>
	<h4>
	<div><?= Yii::t('init', 'Insert your user or email, and you will receive an email with your new password.'); ?></div>
	</h4>
	<div id="message"><?= $form->errorSummary($model); ?></div>
	<div style="width: 100%;">
    	<div class="input">
         	<?= $form->field($model,'login'); ?>
		</div>
		<div class="input">
            <?= $form->field($model,'email') ?>
		</div>

		<div data-theme="clean" data-type="image" style="padding-top: 15px; transform:scale(0.91); transform-origin:0 0" class="g-recaptcha" data-sitekey="6LdhOQgTAAAAAKxJtikzjEJ3uXxOE3a5qW9WMVjz"></div>

		<div class="submit">
        	<input class="next ui-button ui-widget ui-state-default ui-corner-all" type="submit" name="submit_password" value="<?= Yii::t('init', 'Send me') ?>" role="button" aria-disabled="false" tabindex="3"/>
			<input type="button" id="button_cancel" onclick="location.href='../'" class="cancel" value=<?= Yii::t("init", 'Cancel'); ?>>
		</div>
 	</div>

<?php ActiveForm::end(); ?>