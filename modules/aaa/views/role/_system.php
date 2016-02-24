<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

use meican\base\grid\IcheckboxColumn;
use meican\base\components\LinkColumn;
use meican\aaa\models\Group;
use meican\aaa\assets\role\RoleSystemAsset;

RoleSystemAsset::register($this);

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("aaa", "Roles for System"); ?></h3>
    </div>
    <div class="box-body">
        <div>
            <a id="<?= $userId ?>" class="btn btn-primary add-system-btn"><?= Yii::t("aaa", "Add"); ?></a>
            <a id="delete-system-role" class="btn btn-warning delete-btn"><?= Yii::t("aaa", "Delete"); ?></a>
        </div><br>

        <?php

        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/aaa/role/delete'],
            'id' => 'system-role-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);

        echo GridView::widget([
            'id' => 'role-system-grid',
            'dataProvider' => $rolesProvider,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                array(
                    'class'=>IcheckboxColumn::className(),
                	'checkboxOptions' =>['class'=>'deleteSystem'],
                    'name'=>'delete',         
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                [
            		'class' => 'yii\grid\ActionColumn',
            		'template'=>'{edit}',
            		'contentOptions' => function($model){
            			return ['class'=>'btn-edit', 'id' => $model->id];
            		},
            		'buttons' => [
            			'edit' => function ($url, $model) {
            				return Html::a('<span class="fa fa-pencil"></span>', null);
            			}
            		],
            		'headerOptions'=>['style'=>'width: 2%;'],
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
    'id' => 'delete-role-system-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Cancel").'</button> <button id="delete-role-btn" class="grid-btn btn btn-danger">'.Yii::t("aaa", "Delete").'</button>',
]);

echo Yii::t("aaa", "Do you want delete the selected items?");

Modal::end(); 

Modal::begin([
    'id' => 'error-modal-system',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Close").'</button>',
]);

Modal::end(); 

Modal::begin([
    'id' => 'add-role-system-modal',
    'header' => Yii::t("aaa", "Add Role"),
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Cancel").'</button> <button id="save-role-btn" class="btn btn-primary">'.Yii::t("aaa", "Save").'</button>',
]);

?>

<div id="add-role-system-form-wrapper"></div>

<?php 

Modal::end(); 

Modal::begin([
    'id' => 'edit-role-system-modal',
    'header' => Yii::t("aaa", "Edit Role"),
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Cancel").'</button> <button id="save-role-btn" class="btn btn-primary">'.Yii::t("aaa", "Save").'</button>',
]); ?>

<div id="edit-role-system-form-wrapper"></div>

<?php Modal::end(); 

?>
