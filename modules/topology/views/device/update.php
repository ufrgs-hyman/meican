<?php $this->params['box-title'] = Yii::t('topology', 'Update Device'); ?>

<?=
	$this->render('_form', array(
		'action' => 'update',
		'device' => $device, 
		'domains' => $domains,
	)); 
?>