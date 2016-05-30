<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\grid\GridButtons;
use meican\topology\models\Device;

\meican\topology\assets\device\Index::register($this);

$this->params['header'] = [Yii::t('topology', 'Devices'), [Yii::t('home', 'Home'), Yii::t('topology', 'Topology'), 'Devices']];

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

        echo Grid::widget([
        	'tableOptions' => [
        		'class' => 'table table-condensed',
        	],
            'dataProvider' => $devices,
            'filterModel' => $searchModel,
            'id' => 'gridNetowrks',
            'columns' => array(
                [
                    'class'=>IcheckboxColumn::className(),
                    'name'=>'delete',         
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
            	[
            		'class' => 'yii\grid\ActionColumn',
            		'template'=>'{update}',
            		'buttons' => [
            				'update' => function ($url, $model) {
            					return Html::a('<span class="fa fa-pencil"></span>', $url);
            				}
            		],
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
                        return Html::a($dev->getPorts()->count(), ['/topology/port/index', 'id' => $dev->domain_id]);
                    },
                    'headerOptions'=>['style'=>'width: 4%;'],
                ],
            ),
        ]);

        ActiveForm::end();

        ?>
    </div>
</div>
