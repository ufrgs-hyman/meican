<?php 
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\grid\GridButtons;

\meican\aaa\assets\group\Index::register($this);

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
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{update}',
                    'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="fa fa-pencil"></span>', $url);
                            }
                    ],
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
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