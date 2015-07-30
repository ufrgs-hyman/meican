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
	use app\models\ConnectionAuth;
	use app\models\Urn;
	use app\models\User;
	
	use yii\helpers\ArrayHelper;
	
	use yii\data\ArrayDataProvider;
?>

<h1><?= Yii::t('circuits', 'Pending Authorization'); ?></h1>

	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data,
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridInfo',
			'filterModel' => $searchModel,
			'layout' => "{items}{summary}{pager}",
			'columns' => array(
					[
						'label' => Yii::t('circuits', 'Reply request as '),
						'value' => 'domain',
						'filter' => Html::activeDropDownList($searchModel, 'domain',
								ArrayHelper::map(
										ConnectionAuth::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
								['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
						),
						'contentOptions'=>['style'=>'font-weight: bold;'],
						'headerOptions'=>['style'=>'width: 23%;'],
					],
	        		[
		        		'label' => Yii::t('circuits', 'Source Domain'),
		        		'value' => function($aut){
	        				if($aut->source){
				        		return $aut->source;
				        	}
				        	else{
				        		return Yii::t('circuits', 'deleted');
				        	};
		        		},
		        		'filter' => Html::activeDropDownList($searchModel, 'src_domain',
		        				ArrayHelper::map(
		        						ConnectionPath::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
		        				['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
		        		),
		        		'headerOptions'=>['style'=>'width: 21%;'],
	        		],
	        		[
	        			'label' => Yii::t('circuits', 'Destination Domain'),
	        			'value' => function($aut){
				        	if($aut->destination){
				        		return $aut->destination;
				        	}
				        	else{
				        		return Yii::t('circuits', 'deleted');
				        	};
	        			},
	        			'filter' => Html::activeDropDownList($searchModel, 'dst_domain',
	        					ArrayHelper::map(
	        							ConnectionPath::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
	        					['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
	        			),
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