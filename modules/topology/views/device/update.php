<h1><?= Yii::t("topology", 'Update Device') ?></h1>

<?=
	$this->render('_form', array(
		'action' => 'update',
		'device' => $device, 
		'domains' => $domains,
	)); 
?>