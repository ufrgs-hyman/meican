<?php 
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\base\grid\Grid;
use meican\base\grid\GridButtons;
use meican\topology\models\Change;

\meican\topology\assets\discovery\Task::register($this);

$this->params['header'] = [Yii::t('topology',"Discovery Task").' #'.$model->id, ['Home', 'Topology', 'Discovery', 'Task']];

?>
<data id="task-id" value="<?= $model->id; ?>"/>
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("topology", "Task info"); ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'started_at:datetime',  
                [
                    'label' => 'Rule name',
                    'value' => $model->getRule()->asArray()->one()['name']
                ],
                [
                    'label' => 'Target URL',
                    'value' => $model->getRule()->asArray()->one()['url']
                ],
                [
                    'label' => 'Topology type',
                    'value' => $model->getRule()->one()->getType()
                ],
                'status',  
                [
                    'label' => 'Total pending changes',
                    'value' => $model->getChanges()->where(['status'=>Change::STATUS_PENDING])->count()
                ],
                [
                    'label' => 'Total applied changes',
                    'value' => $model->getChanges()->where(['status'=>Change::STATUS_APPLIED])->count()
                ],
                [
                    'label' => 'Total failed changes',
                    'value' => $model->getChanges()->where(['status'=>Change::STATUS_FAILED])->count()
                ],
            ],
        ]); ?>
    </div>
</div>  

<div id="changes-box" class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("topology", "Discovered changes"); ?></h3>
        <div class="box-tools">
            <button class="btn btn-sm btn-default" id="apply-all">Apply all changes</button>
        </div>
    </div>
    <div class="box-body">
        <?php

        Pjax::begin([
            'id' => 'change-pjax',
        ]);

        echo Grid::widget([
            'id'=> 'change-grid',
            'filterModel' => $searchChange,
            'dataProvider' => $changeProvider,
            'columns' => array(
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{apply}',
                    'buttons' => [
                        'apply' => function ($url, $model) {
                            return $model->status == Change::STATUS_APPLIED ? "" : Html::a('<span class="fa fa-eye apply-btn"></span>', '#');
                        }
                    ],
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'attribute' => 'domain',
                    'filter' => Html::activeDropDownList($searchChange, 'domain', 
                        ArrayHelper::map(
                            Change::find()->where(['status'=>Change::STATUS_PENDING])->asArray()->select("domain")->distinct(true)->orderBy("domain ASC")->all(), 'domain', 'domain'),
                        ['class'=>'form-control','prompt' => 'any']),
                    'headerOptions'=>['style'=>'width: 16%;'],
                ],
                [
                    'header' => Yii::t('topology', 'Type'),
                    'value' => function($model) {
                        return $model->getType();
                    },
                    'filter' => Html::activeDropDownList($searchChange, 'type', 
                        ArrayHelper::map(
                            Change::getTypes(), 'id', 'name'),
                        ['class'=>'form-control','prompt' => 'any']),
                    'headerOptions'=>['style'=>'width: 7%;'],
                ],
                [
                    'attribute' => 'item_type',
                    'filter' => Html::activeDropDownList($searchChange, 'item_type', 
                        ArrayHelper::map(
                            Change::getItemTypes(), 'id', 'name'),
                        ['class'=>'form-control','prompt' => 'any']),
                    'value' => function($model) {
                        return $model->getItemType();
                    },
                    'headerOptions'=>['style'=>'width: 12%;'],
                ],
                [
                    'attribute' => 'data',
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getDetails();
                    },
                    'headerOptions'=>['style'=>'width: 30%;'],
                ],
                [
                    'header' => 'Status',
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        switch ($model->status) {
                            case Change::STATUS_FAILED:
                                return '<a href="#" title="'.$model->error.'"><span class="label label-danger">Failed</span></a>';
                            case Change::STATUS_APPLIED:
                                return '<span class="label label-success">Applied</span>';
                            case Change::STATUS_PENDING:
                                return '<span class="label label-primary">Pending</span>';
                            default:
                                return "";
                        }
                    },
                    'headerOptions'=>['style'=>'width: 5%;'],
                ],
            ),
        ]);

        Pjax::end();

        ?>
    </div>
    <div id="refresh-overlay" class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
    </div>
</div>  

