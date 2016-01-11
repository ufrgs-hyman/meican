<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use meican\base\components\LinkColumn;
use meican\base\widgets\GridButtons;
use meican\topology\assets\discovery\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = [Yii::t('topology',"Discovery"), ['Home', 'Topology']];

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
            'id' => 'rule-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);
    
        echo GridView::widget([
            'id'=> 'rule-grid',
            'layout' => "{items}{summary}{pager}",
            'dataProvider' => $data,
            'columns' => array(
                    array(
                        'class'=>CheckboxColumn::className(),
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
                            $event = $model->getEvents()->asArray()->one();
                            return '<a href="'.Url::toRoute(["/topology/change/pending",'eventId'=>$event ? $event['id'] : '']).'">'.
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
                        'attribute'=> 'protocol',
                        'value' => function($model) {
                            return $model->getProtocol();
                        }
                    ],
                    [
                        'attribute'=> 'type',
                        'value' => function($model) {
                            return $model->getType();
                        }
                    ],
                    [
                        'header' => Yii::t("topology", "Autosync by recurrence"),
                        'value' => function ($model){
                            return $model->isAutoSyncEnabled() ? Yii::t("topology", "Enabled") : "";
                        },
                    ],
                    [
                        'attribute'=> 'subscription_id',
                        'value' => function($model) {
                            return $model->subscription_id ? Yii::t("topology", "Enabled") : "";
                        }
                    ],
                    [
                        'attribute'=> 'auto_apply',
                        'value' => function($model) {
                            return $model->auto_apply ? Yii::t("topology", "Automatically") : Yii::t("topology", "Manually");
                        }
                    ],
                    [
                        'header' =>Yii::t("topology", "Last sync"),
                        'format' => 'datetime',
                        'value' => function ($model){
                            return $model->getLastSyncDate();
                        },
                    ],
                ),
            ]);

            ActiveForm::end();
        ?>
    </div>
</div>
