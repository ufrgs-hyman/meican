<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->params['header'] = [Yii::t('topology', 'Devices'), [Yii::t('home', 'Home'), Yii::t('topology', 'Topology')]];

$form= ActiveForm::begin([
    'id'        => 'device-form',
    'method'    => 'post',
    'layout'    => 'horizontal'
]); 

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->params['box-title']; ?></h3>
    </div>
    <div class="box-body">
        <?= $form->field($device,'name')->textInput(['size'=>30,'maxlength'=>50]); ?>
        <?= $form->field($device,'ip')->textInput(['size'=>30,'maxlength'=>16]); ?>
        <?= $form->field($device,'trademark')->textInput(['size'=>30,'maxlength'=>50]); ?>
        <?= $form->field($device,'model')->textInput(['size'=>30,'maxlength'=>50]); ?>
        <?= $form->field($device,'address')->textInput(['size'=>30,'maxlength'=>50]); ?>
        <?= $form->field($device,'latitude')->textInput(['size'=>30,'maxlength'=>30]); ?>
        <?= $form->field($device,'longitude')->textInput(['size'=>30,'maxlength'=>30]); ?>
        <?= $form->field($device,'node')->textInput(['size'=>30,'maxlength'=>50]); ?>
        <?= $form->field($device,'domain_id')->dropDownList(ArrayHelper::map($domains, 'id', 'name')); ?>
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
