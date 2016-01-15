<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\base\components\LinkColumn;
use meican\base\widgets\GridButtons;
use meican\topology\models\Change;
use meican\topology\assets\sync\ChangeAsset;

ChangeAsset::register($this);

$this->params['header'] = [Yii::t('topology',"Discovery Task"), ['Home', 'Topology', 'Discovery', 'Task']];

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("topology", "Info"); ?></h3>
    </div>
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'started_at',               
                'status',  
            ],
        ]); ?>
    </div>
</div>  

<div id="changes-box" class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("topology", "Discovered changes"); ?></h3>
    </div>
    <div class="box-body">
        <?php

        Pjax::begin([
            'id' => 'change-pjax',
            'enablePushState' => false,
        ]);

        echo GridView::widget([
            'id'=> 'change-grid',
            'layout' => "{items}{summary}{pager}",
            'filterModel' => $searchChange,
            'dataProvider' => $changeProvider,
            'columns' => array(
                [
                    'format' => 'raw',
                    'value' => function ($model){
                        return $model->status == Change::STATUS_APPLIED ? "" : '<a href="#">'.Html::img('@web/images/ok.png', ['class' => "apply-button"])."</a>";
                    },
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
                    'headerOptions'=>['style'=>'width: 9%;'],
                ],
                [
                    'header' => Yii::t('topology', 'Parent'),
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getParentInfo();
                    },
                    'headerOptions'=>['style'=>'width: 16%;'],
                ],
                [
                    'attribute' => 'data',
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getDetails();
                    },
                    'headerOptions'=>['style'=>'width: 45%;'],
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

