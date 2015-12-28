<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<div class="login-box">
  <div class="login-logo">
    <?= Html::img("@web/images/meican_new.png", ['style'=>'width: 240px;','title' => 'MEICAN']); ?>
  </div>
  <!-- /.login-logo -->
  <?php $form = ActiveForm::begin(); ?>
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to access the service</p>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'login', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Login']])->label(false) ?>
            <span class="fa fa-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'password', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Password']])->passwordInput()->label(false) ?>
            <span class="fa fa-lock form-control-feedback"></span>
        </div>
        <a href="#">I forgot my password</a>
        <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
              <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-flat form-control', 'name' => 'login-button','value'=>'submit']) ?>
            </div>
        </div>
        <div class="text-center">
          <p>Other options:</p>
          <a href="#" class="btn btn-default"><?= Html::img('@web/images/cafe.png', ['style'=>'margin-right: 15px; width:60px; margin-bottom:1px;']); ?>Sign in using
        CAFé Federation</a><br>          
        </div>
    </div>
  <?php ActiveForm::end(); ?>
  <!-- /.login-box-body -->
</div>