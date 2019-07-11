<?php 
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\grid\GridButtons;
use meican\topology\models\Location;

\meican\topology\assets\location\Index::register($this);

$this->params['header'] = [Yii::t('topology', 'Locations'), [Yii::t('home', 'Home'), Yii::t('topology', 'Topology'), 'Locations']];

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

        Pjax::begin();

        echo Grid::widget([
            'dataProvider' => $locations,
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
                    'headerOptions'=>['style'=>'width: 20%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Latitude'),
                    'value' => 'lat',
                    'headerOptions'=>['style'=>'width: 20%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Longitude'),
                    'value' => 'lng',
                    'headerOptions'=>['style'=>'width: 20%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Domain'),
                    'value' => function($loc){
                        return $loc->getDomain()->one()->name;
                },
                    'filter' => Html::activeDropDownList($searchModel, 'domain_name',
                        ArrayHelper::map($domainsWithLocation, 'name', 'name'),
                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]	
                    ),
                    'headerOptions'=>['style'=>'width: 20%;'],
                ],
            ),
        ]);

        Pjax::end();

        ActiveForm::end();

        ?>
    </div>
</div>
