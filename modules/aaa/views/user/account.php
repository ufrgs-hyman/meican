<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\CheckboxColumn;

use meican\aaa\models\Group;
use meican\base\widgets\GridButtons;
use meican\base\components\LinkColumn;

$this->params['header'] = ["My account", ['Home', 'My account']];

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
                <a href="<?= Url::to("update-my-account") ?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a>
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

                echo GridView::widget([
                    'dataProvider' => $rolesProvider,
                    'layout' => "{items}{summary}{pager}",
                    'columns' => array(
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

                ?>
            </div>
        </div>
    </div>
</div>
