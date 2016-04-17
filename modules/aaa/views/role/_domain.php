<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

use meican\base\grid\IcheckboxColumn;
use meican\base\components\LinkColumn;
use meican\aaa\models\Group;
use meican\aaa\assets\role\RoleDomainAsset;

RoleDomainAsset::register($this);

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t("aaa", "Roles for Domains"); ?></h3>
    </div>
    <div class="box-body">
        <div>
            <a id="<?= $userId ?>" class="btn btn-primary add-domain-btn"><?= Yii::t("aaa", "Add"); ?></a>
            <a id="delete-domain-role" class="btn btn-warning delete-btn"><?= Yii::t("aaa", "Delete"); ?></a>
        </div><br>

        <?php

        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/aaa/role/delete'],
            'id' => 'domain-role-form',  
            'enableClientScript'=>false,
            'enableClientValidation' => false,
        ]);

        echo GridView::widget([
            'id' => 'role-domain-grid',
            'dataProvider' => $rolesProvider,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                [
                    'class'=>IcheckboxColumn::className(),
                	'checkboxOptions' =>['class'=>'deleteDomain'],
                    'name'=>'delete',         
                    'multiple'=>false,
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
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
            ),
        ]);

        ActiveForm::end();
    
        ?>
    </div>
</div>

<?php 

Modal::begin([
    'id' => 'delete-role-domain-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Cancel").'</button> <button id="delete-role-btn" class="grid-btn btn btn-danger">'.Yii::t("aaa", "Delete").'</button>',
]);

echo Yii::t("aaa", "Do you want delete the selected items?");

Modal::end(); 

Modal::begin([
    'id' => 'error-modal-domain',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Close").'</button>',
]);

Modal::end(); 

Modal::begin([
    'id' => 'add-role-domain-modal',
    'header' => Yii::t("aaa", "Add Role"),
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Cancel").'</button> <button id="save-role-btn" class="btn btn-primary">'.Yii::t("aaa", "Save").'</button>',
]);

?>

<div id="add-role-domain-form-wrapper"></div>

<?php 

Modal::end(); 

Modal::begin([
    'id' => 'edit-role-domain-modal',
    'header' => Yii::t("aaa", "Edit Role"),
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("aaa", "Cancel").'</button> <button id="save-role-btn" class="btn btn-primary">'.Yii::t("aaa", "Save").'</button>',
]); ?>

<div id="edit-role-domain-form-wrapper"></div>

<?php Modal::end(); 

?>
