<?php

use yii\widgets\ActiveForm; 
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form=ActiveForm::begin(array(
	'id'=>'login-form',
    'enableClientValidation'=>false,
	'options'=>array(
        'class'=>'login',
    ),
)); ?>
	<div id="message"><?= $form->errorSummary($model); ?></div>
	<div style="width: 100%;">
    	<div class="input">
         	<?= $form->field($model,'login'); ?>
		</div>
		<div class="input password">
            <a href="<?= Url::toRoute("login/password") ?>" tabindex="5">(<?= Yii::t('init', 'Forgot your password?') ?>)</a>
            <?= $form->field($model,'password')->passwordInput(); ?>
		</div>
		<div class="submit">
        	<input style="margin-left: 35%;" class="next ui-button ui-widget ui-state-default ui-corner-all" type="submit" id="submit_login" name="submit_login" value="<?= Yii::t('init', 'Sign in') ?>" role="button" aria-disabled="false" tabindex="3"/>
		</div>
 	</div>
 	<div class="login"><?= Yii::t('init', 'Alternatively, 
    you can access MEICAN using the Federated Academic Community (CAFe):'); ?>
    </div>
    <div style="margin-top: 10px;">
    		<button id="cafe-button" style="margin-left: 34%; width: 80px; height: 32px;">
                <img alt="Cafe" width="100%" src="<?= Url::base(); ?>/images/cafe.png">
            </button>
    </div>
<?php ActiveForm::end(); ?>