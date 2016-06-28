<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

use meican\base\grid\Grid;
use meican\base\grid\GridButtons;
use meican\base\grid\IcheckboxColumn;

\meican\topology\assets\discovery\Index::register($this);

$this->params['header'] = [Yii::t('topology',"Discovery"), ['Home', 'Topology', 'Discovery']];

?>

<div class="row">
    
    <div class="col-md-8">
        
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Rules"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                echo GridButtons::widget(['addRoute'=>'create-rule']).'<br>';
            
                $form = ActiveForm::begin([
                    'method' => 'post',
                    'action' => ['delete-rule'],
                    'id' => 'rule-form',  
                    'enableClientScript'=>false,
                    'enableClientValidation' => false,
                ]);

                Pjax::begin(['id'=>'rule-pjax']);
            
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
                        "name",
                        [
                            'attribute'=> 'auto_apply',
                            'value' => function($model) {
                                return $model->auto_apply ? Yii::t("topology", "Automatic") : Yii::t("topology", "Manual");
                            }
                        ],
                        [
                            'header' => 'Scheduled',
                            'value' => function($model) {
                                return $model->isScheduled() ? Yii::t("topology", "Yes") : Yii::t("topology", "No");
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template'=>'{start}',
                            'buttons' => [
                                    'start' => function ($url, $model) {
                                        return '<button class="btn btn-sm btn-primary start-discovery"><span class="fa fa-compass "></span> Start Discovery</button>';
                                    }
                            ],
                            'headerOptions'=>['style'=>'width: 2%;'],
                        ],
                    ),
                ]);

                Pjax::end();    

                ActiveForm::end();

                ?>
            </div>
        </div>  
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Last tasks"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                Pjax::begin(['id'=>'task-pjax']);
            
                echo Grid::widget([
                    'id'=> 'search-grid',
                    'dataProvider' => $taskProvider,
                    'columns' => array(
                        'started_at:datetime',
                        [
                            'header' => Yii::t("topology", "Rule"),
                            'value' => function ($model){
                                return $model->getRule()->one()->name;
                            },
                        ],
                        'status',
                        [
                            'header' => Yii::t("topology", "Discovered changes"),
                            'format' => 'raw',
                            'value' => function ($model){
                                return Html::a('<span class="fa fa-eye"></span> ', ['task', 'id'=>$model->id]).$model->getChanges()->count();
                            },
                        ],
                    ),
                ]);

                Pjax::end();    

                ?>
            </div>
        </div>  
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Change history"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                Pjax::begin(['id'=>'change-pjax']);

                echo Grid::widget([
                    'id'=> 'change-grid',
                    'dataProvider' => $changeProvider,
                    'columns' => array(
                        'applied_at',
                        'domain',
                    ),
                ]);

                Pjax::end();

                ?>
            </div>
        </div>  
    </div>
</div>  