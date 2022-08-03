<?php 
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

$this->params['box-title'] = Yii::t('topology', 'Add Location'); ?>

<?=
    $this->render('_form', array(
        'action' => 'create',
        'location' => $location, 
        'domains' => $domains,
    )); 
?>