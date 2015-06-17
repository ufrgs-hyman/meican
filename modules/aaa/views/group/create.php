<h1><?= Yii::t("aaa", 'Add new group'); ?></h1>

<?= $this->render('_form', array('group' => $group, 'apps' => $apps)); ?>