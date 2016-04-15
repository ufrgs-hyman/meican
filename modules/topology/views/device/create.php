<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

$this->params['box-title'] = Yii::t('topology', 'Add Device'); ?>

<?=
    $this->render('_form', array(
        'action' => 'create',
        'device' => $device, 
        'domains' => $domains,
    )); 
?>