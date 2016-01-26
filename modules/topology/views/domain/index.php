<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\ActiveForm;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\widgets\GridButtons;

use yii\helpers\Html;
use yii\helpers\Url;

use meican\topology\assets\domain\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = [Yii::t('topology', 'Domains'), [Yii::t('home', 'Home'), Yii::t('topology', 'Topology')]];

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
            'id' => 'domain-form',
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);
        
        echo Grid::widget([
        	'tableOptions' => [
        		'class' => 'table table-condensed',
        	],
            'dataProvider' => $domains,
            'id' => 'grid',
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
                    'label' => Yii::t('topology', 'Name'),
                    'value' => 'name',
                    'headerOptions'=>['style'=>'width: 50%;'],
                ],
                [
                    'label' => Yii::t('topology', 'Default Policy'),
                    'value' => function($dom){
                        return $dom->getPolicy();
                    },
                    'headerOptions'=>['style'=>'width: 46%;'],
                ],
            ),
        ]); 

        ActiveForm::end();

        ?>
    </div>
</div>
