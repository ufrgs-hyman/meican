<?php $this->params['box-title'] = Yii::t('topology', 'Add Device'); ?>

<?=
	$this->render('_form', array(
		'action' => 'create',
		'device' => $device, 
		'domains' => $domains,
	)); 
?>