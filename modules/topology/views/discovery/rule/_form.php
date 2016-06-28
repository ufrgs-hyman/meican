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

\meican\topology\assets\discovery\Rule::register($this);

$this->params['header'] = [Yii::t('topology', 'Discovery'), ['Home', 'Topology', 'Discovery']];

$form= ActiveForm::begin([
    'id'        => 'rule-form',
    'layout'    => 'horizontal'
]); 

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->params['box-title']; ?></h3>
    </div>
    <div class="box-body">
        <?= $form->field($model,'name')->textInput(['size'=>50]); ?>
        <?= $form->field($model,'protocol')->dropDownList(ArrayHelper::map(DiscoveryRule::getProtocols(), 'id', 'name')); ?>
        <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(DiscoveryRule::getTypes(), 'id', 'name')); ?>
    <div id="subscribed-row" <?= ($model->type == Service::TYPE_NSI_DS_1_0) ? "" : "disabled" ?>>
        <?= $form->field($model,'subscribe_enabled')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>Yii::t("topology", 'Disabled')],['id'=>true,'name'=>Yii::t("topology", 'Enabled')]], 'id', 'name'), 
                ['disabled'=>($model->protocol == Service::TYPE_NSI_DS_1_0) ? false : true]); ?>
    </div>
        <?php echo $form->field($model,'freq_enabled')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>Yii::t("topology", 'Disabled')],['id'=>true,'name'=>Yii::t("topology", 'Enabled')]], 'id', 'name')); ?>
        <?= $form->field($model,'auto_apply')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>Yii::t("topology", 'Manually')],['id'=>true,'name'=>Yii::t("topology", 'Automatically')]], 'id', 'name')); ?>
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

<?php Modal::begin([
    'id' => 'schedule-modal',
    'header' => 'Schedule form',
    'footer' => '<button type="button" class="btn btn-primary confirm-btn">Confirm</button>'.        
        '<button type="button" class="btn btn-default close-btn">Close</button>'
]); ?>

<div id="cron-widget"></div>

<?php Modal::end(); ?>

<?php ActiveForm::end(); ?>