<?php $this->params['box-title'] = Yii::t('topology', 'Add Provider'); ?>

<?=
    $this->render('_form', array(
        'model' => $model,
    )); 
?>