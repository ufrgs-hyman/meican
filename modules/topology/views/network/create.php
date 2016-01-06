<?php $this->params['box-title'] = Yii::t('topology', 'Add Network'); ?>

<?=
    $this->render('_form', array(
        'action' => 'create',
        'domains' => $domains, 
        'network' => $network,
    )); 
?>