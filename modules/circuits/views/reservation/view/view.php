<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\Html;

use meican\base\grid\Grid;
use meican\base\widgets\DetailView;

\meican\circuits\assets\reservation\View::register($this);

$this->params['header'] = [Yii::t('circuits',"Reservation Details"), ['Home', 'Circuits']];

?>

<div class="row">
    <div class="col-md-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("circuits", "Circuits"); ?></h3>
            </div>
            <div class="box-body">
                <?php

                echo Grid::widget([
                    'dataProvider' => $connDataProvider,
                    'columns' => [
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template'=>'{view}',
                            'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<span class="fa fa-eye"></span>', ['connection/view','id'=>$model->id]);
                                    }
                            ],
                            'headerOptions'=>['style'=>'width: 2%;'],
                        ],
                        'external_id',
                        'start:datetime',
                        'finish:datetime',
                        'status',
                        'dataplane_status',
                        'auth_status'],
                    ]);
                ?>
            </div>
        </div> 
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("circuits", "Reservation Info"); ?></h3>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $reservation,
                    'attributes' => [
                        'id',
                        'name',               
                        'bandwidth',
                        'requester_nsa',
                        'provider_nsa',
                        //'request_user_id',
                    ],
                ]); ?>
            </div>
        </div>    
    </div>
</div> 
 
