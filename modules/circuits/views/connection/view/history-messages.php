<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;

use meican\base\grid\Grid;

echo Grid::widget([
    'id'=> 'message-grid',
    'summary' => false,
    'dataProvider' => $messages,
    'columns' => array(
        [
            'header' => 'Messages',
            'format' => 'raw',
            'value' => function ($model){
                return "<b>".$model->getTypeLabel()."</b> by ".$model->getAuthor().' at '.
                    Yii::$app->formatter->asDatetime($model->created_at).
                    '<br>'.Html::textarea('message', $model->message, ['rows'=> 20, 'cols'=> 138, 'readOnly' => true]);
            },
        ],
    ),
]);

?>