<?php

use meican\aaa\assets\group\CreateEditAsset;

CreateEditAsset::register($this);

$this->params['box-title'] = Yii::t('aaa', 'Add Group');

?>

<?= $this->render('_form', array('group' => $group, 'apps' => $apps, 'root' => $root)); ?>