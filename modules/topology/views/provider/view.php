<?php 

    use yii\grid\GridView;
    use yii\widgets\DetailView;
    use yii\helpers\Url;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\grid\CheckboxColumn;
    
    use meican\base\components\LinkColumn;
    use meican\topology\assets\service\IndexAsset;
    
    IndexAsset::register($this);
?>

<h1><?= Yii::t("topology", "Provider"); ?></h1>

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
        [            
            'attribute'=> 'domain_id',         
            'value' => $model->getDomainName(),
        ],
    ],
]); ?>

<h1><?= Yii::t("topology", "Services"); ?></h1>
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
    Html::a(Yii::t("init", "Add"), array('/topology/service/create', 'id'=>$model->id)); 
    ?>
    <?=
    Html::submitButton(Yii::t("init", "Delete"), ['id'=>'deleteButton']);
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
