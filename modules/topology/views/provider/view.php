<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;
use meican\topology\assets\service\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = [$model->name, ['Home', 'Topology']];

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Informations"); ?></h3>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',               
                        'nsa',  
                        [            
                            'attribute'=> 'type',         
                            'value' => $model->getType(),
                        ], 
                        'latitude',
                        'longitude',
                        [            
                            'attribute'=> 'domain_id',         
                            'value' => $model->getDomainName(),
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Services"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                $form = ActiveForm::begin([
                    'method' => 'post',
                    'action' => ['/topology/service/delete'],
                    'id' => 'service-form',  
                    'enableClientScript'=>false,
                    'enableClientValidation' => false,
                ]);

                echo GridButtons::widget([
                    'addRoute'=>['/topology/service/create', 'id'=>$model->id]]).'<br>';

                echo Grid::widget([
                    'dataProvider' => $services,
                    'columns' => array(
                            array(
                                'class'=>IcheckboxColumn::className(),
                                'name'=>'delete',         
                                'multiple'=>false,
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ),
                            array(
                                'class'=> LinkColumn::className(),
                                'image'=>'/images/edit_1.png',
                                'label' => '',
                                'url' => '/topology/service/update',
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ),
                            [
                                'attribute' => 'type',
                                'value' => function($model) {
                                    return $model->getType();
                                }
                            ],
                            'url',
                        ),
                ]);

                ActiveForm::end();
            
                ?>
            </div>
        </div>
    </div>
</div>

        
