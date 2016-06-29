<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

\meican\aaa\assets\login::register($this);

?>

<div class="login-box">
    <div class="login-logo">
    <?= Html::img("@web/images/meican_new.png", ['style'=>'width: 240px;','title' => 'MEICAN']); ?>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <?php $form = ActiveForm::begin(array(
            'id'=>'login-form',
            'enableClientValidation'=>false,
        )); ?>
        <p class="login-box-msg">Sign in to access the service</p>
        <div class="form-group has-feedback">
          <?= $form->field($model, 'login', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Login']])->label(false) ?>
          <span class="fa fa-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <?= $form->field($model, 'password', ['inputOptions' => ['class' => 'form-control', 'placeholder' => 'Password']])->passwordInput()->label(false) ?>
          <span class="fa fa-lock form-control-feedback"></span>
        </div>
        <?= Html::a(Yii::t("home", "I forgot my password"),array('/aaa/login/password')); ?>
        <div class="row">
          <div class="col-xs-8">
            <div class="checkbox icheck" hidden>
              <label>
                <input type="checkbox"> Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary form-control']) ?>
          </div>
        </div>
        <?php if($federation): ?>
            <div class="text-center">
            <p>Other options:</p>
            <a id="cafe-button" href="#" class="btn btn-default"><?= Html::img('@web/images/cafe.png', ['style'=>'margin-right: 15px; width:60px; margin-bottom:1px;']); ?>Sign in using
            CAFé Federation</a><br>          
            </div>
        <?php endif; ?>
        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div>