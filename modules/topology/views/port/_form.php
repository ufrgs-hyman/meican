<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<?php
$form=ActiveForm::begin(array(
    'id'=> $formId,
    'layout' => 'horizontal',
)); ?>

<?= $form->field($port,'urn')->textInput(['size'=>50,'maxlength'=>16, 'disabled' => !$port->isNewRecord]); ?>
<?= $form->field($port,'network_id')->dropDownList(ArrayHelper::map($networks->all(), 'id', 'name'), ['disabled' => !$port->isNewRecord]); ?>
<?= $form->field($port,'name')->textInput(['size'=>50,'maxlength'=>50]); ?>
<?= $form->field($port,'vlan_range')->textInput(['size'=>30,'maxlength'=>50]); ?>
<?= $form->field($port,'capacity')->textInput(['size'=>30,'maxlength'=>20]); ?>
<?= $form->field($port,'max_capacity')->textInput(['size'=>30,'maxlength'=>20]); ?>
<?= $form->field($port,'min_capacity')->textInput(['size'=>30,'maxlength'=>20]); ?>
<?= $form->field($port,'granularity')->textInput(['size'=>30,'maxlength'=>30]); ?>
<?= $form->field($port,'location_id')->dropDownList(ArrayHelper::map($locations->all(), 'id', 'name'), ['prompt'=>'']); ?>
<?= $form->field($port,'devicetype_id')->dropDownList(ArrayHelper::map($devices, 'id', 'name'), ['prompt'=>'']); ?>



<?php ActiveForm::end(); ?>
