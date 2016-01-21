<?php 
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
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
    
        echo Grid::widget([
            'dataProvider' => $groups,
            'columns' => array(
                array(
                    'class'=> IcheckboxColumn::className(),
                    'name'=>'delete',
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