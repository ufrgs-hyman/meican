<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use meican\base\grid\Grid;

echo Grid::widget([
    'id'=> 'history-grid',
    'dataProvider' => $history,
    'columns' => array(
        [
            'attribute' => 'type',
            'value' => function ($model){
                return $model->getTypeLabel();
            },
        ],
        [
            'header' => Yii::t("circuits", 'Info'),
            'format' => 'raw',
            'value' => function ($model){
                if($model->message) {
                    return '<a class="event-message" href="#"><span class="fa fa-file-text"></span></a>';
                } elseif ($model->data) {
                    $tmp = json_decode($model->data);
                    return (isset($tmp->bandwidth) ? '<span class="fa fa-tachometer"></span> '.$tmp->bandwidth.' Mbps<br>' : '')
                        .(isset($tmp->start) ? '<span class="fa fa-clock-o"></span> Start at '.Yii::$app->formatter->asDatetime($tmp->start).'<br>' : '')
                        .(isset($tmp->end) ? '<span class="fa fa-clock-o"></span> End at '.Yii::$app->formatter->asDatetime($tmp->end).'<br>' : '');
                } 
                return '';
            },
        ],
        [
            'attribute' => 'author_id',
            'format' => 'raw',
            'value' => function ($model){
                return $model->getAuthor();
            },
        ],
    ),
]);

?>