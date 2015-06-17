<h1><?= Yii::t("topology", 'Update Domain'); ?></h1>

<?=
	$this->render('_form', array(
		'domain' => $domain, 
	)); 
?>