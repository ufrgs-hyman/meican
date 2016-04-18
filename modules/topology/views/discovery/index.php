<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use meican\base\grid\Grid;
use meican\base\grid\GridButtons;
use meican\base\grid\IcheckboxColumn;

\meican\topology\assets\discovery\Index::register($this);

$this->params['header'] = [Yii::t('topology',"Discovery"), ['Home', 'Topology']];

?>

<div class="row">
    
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Last tasks"); ?></h3>
            </div>
            <div class="box-body">
                <?php
            
                echo Grid::widget([
                    'id'=> 'search-grid',
                    'dataProvider' => $taskProvider,
                    'columns' => array(
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template'=>'{task}',
                                'buttons' => [
                                        'task' => function ($url, $model) {
                                            return Html::a('<span class="fa fa-eye"></span>', $url);
                                        }
                                ],
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
                            [
                                'header' => Yii::t("topology", "Rule"),
                                'value' => function ($model){
                                    return $model->getRule()->one()->name;
                                },
                            ],
                            'started_at:datetime',
                            'status',
                            [
                                'header' => Yii::t("topology", "Discovered changes"),
                                'value' => function ($model){
                                    return $model->getChanges()->count();
                                },
                            ],
                        ),
                    ]);
                ?>
            </div>
        </div>  
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Rules"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                echo GridButtons::widget(['addRoute'=>'create-rule']).'<br>';
            
                $form = ActiveForm::begin([
                    'method' => 'post',
                    'action' => ['delete'],
                    'id' => 'rule-form',  
                    'enableClientScript'=>false,
                    'enableClientValidation' => false,
                ]);
            
                echo Grid::widget([
                    'id'=> 'rule-grid',
                    'dataProvider' => $ruleProvider,
                    'columns' => array(
                            array(
                                'class'=>IcheckboxColumn::className(),
                                'name'=>'delete',         
                                'multiple'=>false,
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ),
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template'=>'{update-rule}',
                                'buttons' => [
                                        'update-rule' => function ($url, $model) {
                                            return Html::a('<span class="fa fa-pencil"></span>', $url);
                                        }
                                ],
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template'=>'{execute}',
                                'buttons' => [
                                        'execute' => function ($url, $model) {
                                            return Html::a('<span class="fa fa-compass execute-discovery"></span>', '#');
                                        }
                                ],
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
                            "name",
                            [
                                'attribute'=> 'auto_apply',
                                'value' => function($model) {
                                    return $model->auto_apply ? Yii::t("topology", "Automatic") : Yii::t("topology", "Manual");
                                }
                            ],
                        ),
                    ]);

                    ActiveForm::end();
                ?>
            </div>
        </div>  
    </div>
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Last applied changes"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                echo Grid::widget([
                    'id'=> 'change-grid',
                    'dataProvider' => $changeProvider,
                    'columns' => array(
                        [
                            'header' => Yii::t("topology", "Discovered at"),
                            'value' => function ($model){
                                return $model->getTask()->one()->started_at;
                            },
                        ],
                        'domain',
                        [
                            'header' => Yii::t("topology", "Total"),
                            'value' => function ($model){
                                return $model->count;
                            },
                        ],
                    ),
                ]);

                ?>
            </div>
        </div>  
    </div>
</div>  