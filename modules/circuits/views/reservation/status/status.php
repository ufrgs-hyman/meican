<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\widgets\Pjax;

use kartik\switchinput\SwitchInput;

\meican\circuits\assets\reservation\Status::register($this);

$this->params['header'] = [Yii::t('circuits', 'Status'), ['Home', Yii::t('circuits', 'Circuits'), 'Status']];

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("circuits", "Circuits"); ?></h3>
        <div class="box-tools pull-right">
            <?= SwitchInput::widget(['id' => 'auto-refresh-switch', 'name'=>'auto-refresh', 'value'=>true, 
                'pluginOptions' => [
                    'size' => 'small',
                    'labelText' => 'Auto update'
                ]]); ?>
        </div>
        
    </div>
    <div class="box-body">   
        <?php Pjax::begin(['id' => 'circuits-pjax']); ?>

        <?= $this->render('_grid', array(
            'gridId' => 'circuits-grid',
            'data' => $data,
            'searchModel'=> $searchModel,
            'allowedDomains' => $allowedDomains
        )); ?>
        
        <?php Pjax::end(); ?>
    </div>
</div>
