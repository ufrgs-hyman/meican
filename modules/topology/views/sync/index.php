<?php 
    use yii\grid\GridView;
    use yii\grid\CheckboxColumn;
    
    use app\components\LinkColumn;
    
    use yii\helpers\Html;
    
    use yii\widgets\ActiveForm;

    use yii\jui\ProgressBar;
    
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
                    'attribute'=> 'enabled',
                    'value' => function($model) {
                        return $model->enabled ? Yii::t("topology", "Enabled") : Yii::t("topology", "Disabled");
                    }
                ],
                [
                    'attribute'=> 'auto_apply',
                    'value' => function($model) {
                        return $model->auto_apply ? Yii::t("topology", "Enabled") : Yii::t("topology", "Disabled");
                    }
                ],
                "sync_date:datetime",
            ),
    ]);
?>

<?php
    ActiveForm::end();
?>

<div id="loading-dialog" hidden>

</div>
