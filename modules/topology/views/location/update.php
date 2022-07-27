<?php 
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

$this->params['box-title'] = Yii::t('topology', 'Update Location'); ?>

<?=
	$this->render('_form', array(
		'action' => 'update',
		'location' => $location, 
		'domains' => $domains,
		'devices' => $devices,
	)); 
?>