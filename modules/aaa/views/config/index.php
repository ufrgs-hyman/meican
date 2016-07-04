<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->params['header'] = [Yii::t('aaa', 'Configuration'), ['Home', Yii::t('aaa', 'Users'), 'Configuration']];

?>

<?= $this->render('_form', array(
    'model' => $model, 
)); ?>
