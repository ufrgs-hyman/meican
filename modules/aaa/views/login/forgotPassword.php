<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>

<script type="text/javascript">
var RecaptchaOptions = {
   lang : 'pt-BR',
};
</script>

<script src='https://www.google.com/recaptcha/api.js'></script>

<div class="login-box">
  <div class="login-logo">
    <?= Html::img("@web/images/meican_new.png", ['style'=>'width: 240px;','title' => 'MEICAN']); ?>
  </div>
  <!-- /.login-logo -->
    <div class="login-box-body">
      <?php $form = ActiveForm::begin(array(
        'id'=>'password-form',
      	'validateOnSubmit'=> true,

      )); ?>
      <p class="login-box-msg"><?= Yii::t('home', 'Insert your user or email, and you will receive an email with your new password.'); ?></p>
      
      <div class="form-group has-feedback">          
          <?= $form->field($model,'login', ['inputOptions' => ['class' => 'form-control', 'placeholder' => Yii::t('home', 'Login')]])->label(false) ?>
          <span class="fa fa-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
          <?= $form->field($model, 'email', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Email']])->input('email')->label(false) ?>
          <span class="fa fa-at form-control-feedback"></span>
      </div>

	  <div data-theme="clean" data-type="image" style="padding-bottom: 15px; transform:scale(1.06);-webkit-transform:scale(1.06);transform-origin:0 0;-webkit-transform-origin:0 0;" class="g-recaptcha" data-sitekey="<?= Yii::$app->params["google.recaptcha.site.key"] ?>"></div>

      <div class="row">
          <div class="col-xs-8">
            <?= Html::submitButton(Yii::t('home', 'Send me'), ['class' => 'btn btn-primary btn-flat form-control']) ?>
          </div>
          <div class="col-xs-4">
            <?= Html::a(Yii::t('home', 'Cancel'), ['/aaa/login/'], ['style'=>'text-align: center;', 'class'=>'btn-primary btn-flat form-control']) ?>
          </div>
      </div>
      
      <?php ActiveForm::end(); ?>
    </div>
  <!-- /.login-box-body -->
</div>