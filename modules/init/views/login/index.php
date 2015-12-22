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
        <p class="login-box-msg">Sign in to start your session</p>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'login', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Username']])->label(false) ?>
            <span class="fa fa-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'password', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Password']])->passwordInput()->label(false) ?>
            <span class="fa fa-lock form-control-feedback"></span>
        </div>
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
              <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-flat form-control', 'name' => 'login-button']) ?>
            </div>
        </div>
        <div class="text-center">
          <p>- OR -</p>
          <a href="#">Sign in using CAFé</a><br>
          <a href="#">I forgot my password</a>
        </div>

    </div>
  <?php ActiveForm::end(); ?>
  <!-- /.login-box-body -->
</div>