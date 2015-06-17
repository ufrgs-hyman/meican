<h1><?= Yii::t("aaa", 'Edit role');?></h1>

<?= $this->render('_form', array('udr' => $udr, 'domains' => $domains, 'groups' => $groups)); ?>