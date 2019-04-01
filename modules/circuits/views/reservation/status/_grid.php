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
use yii\helpers\Url;


function generateGrid($gridId, $data, $searchModel, $allowedDomains){
    return
        Grid::widget([
            'id' => $gridId,
            'dataProvider' => $data,
            'filterModel' => $searchModel,
            'layout' => "{items}{summary}{pager}",
            'columns' => array(
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{view}',
                    'buttons' => [
                            'view' => function ($url, $model) {
                                $url = str_replace('reservation', 'connection', $url);
                                return Html::a('<span class="fa fa-eye"></span>', $url);
                            }
                    ],
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Reservation Name'),
                    'attribute' => 'reservation_id',
                    'value' => 'reservation.name',
                    'headerOptions'=>['style'=>'width: 11%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Request Date'),
                    'attribute'=>'reservation_id',
                    'value' => 'reservation.date',
                    'format'=>'datetime',
                    'headerOptions'=>['style'=>'width: 10%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Start Time').' '.'<img style="width: 7px; height:11px;" src="'.Url::to('@web/images/sort_image.png').'" alt="Order by:"/>',
                    'attribute' => 'start',
                    'value' => 'start',
                    'format'=>'datetime',
                    'headerOptions'=>['style'=>'width: 10%;'],
                    'encodeLabel' => false,
                ],
                [
                    'label' => Yii::t('circuits', 'End Time').' '.'<img style="width: 7px; height:11px;" src="'.Url::to('@web/images/sort_image.png').'" alt="Order by:"/>',
                    'attribute' => 'finish',
                    'value' => 'finish',
                    'format'=>'datetime',
                    'headerOptions'=>['style'=>'width: 10%;'],
                    'encodeLabel' => false,
                ],
                [
                    'label' => Yii::t('circuits', 'Source Domain'),
                    'value' => function($model) {
                        return $model->getSourceDomain();
                    },      
                    'filter' => Html::activeDropDownList($searchModel, 'src_domain', 
                        ArrayHelper::map($allowedDomains, 'name', 'name'),
                        ['id'=>'reservationsearch-src_domain', 'class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
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
                        ['id'=>'reservationsearch-dst_domain','class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
                    ),
                    'headerOptions'=>['style'=>'width: 14%;'],
                ],
                [
                    'label' => Yii::t('circuits', 'Bandwidth').' '.'<img style="width: 7px; height:11px;" src="'.Url::to('@web/images/sort_image.png').'" alt="Order by:"/>',
                    'attribute' => 'bandwidth',
                    'value' => function($conn){
                        return $conn->bandwidth." Mbps";
                    },
                    'headerOptions'=>['style'=>'width: 10%;'],
                    'encodeLabel' => false,
                ],
                [
                    'label' => Yii::t('circuits', 'Requester'),
                    'value' => function($conn){
                        $reservation_id = $conn->reservation_id;
                        $res = Reservation::findOne(['id' => $reservation_id]);
                        $user_id = $res->request_user_id;
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

$this->registerJsFile(
    '@web/js/tabs.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

$tab = (isset($_COOKIE['lastTab']))? $_COOKIE['lastTab']:'#tabCurrent';

echo Tabs::widget([
    'items' => [
        [
            'label' => Yii::t('circuits','Current'),
            'content' => generateGrid($gridId.'current', $data['current'], $searchModel, $allowedDomains),
            'options' => ['id' => 'tabCurrent'],
            'active' => '#tabCurrent' == $tab
        ],
        [
            'label' => Yii::t('circuits','Future'),
            'content' => generateGrid($gridId.'future', $data['future'], $searchModel, $allowedDomains),
            'options' =>    ['id' => 'tabFuture'],
            'active' => '#tabFuture' == $tab 
        ],
        [
            'label' => Yii::t('circuits','Past'),
            'content' => generateGrid($gridId.'past', $data['past'], $searchModel, $allowedDomains),
            'options' => ['id' => 'tabPast'],
            'active' => '#tabPast' == $tab 
        ],
         
    ],
]);

?>