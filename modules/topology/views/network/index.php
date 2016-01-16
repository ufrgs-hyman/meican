<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */
   
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use meican\base\grid\IcheckboxColumn;
use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;
use meican\topology\assets\network\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = ["Networks", ['Home', 'Topology']];

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
            'id' => 'network-form',    
            'enableClientScript'=>false,
            'enableClientValidation' => false,
    ]);

    echo GridView::widget([
        'options' => ['class' => 'list'],
        'dataProvider' => $networks,
        'filterModel' => $searchModel,
        'id' => 'gridNetowrks',
        'layout' => "{items}{summary}{pager}",
        'columns' => array(
                [
                    'class'=>IcheckboxColumn::className(),
                    'name'=>'delete',         
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/edit_1.png',
                    'label' => '',
                    'title'=> Yii::t("topology", 'Update'),
                    'url' => '/topology/network/update',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Name'),
                    'value' => 'name',
                    'headerOptions'=>['style'=>'width: 25%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Urn'),
                    'value' => 'urn',
                    'headerOptions'=>['style'=>'width: 30%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Latitude'),
                    'value' => 'latitude',
                    'headerOptions'=>['style'=>'width: 8%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Longitude'),
                    'value' => 'longitude',
                    'headerOptions'=>['style'=>'width: 8%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Domain'),
                    'value' => function($net){
                        return $net->getDomain()->one()->name;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'domain_name',
                        ArrayHelper::map(
                            $allowedDomains, 'name', 'name'),
                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]        
                    ),
                    'headerOptions'=>['style'=>'width: 25%;'],
                ],
        ),
    ]);

    ActiveForm::end();
    
    ?>
    
    </div>
</div>
