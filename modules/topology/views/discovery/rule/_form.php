<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\topology\models\Service;
use meican\topology\models\DiscoveryRule;

use kartik\switchinput\SwitchInput;

\meican\topology\assets\discovery\Rule::register($this);

$this->params['header'] = [$this->params['box-title'], ['Home', 'Topology', 'Discovery', 'Rule']];

$form= ActiveForm::begin([
    'id'        => 'rule-form',
    'layout'    => 'horizontal'
]); 

?>

<div class="row">
    <div class="col-md-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Details</h3>
            </div>
            <div class="box-body">
                <?= $form->field($model,'name')->textInput(['size'=>50]); ?>
                <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(DiscoveryRule::getTypes(), 'id', 'name')); ?>
                <?= $form->field($model,'auto_apply')->dropDownList(ArrayHelper::map(
                    [['id'=>false, 'name'=>Yii::t("topology", 'Manually')],['id'=>true,'name'=>Yii::t("topology", 'Automatically')]], 'id', 'name')); ?>
                <?= $form->field($model,'protocol')->dropDownList(ArrayHelper::map(DiscoveryRule::getProtocols(), 'id', 'name')); ?>
                <?= $form->field($model,'url')->textInput(['size'=>50]); ?>
                <?= $form->field($model,'freq')->hiddenInput()->label(""); ?>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" class="btn btn-primary"><?= Yii::t("topology", 'Save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Schedule</h3>
                <div class="box-tools" style="margin-right:10px;">
                    <?= $form->field($model, 'freq_enabled')->widget(SwitchInput::classname(), [
                        'pluginOptions' => [
                            'size' => 'small'
                    ]])->label(''); ?>
                </div>
            </div>
            <div class="box-body">
                <div id="cron-widget"></div>
            </div>
            <div class="box-footer">
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
