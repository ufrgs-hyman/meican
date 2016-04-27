<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\topology\models\Provider;
use meican\circuits\models\Protocol;

\meican\circuits\assets\config\Index::register($this);

$form = ActiveForm::begin([
    'id'        => 'config-form',
    'method'    => 'post',
    'layout'    => 'horizontal'
]); 

?>

<div class="box box-default">
	<div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('circuits', 'Circuit service settings'); ?></h3>
    </div>
    <div class="box-body">
        <?= $form->field($model,'meicanNsa')->textInput(['size'=>50]); ?>
        <?= $form->field($model,'protocol')->dropDownList(ArrayHelper::map(Protocol::getTypes(), 'id', 'name')); ?>
        <?= $form->field($model,'defaultProviderNsa')->textInput(['size'=>50]); ?>
        <?= $form->field($model,'defaultCSUrl')->textInput(['size'=>50]); ?>
        <?= $form->field($model,'uniportsEnabled')->dropDownList(ArrayHelper::map([['id'=>'false', 'name'=>Yii::t('circuits', 'Disabled')],['id'=>'true','name'=>Yii::t('circuits', 'Enabled')]], 'id', 'name'), ['disabled'=>true]); ?>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-primary"><?= Yii::t("aaa", 'Save'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
