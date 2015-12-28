<?php

use meican\aaa\assets\group\CreateEditAsset;

CreateEditAsset::register($this);

?>

<h1><?= Yii::t("aaa", 'Edit group'); ?></h1>

<?= $this->render('_form', array('group' => $group, 'apps' => $apps,  'root' => $root, 'childsChecked' => $childsChecked)); ?>