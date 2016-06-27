<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\widgets\Pjax;

use kartik\switchinput\SwitchInput;

\meican\circuits\assets\reservation\Status::register($this);

$this->params['header'] = [Yii::t('circuits', 'Circuits'), ['Home', Yii::t('circuits', 'Circuits'), 'Status']];

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("circuits", "Active or scheduled"); ?></h3>
        <div class="box-tools pull-right">
            <?= SwitchInput::widget(['id' => 'auto-refresh-scheduled-switch', 'name'=>'auto-refresh', 'value'=>true, 
                'pluginOptions' => [
                    'size' => 'small',
                    'labelText' => 'Auto update'
                ]]); ?>
        </div>
        
    </div>
    <div class="box-body">   
        <?php Pjax::begin(['id' => 'scheduled-pjax']); ?>

        <?= $this->render('_grid', array(
            'gridId' => 'scheduled-grid',
            'data' => $scheduledData,
            'searchModel'=> $searchModel
        )); ?>
        
        <?php Pjax::end(); ?>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("circuits", "Finished"); ?></h3>
        <div class="box-tools pull-right">
            <?= SwitchInput::widget(['id' => 'auto-refresh-finished-switch', 'name'=>'auto-refresh', 'value'=>true, 
                'pluginOptions' => [
                    'size' => 'small',
                    'labelText' => 'Auto update'
                ]]); ?>
        </div>
        
    </div>
    <div class="box-body">   
        <?php Pjax::begin(['id' => 'finished-pjax']); ?>

        <?= $this->render('_grid', array(
            'gridId' => 'finished-grid',
            'data' => $finishedData,
            'searchModel'=> $searchModel 
        )); ?>
        
        <?php Pjax::end(); ?>
    </div>
</div>