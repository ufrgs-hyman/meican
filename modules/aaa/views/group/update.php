<?php

use app\modules\aaa\assets\GroupCreateEditAsset;

GroupCreateEditAsset::register($this);

?>

<h1><?= Yii::t("aaa", 'Edit group'); ?></h1>

<?= $this->render('_form', array('group' => $group, 'apps' => $apps,  'root' => $root, 'childsChecked' => $childsChecked)); ?>