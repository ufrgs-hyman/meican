<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;

\meican\aaa\assets\user\Update::register($this);

$this->params['header'] = [$user->scenario == $user::SCENARIO_UPDATE_ACCOUNT ? "My account" : "Users", ['Home']];

$form=ActiveForm::begin(array(
    'layout' => 'horizontal'
)); ?>       

<div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Edit <?= $user->scenario == $user::SCENARIO_UPDATE_ACCOUNT ? Yii::t('aaa', 'my account') : Yii::t('aaa', 'User'); ?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($user, 'login')->textInput(['disabled' => $user->scenario == $user::SCENARIO_UPDATE_ACCOUNT ? true : false]); ?>
                <?= $form->field($user, 'name'); ?>
                <?= $form->field($user, 'email'); ?>  
                
                <?= $form->field($user, 'isChangedPass')->checkBox(['class'=>'icheck']); ?>

                <div id="changePasswordForm" style="display: none;">
                    <?php if($user->scenario == $user::SCENARIO_UPDATE_ACCOUNT) echo $form->field($user, 'currentPass')->passwordInput(); ?>
            
                    <?= $form->field($user, 'newPass')->passwordInput(); ?>
                
                    <?= $form->field($user, 'newPassConfirm')->passwordInput(); ?>
                </div> 
            </div> 
            <div class="col-md-6">
                <?= $form->field($user, 'language')->dropDownList(
                      array('en-US' => Yii::t('aaa', 'English'), 'pt-BR' => Yii::t('aaa', 'Portuguese')));
                  ?>
                    <?= $form->field($user, 'dateFormat')->dropDownList(
                            array(
                            'dd/MM/yyyy' => Yii::t('aaa', 'dd/mm/yyyy'), 
                            'MM/dd/yyyy' => Yii::t('aaa', 'mm/dd/yyyy'), 
                            'yyyy/MM/dd' => Yii::t('aaa', 'yyyy/mm/dd')));
                  ?> 
                  <?= $form->field($user, 'timeFormat')->dropDownList(
                            array(
                            'HH:mm' => Yii::t('aaa', 'HH:mm')));
                  ?> 

                  <?php     
                    $zones = DateTimeZone::listIdentifiers();
                    foreach ($zones as &$zone) {
                        $zone = str_replace("_", " ", $zone);
                    }

                    echo $form->field($user, 'timeZone')->dropDownList(array_combine(
                        DateTimeZone::listIdentifiers(),$zones))->label(Yii::t('aaa', 'Time Zone'));
                  ?>  
            </div> 
        </div>
    </div>
    <div class="box-footer">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>