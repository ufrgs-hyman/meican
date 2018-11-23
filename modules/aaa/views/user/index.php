<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

use meican\base\grid\Grid;
use meican\base\grid\IcheckboxColumn;
use meican\base\grid\GridButtons;
use meican\aaa\models\UserDomainRole;

\meican\aaa\assets\user\Index::register($this);

$this->params['header'] = ["Users", ['Home', 'Users']];

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
            'id' => 'user-grid-form',
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);
        

        echo Grid::widget([
            'dataProvider' => $users,
            'filterModel' => $searchModel,
            'columns' => array(
                [
                    'class'=> IcheckboxColumn::className(),
                    'name'=>'delete',
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{view}',
                    'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="fa fa-eye"></span>', $url);
                            }
                    ],
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'label' => Yii::t('aaa', 'User'),
                    'value' => 'login',
                    'headerOptions'=>['style'=>'width: 47%;'],
                ],
                [
                    'label' => Yii::t('aaa', 'Name'),
                    'value' => 'name',
                    'headerOptions'=>['style'=>'width: 47%;'],
                ],
            ),
        ]);  
        
        ActiveForm::end();

        ?>
         
    </div>
</div>