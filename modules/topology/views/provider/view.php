<?php 

    use yii\grid\GridView;
    use yii\widgets\DetailView;
    use yii\helpers\Url;

    use yii\grid\CheckboxColumn;
    
    use app\components\LinkColumn;
    
    use yii\helpers\Html;
    
    use yii\widgets\ActiveForm;
?>

<h1><?= Yii::t("topology", "Provider details"); ?></h1>

<?= DetailView::widget([
    'options' => ['class' => 'list'],
    'model' => $model,
    'attributes' => [
        'name',               
        'nsa',   
        'type', 
        'latitude',
        'longitude',
    ],
]); ?>

<h1><?= "Services" ?></h1>
<?php
    $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['delete'],
            'id' => 'service-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
    ])
?>
    
<?= $this->render('//formButtons'); ?>

<?=
    GridView::widget([
        'options' => ['class' => 'list'],
        'layout' => "{items}",
        'dataProvider' => $services,
        'columns' => array(
                array(
                    'class'=>CheckboxColumn::className(),
                    'name'=>'delete',         
                    'checkboxOptions'=>[
                        'class'=>'deleteCheckbox',
                    ],
                    'multiple'=>false,
                    'contentOptions'=>['style'=>'width: 15px;'],
                ),
                array(
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/edit_1.png',
                    'label' => '',
                    'url' => 'update',
                    'contentOptions'=>['style'=>'width: 15px;'],
                ),
                'type',
                'url',
            ),
    ]);
?>

<?php
    ActiveForm::end();
?>
