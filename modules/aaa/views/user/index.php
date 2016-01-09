<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\helpers\ArrayHelper;

use meican\base\widgets\GridButtons;
use meican\aaa\models\UserDomainRole;
use meican\base\components\LinkColumn;
use meican\aaa\assets\user\IndexAsset;

IndexAsset::register($this);

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
    
        echo GridView::widget([
            'options' => ['class' => 'list'],
            'dataProvider' => $users,
            'filterModel' => $searchModel,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                [
                    'class'=> CheckboxColumn::className(),
                    'name'=>'delete',
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/edit_1.png',
                    'url' => 'update',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                array(
                    'class'=> LinkColumn::className(),
                    'image'=>'/images/eye.png',
                    'label' => '',
                    'title'=>Yii::t("aaa",'Show details'),
                    'url' => 'view',
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                [
                    'label' => Yii::t('aaa', 'User'),
                    'value' => 'login',
                    'headerOptions'=>['style'=>'width: 39%;'],
                ],
                [
                    'label' => Yii::t('aaa', 'Name'),
                    'value' => 'name',
                    'headerOptions'=>['style'=>'width: 39%;'],
                ],
                [
                    'label' => Yii::t('aaa', '#Roles in Domain'),
                    'value' => 'numRoles',
                    'filter' => Html::activeDropDownList($searchModel, 'domain',
                        ArrayHelper::map($domains, 'name', 'name'),
                        ['id'=>'dropdown', 'class'=>'form-control','prompt' => Yii::t("bpm", 'any')]),
                    'headerOptions'=>['style'=>'width: 16%;'],
                ],
                ),
            ]);
            
        ?>

        <?php
            ActiveForm::end();
        ?>
    </div>
</div>