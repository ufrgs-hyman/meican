<h1><?= Yii::t('topology', 'Add Domain');?></h1>

<?=
	$this->render('_form', array(
		'domain' => $domain, 
	)); 
?>