<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<script>
	var systemGroups = '<?= json_encode($systemGroups) ?>';
</script>

<?php
$form=ActiveForm::begin(array(
    'id'=> 'add-role-form',
    'layout' => 'horizontal'
)); 

echo $form->field($udr,'_groupRoleName')->dropDownList($groups);

echo $form->field($udr,'domain')->dropDownList(array_merge($anyDomain, ArrayHelper::map($domains, 'name', 'name')));

ActiveForm::end(); ?>