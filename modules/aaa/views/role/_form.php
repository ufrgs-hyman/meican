<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<script>
    var systemGroups = '<?= json_encode($systemGroups) ?>';
</script>

<?php
$form=ActiveForm::begin(array(
    'id'=> $formId,
    'layout' => 'horizontal'
)); 

echo $form->field($udr,'_groupRoleName')->dropDownList($groups);

echo $form->field($udr,'domain')->dropDownList(array_merge($anyDomain, ArrayHelper::map($domains, 'name', 'name')));

ActiveForm::end(); ?>