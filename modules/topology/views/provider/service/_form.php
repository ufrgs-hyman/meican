<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\topology\models\Service;

$this->params['header'] = [$provider->name, ['Home', 'Topology', 'Providers', $provider->name, 'Services']];

?>

<?php $form= ActiveForm::begin([
    'id'        => 'service-form',
    'method'    => 'post',
    'layout'    => 'horizontal'
]); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->params['box-title']; ?></h3>
    </div>
    <div class="box-body">
    <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(Service::getTypes(), 'id', 'name')); ?>
    <?= $form->field($model,'url')->textInput(['size'=>80]); ?>
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
