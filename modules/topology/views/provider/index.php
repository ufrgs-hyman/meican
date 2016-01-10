<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;
use meican\topology\assets\provider\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = ["Providers", ['Home', 'Topology']];

?>

<div class="box box-default">
    <div class="box-header with-border">
        <?= GridButtons::widget(); ?>
    </div>
    <div class="box-body">

<?php
    $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['delete'],
        'id' => 'provider-form',  
        'enableClientScript'=>false,
        'enableClientValidation' => false,
    ]);
    echo GridView::widget([
        'options' => ['class' => 'list'],
        'dataProvider' => $providers,
        'layout' => "{items}{summary}{pager}",
        'columns' => array(
                array(
                    'class'=>CheckboxColumn::className(),
                    'name'=>'delete',         
                    'checkboxOptions'=>[
                        'class'=>'deleteCheckbox',
                    ],
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                array(
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/edit_1.png',
                    'label' => '',
                    'url' => 'update',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                array(
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/eye.png',
                    'label' => '',
                    'title'=>Yii::t("topology",'Show details and services of this provider'),
                    'url' => 'view',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                'name',
                [
                    'attribute'=> 'type',
                    'value' => function($model) {
                        return $model->getType();
                    },
                ],
                'nsa',
                'latitude',
                'longitude',
            ),
    ]);

    ActiveForm::end();
?>

    </div>
</div>
