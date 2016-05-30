<?php 

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->params['header'] = [Yii::t('aaa', 'Configuration'), ['Home', Yii::t('aaa', 'Users'), 'Configuration']];

?>

<?= $this->render('_form', array(
    'model' => $model, 
)); ?>
