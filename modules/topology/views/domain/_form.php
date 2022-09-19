<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;

use kartik\color\ColorInput;

$this->params['header'] = [Yii::t('topology', 'Domains'), [Yii::t('home', 'Home'), Yii::t('topology', 'Topology')]];

$form= ActiveForm::begin([
    'id'        => 'domain-form',
    'method'    => 'post',
    'layout'    => 'horizontal'
]); 

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->params['box-title']; ?></h3>
    </div>
    <div class="box-body">
        <center> <h4> <em>General Settings</em> </h4> </center>
        <?= $form->field($domain,'name')->textInput(['size'=>30,'maxlength'=>60]); ?>
        <?= $form->field($domain,'default_policy')->dropDownList($domain->getPolicyOptions()); ?>
        <?= $form->field($domain, 'color')->widget(ColorInput::classname(), [
            'options' => ['placeholder' => 'Select color ...', 'readonly' => true],
        ]); ?>
        <hr>

        <center> <h4> <em>Default View Settings</em> </h4> </center>
        <?= $form->field($domain, 'grouped_nodes')->checkBox(['class'=>'icheck' , 'value' => true, 'label' => 'Group Nodes at Inicialization']); ?>  
        <!-- <?= 123123123 ?>   -->
    </div>
    <div class="box-footer">
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-primary"><?= Yii::t("topology", 'Save'); ?></button>
            </div>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>
