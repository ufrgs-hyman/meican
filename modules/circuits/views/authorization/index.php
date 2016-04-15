<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use meican\base\grid\Grid;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

use meican\base\components\LinkColumn;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\ConnectionAuth;
use meican\aaa\models\User;


$this->params['header'] = [Yii::t('circuits', 'Pending Authorization'), ['Home', Yii::t('circuits', 'Circuits')]];

?>

<div class="box box-default">
    <div class="box-body">      
	<?=
		Grid::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data,
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
	        					return Html::button(Yii::t('circuits', 'Answer'), ['class' => 'btn btn-sm btn-primary', 'onclick' => "window.location='answer?id=$model->id&domain=$model->domain'"]);
			                },
			            ],
			            'headerOptions'=>['style'=>'width: 11%;'],
        			],

	        	),
		]);
	?>
	</div>
</div>