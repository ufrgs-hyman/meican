<h1><?= Yii::t("aaa", 'Edit group'); ?></h1>

<?= $this->render('_form', array('group' => $group, 'apps' => $apps, 'childsChecked' => $childsChecked)); ?>