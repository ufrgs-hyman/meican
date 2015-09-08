<?php 

    use yii\grid\GridView;
    use yii\widgets\DetailView;
    use yii\helpers\Url;

    use yii\grid\CheckboxColumn;
    
    use app\components\LinkColumn;
    
    use yii\helpers\Html;
    
    use yii\widgets\ActiveForm;

    use app\modules\topology\assets\ServiceAsset;
    
    ServiceAsset::register($this);
?>

<h1><?= Yii::t("topology", "Provider details"); ?></h1>

<?= DetailView::widget([
    'options' => ['class' => 'list'],
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
    ],
]); ?>

<h1><?= "Services" ?></h1>
<?php
    $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/topology/service/delete'],
            'id' => 'service-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
    ])
?>
    
<div class="controls">
    <?=
    Html::a('Add', array('/topology/service/create', 'id'=>$model->id)); 
    ?>
    <?=
    Html::submitButton('Delete', ['id'=>'deleteButton']);
    ?>
</div>

<div style="clear: both"></div>

<?=
    GridView::widget([
        'options' => ['class' => 'list'],
        'dataProvider' => $services,
    	'layout' => "{items}{summary}{pager}",
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
?>

<?php
    ActiveForm::end();
?>
