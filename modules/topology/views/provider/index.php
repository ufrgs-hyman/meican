<?php 
    use yii\grid\GridView;
    use yii\grid\CheckboxColumn;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    
    use meican\base\components\LinkColumn;
    use meican\topology\assets\provider\IndexAsset;
    
    IndexAsset::register($this);
?>

<h1><?= Yii::t('topology',"Providers"); ?></h1>
<?php
    $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['delete'],
            'id' => 'provider-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
    ])
?>
    
<?= $this->render('//formButtons'); ?>

<?=
    GridView::widget([
        'options' => ['class' => 'list'],
        'dataProvider' => $providers,
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
                    'url' => 'update',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                array(
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/eye.png',
                    'label' => '',
                    'title'=>Yii::t("topology",'Show details and services of this provider'),
                    'url' => 'view',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                'name',
                [
                    'attribute'=> 'type',
                    'value' => function($model) {
                        return $model->getType();
                    },
                ],
                'nsa',
                'latitude',
                'longitude',
            ),
    ]);
?>

<?php
    ActiveForm::end();
?>
