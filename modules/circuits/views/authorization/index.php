<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use app\components\LinkColumn;
	use yii\helpers\Html;
	use yii\i18n\Formatter;
	use yii\widgets\ActiveForm;
	use app\models\Reservation;
	use app\models\Connection;
	use app\models\ConnectionPath;
	use app\models\Urn;
	use app\models\User;
	
	use yii\data\ArrayDataProvider;
?>

<h1><?= Yii::t('circuits', 'Pending Authorization'); ?></h1>

	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => new ArrayDataProvider([
    			'allModels' => $array,
    			'sort' => false,
    			'pagination' => false,
    		]),
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridInfo',
			'layout' => "{items}",
			'columns' => array(
					[
						'label' => Yii::t('circuits', 'Reply request as '),
						'value' => 'domain',
						'contentOptions'=>['style'=>'font-weight: bold;'],
						'headerOptions'=>['style'=>'width: 23%;'],
					],
	        		[
		        		'label' => Yii::t('circuits', 'Source Domain'),
		        		'value' => function($aut){
		        			$connection_id = Connection::find()->where(['reservation_id' => $aut->id])->one()->id;
		        			$path = ConnectionPath::findOne(['conn_id' => $connection_id, 'path_order' => 0]);
				        	if($path){
				        		return $path->domain;
				        	}
				        	else{
				        		return Yii::t('circuits', 'deleted');
				        	};
		        		},
		        		'headerOptions'=>['style'=>'width: 21%;'],
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Destination Domain'),
	        			'value' => function($aut){
	        				$connection_id = Connection::find()->where(['reservation_id' => $aut->id])->one()->id;
		        			$path = ConnectionPath::find()->where(['conn_id' => $connection_id])->orderBy("path_order DESC")->one();
				        	if($path){
				        		return $path->domain;
				        	}
				        	else{
				        		return Yii::t('circuits', 'deleted');
				        	};
	        			},
	        			'headerOptions'=>['style'=>'width: 21%;'],
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Requester'),
	        			'value' => function($aut){
	        				$user_id = $aut->request_user_id;
	        				return User::findOne(['id' => $user_id])->name;
	        			},
	        			'headerOptions'=>['style'=>'width: 12%;'],
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Bandwidth'),
	        			'value' => function($aut){
	        				return $aut->bandwidth." Mbps";
	        			},
	        			'headerOptions'=>['style'=>'width: 12%;'],
	        		],
	        		[
            			'class' => 'yii\grid\ActionColumn',
            			'template' => '{answer}',
            			'buttons' => [
	           				'answer' => function ($url,$model) {
            					return Html::button(Yii::t('circuits', 'Answer'), ['onclick' => "window.location='answer?id=$model->id&domain=$model->domain'"]);
			                },
			            ],
			            'headerOptions'=>['style'=>'width: 11%;'],
        			],

	        	),
		]);
	?>