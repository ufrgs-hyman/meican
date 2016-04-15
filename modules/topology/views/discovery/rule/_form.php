<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\topology\models\Service;
use meican\topology\models\DiscoveryRule;
use meican\topology\assets\sync\SyncFormAsset;

SyncFormAsset::register($this);

$this->params['header'] = [Yii::t('topology', 'Discovery'), ['Home', 'Topology']];

$form= ActiveForm::begin([
    'id'        => 'rule-form',
    'method'    => 'post',
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
            [['id'=>false, 'name'=>Yii::t("topology", 'Disabled')],['id'=>true,'name'=>Yii::t("topology", 'Enabled')]], 'id', 'name'));
            echo '<a id="cron-open-link" style="float: left;
    width: 130px;
    margin-left: 0px;
    margin-right: 10px;
    text-align: right;
    font-size: 100%;" href="#">'.Yii::t("topology", "Set recurrence").'</a>'; ?>
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

<?php ActiveForm::end(); ?>