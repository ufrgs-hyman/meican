<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

$this->params['box-title'] = Yii::t('topology', 'Add Rule'); ?>

<?= $this->render('_form', array(
    'model' => $model, 
)); ?>