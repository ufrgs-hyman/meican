<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use app\components\LinkColumn;
	use yii\helpers\Html;
	use yii\i18n\Formatter;
	use yii\widgets\ActiveForm;
	use app\models\Reservation;
	use app\models\Connection;
	use app\models\ReservationPath;
	use app\models\Urn;
	use app\models\User;
?>

<h1><?= Yii::t('circuits', 'Pending Authorization'); ?></h1>


	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data,
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridInfo',
			'layout' => "{items}",
			'columns' => array(
					[
						'label' => Yii::t('circuits', 'Reply request as '),
						'value' => 'domain_name',
						'contentOptions'=>['style'=>'min-width: 200px; font-weight: bold;']
					],
	        		[
		        		'label' => Yii::t('circuits', 'Source Domain'),
		        		'value' => function($aut){
		        			$path = ReservationPath::findOne(['reservation_id' => $aut->id, 'path_order' => 0]);
		        			if($path){
		        				return $path->domain;
		        			}
		        			else{
		        				return Yii::t('circuits', 'deleted');
		        			};
		        		},
		        		'contentOptions'=>['style'=>'min-width: 150px;']
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Destination Domain'),
	        			'value' => function($aut){
	        				$path = ReservationPath::find()->where(['reservation_id' => $aut->id])->orderBy("path_order DESC")->one();
	        				if($path){
	        					return $path->domain;
	        				}
	        				else{
	        					return Yii::t('circuits', 'deleted');
	        				}
	        			},
	        			'contentOptions'=>['style'=>'min-width: 150px;']
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Requester'),
	        			'value' => function($aut){
	        				$user_id = $aut->request_user_id;
	        				return User::findOne(['id' => $user_id])->name;
	        			},
	        			'contentOptions'=>['style'=>'min-width: 50px;']
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Bandwidth'),
	        			'value' => function($aut){
	        				return $aut->bandwidth." Mbps";
	        			},
	        			'contentOptions'=>['style'=>'min-width: 50px;']
	        		],
	        		[
            			'class' => 'yii\grid\ActionColumn',
            			'template' => '{answer}',
            			'buttons' => [
	           				'answer' => function ($url,$model) {
            					return Html::button(Yii::t('circuits', 'Answer'), ['onclick' => "window.location='answer?id=$model->id&domain=$model->domain'"]);
			                },
			            ],	
        			],

	        	),
		]);
	?>