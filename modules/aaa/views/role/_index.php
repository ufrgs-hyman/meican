<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Url;

use meican\base\components\LinkColumn;
use meican\aaa\models\Group;
use meican\aaa\assets\role\CreateEditAsset;

CreateEditAsset::register($this);

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("aaa", "Roles"); ?></h3>
    </div>
    <div class="box-body">
        <div>
            <a id="add-role-grid-btn" class="btn btn-primary">Add</a>
            <a id="delete-role-grid-btn" class="btn btn-warning">Delete</a>
        </div><br>

        <?php

        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/aaa/role/delete'],
            'id' => 'role-grid-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);

        echo GridView::widget([
            'id' => 'role-grid',
            'dataProvider' => $rolesProvider,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                array(
                    'class'=>CheckboxColumn::className(),
                    'name'=>'delete',         
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                [
                    'format' => 'raw',
                    'value' => function ($model){
                        return '<a href="#">'.Html::img('@web/images/edit_1.png', ['class' => "edit-role-grid-btn"])."</a>";
                    },
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'attribute' => 'domain', 
                    'format' => 'raw',
                    'value' => function($model) {
                        $type = $model->getGroup()->type;
                        if($type == Group::TYPE_DOMAIN){
                            $dom = $model->getDomain();
                            if ($dom) return $dom->name;
                            return Yii::t("aaa", "Any");
                        }
                        else {
                            return Yii::t("aaa", "Any");
                        }
                     }
                ],
                [
                    'attribute' => '_groupRoleName',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getGroup()->name;
                    }
                ],
            ),
        ]);

        ActiveForm::end();
    
        ?>
    </div>
</div>

<?php 

Modal::begin([
    'id' => 'delete-role-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">Cancel</button> <button id="delete-role-btn" class="grid-btn btn btn-danger">Delete</button>',
]);

echo 'Do you want delete the selected items?';

Modal::end(); 

Modal::begin([
    'id' => 'error-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">Close</button>',
]);

echo 'Please, select a item.';

Modal::end(); 

Modal::begin([
    'id' => 'add-role-modal',
    'header' => 'Add Role',
    'footer' => '<button id="close-btn" class="btn btn-default">Close</button> <button id="save-role-btn" class="btn btn-primary">Save</button>',
]);

?>

<div id="add-role-form-wrapper"></div>

<?php 

Modal::end(); 

Modal::begin([
    'id' => 'edit-role-modal',
    'header' => 'Edit Role',
    'footer' => '<button id="close-btn" class="btn btn-default">Close</button> <button id="save-role-btn" class="btn btn-primary">Save</button>',
]); ?>

<div id="edit-role-form-wrapper"></div>

<?php Modal::end(); 

?>
