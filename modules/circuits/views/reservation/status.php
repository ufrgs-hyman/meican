<?php 
	use yii\grid\GridView;
	
	use yii\helpers\Html;
	use yii\helpers\ArrayHelper;
	
	use yii\widgets\ActiveForm;
	use yii\widgets\Pjax;
	
	use app\components\LinkColumn;
	
	use app\models\Domain;
	use app\models\Reservation;
	use app\models\Connection;
	
	use app\modules\circuits\assets\ListReservationAsset;
	
	ListReservationAsset::register($this);
?>

<h1><?= Yii::t('circuits', "Active or pending reservations"); ?></h1>

<?php Pjax::begin([
            'id' => 'pjax-status',
            'enablePushState' => false,
]); ?>

<?=
	GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => array(
				[
					'label' => '',
					'value' => function($model) {
						return "";
					}
				],
				array(
						'class'=> LinkColumn::className(),
						'image'=>'/images/eye.png',
						'label' => '',
						'url' => 'view',
						'contentOptions'=>['style'=>'width: 15px;'],
				),
				'name',
				[
						'attribute'=>'date',
						'format'=>'datetime',
				],
				[
					'label' => Yii::t('circuits', 'Source Domain'),
					'value' => function($model) {
						return $model->getSourceDomain();
					},		
					'filter' => Html::activeDropDownList($searchModel, 'src_domain', 
                        ArrayHelper::map(
                            $allowedDomains, 'name', 'name'),
                        ['class'=>'form-control','prompt' => 'any']),
				],
				[
					'label' => Yii::t('circuits', 'Destination Domain'),
					'value' => function($model) {
						return $model->getDestinationDomain();
					},
					'filter' => Html::activeDropDownList($searchModel, 'dst_domain', 
                        ArrayHelper::map(
                            $allowedDomains, 'name', 'name'),
                        ['class'=>'form-control','prompt' => 'any']),
				],
				'bandwidth',
				array(
						'label' => Yii::t('circuits', "Status"),
						'format' => 'html',
						'value' => function($model) {
							$conns = $model->getConnections()->select(['status', 'auth_status','dataplane_status'])->all();

							//Se for somente uma conexão, mostra os status
							if(count($conns)<2) return $conns[0]->getStatus().", ".$conns[0]->getAuthStatus().", ".$conns[0]->getDataStatus();
							
							//Se forem varias, mostra um resumo
							$provisioned = 0; $reject = 0; $pending = 0;
							foreach($conns as $conn){
								if($conn->status == Connection::STATUS_PROVISIONED) $provisioned++;
								else if($conn->status == Connection::STATUS_FAILED_CREATE ||
										$conn->status == Connection::STATUS_FAILED_CONFIRM ||
										$conn->status == Connection::STATUS_FAILED_SUBMIT ||
										$conn->status == Connection::STATUS_FAILED_PROVISION ||
										$conn->auth_status == Connection::AUTH_STATUS_REJECTED ||
										$conn->auth_status == Connection::AUTH_STATUS_EXPIRED
								) $reject++;
								else $pending++;
							}
							
							$msg = Yii::t("notification", 'Provisioned:')." ".$provisioned.", ";
							$msg .= Yii::t("notification", 'Rejected:')." ".$reject.", ";
							$msg .= Yii::t("notification", 'Pending:')." ".$pending;
							
							return $msg;
						},
				),
			),
	]);
?>

<?php Pjax::end(); ?>
