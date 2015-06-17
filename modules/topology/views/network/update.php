<h1><?= Yii::t('topology', 'Update Network');?></h1>

<?=
	$this->render('_form', array(
		'action' => 'update',
		'domains' => $domains, 
		'network' => $network,
	)); 
?>