<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use meican\base\grid\IcheckboxColumn;
use meican\base\components\LinkColumn;
use meican\base\widgets\GridButtons;
use meican\topology\assets\discovery\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = [Yii::t('topology',"Discovery"), ['Home', 'Topology']];

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Last changes"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                echo GridView::widget([
                    'id'=> 'change-grid',
                    'layout' => "{items}{summary}{pager}",
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
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Last tasks"); ?></h3>
            </div>
            <div class="box-body">
                <?php
            
                echo GridView::widget([
                    'id'=> 'search-grid',
                    'layout' => "{items}{summary}{pager}",
                    'dataProvider' => $taskProvider,
                    'columns' => array(
                            [
                                'format' => 'raw',
                                'value' => function ($model){
                                    return '<a href="'.Url::toRoute(["/topology/discovery/task",'id'=>$model->id]).'">'.
                                        Html::img('@web/images/eye.png')."</a>";
                                },
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
                            [
                                'header' => Yii::t("topology", "Rule"),
                                'value' => function ($model){
                                    return $model->getRule()->one()->name;
                                },
                            ],
                            'started_at',
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
            
                echo GridView::widget([
                    'id'=> 'rule-grid',
                    'layout' => "{items}{summary}{pager}",
                    'dataProvider' => $ruleProvider,
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
                                'url' => 'update',
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ),
                            [
                                'format' => 'raw',
                                'value' => function ($model){
                                    return '<a href="'.Url::toRoute(["/topology/discovery/view",'id'=>$model->id]).'">'.
                                        Html::img('@web/images/eye.png')."</a>";
                                },
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
                            [
                                'format' => 'raw',
                                'value' => function ($model){
                                    return '<a href="#">'.Html::img('@web/images/arrow_circle_double.png', ['class' => "sync-button"])."</a>";
                                },
                                'headerOptions'=>['style'=>'width: 2%;'],
                            ],
                            "name",
                            [
                                'header' => Yii::t("topology", "Auto Discovery"),
                                'value' => function ($model){
                                    return "Scheduled, on Notification";
                                },
                            ],
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
</div>  