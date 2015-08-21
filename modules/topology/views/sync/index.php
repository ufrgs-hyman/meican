<?php 
    use yii\grid\GridView;
    use yii\grid\CheckboxColumn;
    
    use app\components\LinkColumn;
    
    use yii\helpers\Html;
    use yii\helpers\Url;
    
    use yii\widgets\ActiveForm;

    use app\modules\topology\assets\SyncAsset;
    
    SyncAsset::register($this);
?>

<h1><?= "Topology Synchronizer instances" ?></h1>
<?php
    $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['delete'],
            'id' => 'sync-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
    ])
?>
    
<?= $this->render('//formButtons'); ?>

<?=
    GridView::widget([
        'id'=> 'grid-sync',
        'options' => ['class' => 'list'],
        'layout' => "{items}",
        'dataProvider' => $items,
        'columns' => array(
                array(
                    'class'=>CheckboxColumn::className(),
                    'name'=>'delete',         
                    'checkboxOptions'=>[
                        'class'=>'deleteCheckbox',
                    ],
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
                    'attribute'=> 'type',
                    'value' => function($model) {
                        return $model->getType();
                    }
                ],
                [
                    'header' => 'Auto Sync',
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
                        return $model->auto_apply ? Yii::t("topology", "Enabled") : "";
                    }
                ],
                [
                    'header' => 'Last Sync',
                    'value' => function ($model){
                        return $model->getLastSyncDate();
                    },
                ],
            ),
    ]);
?>

<?php
    ActiveForm::end();
?>

<div id="loading-dialog" hidden>

</div>
