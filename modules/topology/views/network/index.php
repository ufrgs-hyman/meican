<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
   
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\grid\GridButtons;

\meican\topology\assets\network\Index::register($this);

$this->params['header'] = [Yii::t('topology', 'Networks'), [Yii::t('home', 'Home'), 
    Yii::t('topology', 'Topology'), 'Networks']];

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

    Pjax::begin();

    echo Grid::widget([
        'dataProvider' => $networks,
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
                    'headerOptions'=>['style'=>'width: 25%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Urn'),
                    'value' => 'urn',
                    'headerOptions'=>['style'=>'width: 30%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Longitude'),
                    'value' => 'longitude',
                    'headerOptions'=>['style'=>'width: 8%;'],
                ],
                [
                    'label' => Yii::t("topology", 'Latitude'),
                    'value' => 'latitude',
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

    Pjax::end();

    ActiveForm::end();
    
    ?>
    
    </div>
</div>
