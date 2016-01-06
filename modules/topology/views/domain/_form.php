<?php 

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\topology\assets\domain\FormAsset;

FormAsset::register($this);

$this->params['header'] = [Yii::t('topology', 'Domains'), ['Home', 'Topology', 'Domains']];

$form= ActiveForm::begin([
    'id'        => 'domain-form',
    'method'    => 'post',
    'layout'    => 'horizontal'
]); 

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->params['box-title']; ?></h3>
    </div>
    <div class="box-body">
        <?= $form->field($domain,'name')->textInput(['size'=>30,'maxlength'=>60]); ?>
        <?= $form->field($domain,'default_policy')->dropDownList($domain->getPolicyOptions()); ?>
        <?= $form->field($domain,'color')->hiddenInput(); ?>
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
