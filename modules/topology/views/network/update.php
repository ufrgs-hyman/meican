<?php $this->params['box-title'] = Yii::t('topology', 'Update Network'); ?>

<?=
	$this->render('_form', array(
		'action' => 'update',
		'domains' => $domains, 
		'network' => $network,
	)); 
?>