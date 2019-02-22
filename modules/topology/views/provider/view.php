<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\grid\GridButtons;
use meican\base\widgets\DetailView;

\meican\topology\assets\service\Index::register($this);

$this->params['header'] = [$model->name, ['Home', 'Topology', 'Providers', $model->name]];

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Details"); ?></h3>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'name',               
                        'nsa',  
                        [            
                            'attribute'=> 'type',         
                            'value' => $model->getType(),
                        ], 
                        'longitude',
                        'latitude',
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
                <div class="box-tools">
                    <?= GridButtons::widget([
                        'size' => 'small',
                        'addRoute'=>['/topology/service/create', 'id'=>$model->id]]); ?>
                </div>
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

                echo Grid::widget([
                    'dataProvider' => $services,
                    'columns' => array(
                            array(
                                'class'=>IcheckboxColumn::className(),
                                'name'=>'delete',         
                                'multiple'=>false,
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ),
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template'=>'{update}',
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        return Html::a('<span class="fa fa-pencil"></span>', Url::to(['/topology/service/update', 'id'=>$model->id]));
                                    }
                                ],
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
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

        
