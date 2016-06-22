<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\widgets\DetailView;

echo DetailView::widget([
    'id' => 'circuit-info',
    'model' => $conn,
    'attributes' => [
        'external_id',
        [                      
            'label' => 'Name',
            'value' => $conn->getName()                            
        ],
        [                      
            'attribute' => 'bandwidth',
            'format' => 'raw',
            'value' => '<data id="info-bandwidth" value="'.$conn->bandwidth.'"></data>'.$conn->bandwidth." Mbps"                            
        ],
        [                      
            'attribute' => 'start',
            'format' => 'raw',
            'value' => '<data id="info-start" value="'.Yii::$app->formatter->asDatetime($conn->start).'"></data>'.Yii::$app->formatter->asDatetime($conn->start)
        ],                        
        [                      
            'attribute' => 'finish',
            'format' => 'raw',
            'value' => '<data id="info-end" value="'.Yii::$app->formatter->asDatetime($conn->finish).'"></data>'.Yii::$app->formatter->asDatetime($conn->finish)
        ],  
        'version',
        'type',
        [
            'label' => 'Provider',
            'value' => 'RNP Aggregator'
        ]
    ],
]); 

?>