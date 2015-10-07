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
	use app\models\User;
	use app\models\ConnectionPath;
	
	use app\modules\circuits\assets\HistoryReservationAsset;
	
	HistoryReservationAsset::register($this);
?>

<h1><?= Yii::t('circuits', "History reservations"); ?></h1>

<?=
	GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $data,
		'filterModel' => $searchModel,
		'layout' => "{items}{summary}{pager}",
		'columns' => array(
				[
					'class'=> LinkColumn::className(),
					'image'=>'/images/eye.png',
					'label' => '',
					'url' => 'view',
					'headerOptions'=>['style'=>'width: 2%;'],
				],
				[
					'label' => Yii::t('circuits', 'Name'),
					'value' => 'name',
					'headerOptions'=>['style'=>'width: 13%;'],
				],
				[
					'attribute'=>'date',
					'format'=>'datetime',
					'headerOptions'=>['style'=>'width: 10%;'],
				],
				[
					'label' => Yii::t('circuits', 'Source Domain'),
					'value' => function($model) {
						return $model->getSourceDomain();
					},		
					'filter' => Html::activeDropDownList($searchModel, 'src_domain', 
                        ArrayHelper::map(
							ConnectionPath::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
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
                        ArrayHelper::map(
							ConnectionPath::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
                        ['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
					),
					'headerOptions'=>['style'=>'width: 14%;'],
				],
				[
					'label' => Yii::t('circuits', 'Bandwidth'),
					'value' => function($res){
						return $res->bandwidth." Mbps";
					},
					'headerOptions'=>['style'=>'width: 7%;'],
				],
				[
					'label' => Yii::t('circuits', 'Requester'),
					'value' => function($res){
	        			$user_id = $res->request_user_id;
	        			return User::findOne(['id' => $user_id])->name;
	        		},
					'filter' => Html::activeDropDownList($searchModel, 'request_user',
						ArrayHelper::map(
							User::find()->where(['id' => Yii::$app->user->getId()])->all(), 'login', 'login'),
						['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
					),
					'headerOptions'=>['style'=>'width: 12%;'],
				],
				[
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
					'headerOptions'=>['style'=>'width: 28%;'],
				],
			),
	]);
?>