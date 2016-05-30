<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\aaa\models\UserDomainRole;
use meican\topology\models\Domain;

$form= ActiveForm::begin([
    'id'        => 'config-form',
    'method'    => 'post',
    'layout'    => 'horizontal'
]); 

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("aaa", "Federation sign-in"); ?></h3></h3>
    </div>
    <div class="box-body">
        <?= $form->field($model,'status')->dropDownList(ArrayHelper::map([['id'=>'true','name'=>Yii::t("aaa" , "Enabled")],['id'=>'false', 'name'=>Yii::t("aaa" , "Disabled")]], 'id', 'name')); ?>
        <?= $form->field($model,'group')->dropDownList(ArrayHelper::map(UserDomainRole::getDomainGroups(), 'role_name', 'name')); ?>
		<?= $form->field($model,'domain')->dropDownList(array_merge([null=>Yii::t("aaa" , "any")], ArrayHelper::map(Domain::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'name', 'name'))); ?>
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
