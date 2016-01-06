<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;

use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;
use meican\aaa\assets\group\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = ["Groups", ['Home', 'Groups']];

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
            'id' => 'group-form',
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);
    
        echo GridView::widget([
            'options' => ['class' => 'list'],
            'dataProvider' => $groups,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                array(
                    'class'=> CheckboxColumn::className(),
                    'name'=>'delete',
                    'checkboxOptions'=> [
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
                    'name',
                [
                    'label' => 'Tipo',
                    'value' => function($group){
                        return $group->getType();
                    }
                ],
                ),
        ]);
        
        ActiveForm::end();
        
        ?>
    </div>
</div>