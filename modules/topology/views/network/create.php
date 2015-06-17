<h1><?= Yii::t('topology', 'Add Network');?></h1>

<?=
	$this->render('_form', array(
		'action' => 'create',
		'domains' => $domains, 
		'network' => $network,
	)); 
?>