<?php 

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\CheckboxColumn;

use meican\aaa\models\Group;
use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;
//use meican\topology\assets\service\IndexAsset;

//IndexAsset::register($this);

$this->params['header'] = [$model->name, ['Home', 'Users']];

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("aaa", "Profile"); ?></h3>
            </div>
            <div class="box-body">                
                <?= $this->render("_profile", ['model'=>$model]); ?>
            </div>
            <div class="box-footer">
                <a href="<?= Url::to(["update", 'id'=>$model->id]) ?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a>
            </div>   
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("aaa", "Roles"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                $form = ActiveForm::begin([
                    'method' => 'post',
                    'action' => ['/topology/service/delete'],
                    'id' => 'role-form',  
                    'enableClientScript'=>false,
                    'enableClientValidation' => false,
                ]);

                echo GridButtons::widget([
                    'addRoute'=>['/aaa/role/create', 'id'=>$model->id]]).'<br>';

                echo GridView::widget([
                    'dataProvider' => $rolesProvider,
                    'layout' => "{items}{summary}{pager}",
                    'columns' => array(
                        array(
                            'class'=>CheckboxColumn::className(),
                            'name'=>'delete',         
                            'multiple'=>false,
                            'headerOptions'=>['style'=>'width: 2%;'],
                        ),
                        array(
                            'class'=> LinkColumn::className(),
                            'image'=>'/images/edit_1.png',
                            'label' => '',
                            'url' => '/aaa/role/update',
                            'headerOptions'=>['style'=>'width: 2%;'],
                        ),
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
    </div>
</div>

        
