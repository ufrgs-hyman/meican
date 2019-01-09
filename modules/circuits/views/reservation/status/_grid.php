<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use meican\topology\models\Domain;
use meican\aaa\models\User;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\base\grid\Grid;
use yii\bootstrap\Tabs;

function generateGrid($gridId, $data, $searchModel, $allowedDomains){
    return
        Grid::widget([
            'id' => $gridId,
            'dataProvider' => $data,
            'filterModel' => $searchModel,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                [
                    'class' => 'yii\grid\ActionColumn',                                 //******************* VERIFICAR E COLOCAR AS TRADUÇÕES ***********
                    'template'=>'{view}',
                    'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="fa fa-eye"></span>', $url);
                            }
                    ],
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'label' => 'Reservation Name',
                    'attribute' => 'reservation_id',
                    'value' => 'reservation.name',
                    'headerOptions'=>['style'=>'width: 11%;'],
                ],
                [
                    'label' => 'Request',
                    'attribute'=>'reservation_id',
                    'value' => 'reservation.date',
                    'format'=>'datetime',
                    'headerOptions'=>['style'=>'width: 10%;'],
                ],
                [
                    'label' => 'Start Time',
                    'attribute' => 'start',
                    'value' => 'start',
                    'format'=>'datetime',
                    'headerOptions'=>['style'=>'width: 10%;'],
                ],
                [
                    'label' => 'End Time',
                    'attribute' => 'finish',
                    'value' => 'finish',
                    'format'=>'datetime',
                    'headerOptions'=>['style'=>'width: 10%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Source Domain'),
                    'value' => function($model) {
                        return $model->getSourceDomain();
                    },      
                    'filter' => Html::activeDropDownList($searchModel, 'src_domain', 
                        ArrayHelper::map($allowedDomains, 'name', 'name'),
                        ['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
                    ),
                    'headerOptions'=>['style'=>'width: 14%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Destination Domain'),
                    'value' => function($model) {
                        return $model->getDestinationDomain();
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'dst_domain', 
                        ArrayHelper::map($allowedDomains, 'name', 'name'),
                        ['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
                    ),
                    'headerOptions'=>['style'=>'width: 14%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Bandwidth'),
                    'value' => function($conn){
                        return $conn->bandwidth." Mbps";
                    },
                    'headerOptions'=>['style'=>'width: 9%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Requester'),
                    'value' => function($conn){
                        $reservation_id = $conn->reservation_id;
                        $user_id = Reservation::findOne(['id' => $reservation_id]);
                        $user = User::findOne(['id' => $user_id]);
                        if($user)
                            return $user->name;
                        return null;
                    },
                    'headerOptions'=>['style'=>'width: 12%;'],
                ],
                [
                    'label' => Yii::t('circuits', "Status"),
                    'format' => 'html',
                    'value' => function($model) {
                        $msg = $model->getStatus().", ".$model->getAuthStatus().", ".$model->getDataStatus();

                        return $msg;
                    },
                    'headerOptions'=>['style'=>'width: 28%;'],
                ],
            ),
        ]);
};

echo Tabs::widget([
    'items' => [
        [
            'label' => 'Current',
            'content' => generateGrid($gridId, $data['current'], $searchModel, $allowedDomains),
            'options' => ['id' => 'tabCurrent'],

        ],
        [
            'label' => 'Future',
            'content' => generateGrid($gridId, $data['future'], $searchModel, $allowedDomains),
            'options' => ['id' => 'tabFuture'],
        ],
         [
            'label' => 'Past',
            'content' => generateGrid($gridId, $data['past'], $searchModel, $allowedDomains),
            'options' => ['id' => 'tabPast'],
        ],
    ],
]);

?>
