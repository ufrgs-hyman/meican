<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<?php
$form=ActiveForm::begin(array(
    'id'=> $formId,
    'layout' => 'horizontal'
)); 

echo $form->field($udr,'domain')->dropDownList(array_merge($anyDomain, ArrayHelper::map($domains, 'name', 'name')));

echo $form->field($udr,'_groupRoleName')->dropDownList($groups);

ActiveForm::end(); ?>