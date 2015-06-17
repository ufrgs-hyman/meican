<h1><?= Yii::t("topology", 'Add Device') ?></h1>

<?=
	$this->render('_form', array(
		'action' => 'create',
		'device' => $device, 
		'domains' => $domains,
	)); 
?>