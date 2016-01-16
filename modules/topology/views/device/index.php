<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use meican\base\grid\IcheckboxColumn;
use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;
use meican\topology\assets\device\IndexAsset;
use meican\topology\models\Device;

IndexAsset::register($this);

$this->params['header'] = ["Devices", ['Home', 'Topology']];

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
            'id' => 'device-form',	
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);

        echo GridView::widget([
            'dataProvider' => $devices,
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
                    'url' => '/topology/device/update',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Name'),
                    'value' => 'name',
                    'headerOptions'=>['style'=>'width: 24%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Ip'),
                    'value' => 'ip',
                    'headerOptions'=>['style'=>'width: 8%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Address'),
                    'value' => 'address',
                    'headerOptions'=>['style'=>'width: 10%;'],
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
                    'label' => Yii::t("topology", 'Node'),
                    'value' => 'node',
                    'headerOptions'=>['style'=>'width: 9%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Domain'),
                    'value' => function($dev){
                        return $dev->getDomain()->one()->name;
                },
                    'filter' => Html::activeDropDownList($searchModel, 'domain_name',
                        ArrayHelper::map($allowedDomains, 'name', 'name'),
                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]	
                    ),
                    'headerOptions'=>['style'=>'width: 23%;'],
                ],
                [
                    'format' => 'html',
                    'label' => Yii::t('topology', '#EndPoints'),
                    'value' => function($dev){
                        return Html::a($dev->getPorts()->count(), ['/topology/port', 'id' => $dev->domain_id]);
                    },
                    'headerOptions'=>['style'=>'width: 4%;'],
                ],
            ),
        ]);

        ActiveForm::end();

        ?>
    </div>
</div>
