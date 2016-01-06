<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$form=ActiveForm::begin(array(
    'id'=> 'add-role-form',
    'layout' => 'horizontal'
)); 

echo $form->field($udr,'_groupRoleName')->dropDownList($groups); ?>

<div id="domain-select">
    <?= $form->field($udr,'domain')->dropDownList(array_merge($anyDomain, ArrayHelper::map($domains, 'name', 'name'))); ?>
</div>

<?php ActiveForm::end(); ?>