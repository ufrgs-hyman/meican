<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

$this->params['header'] = [Yii::t('circuits', 'Configuration'), [Yii::t('circuits', 'Circuits')]];

?>

<?= $this->render('_form', array(
    'model' => $model, 
)); ?>
