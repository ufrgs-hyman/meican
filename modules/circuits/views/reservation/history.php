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

<h1><?= Yii::t('circuits', "History reservations"); ?></h1>

<label><input id="radio_user_reservations" type="radio" name="toggler" value="user" checked style="margin-right: 5px;" /><?= Yii::t('circuits', "Mine"); ?></label>
<label><input id="radio_domains_reservations" type="radio" name="toggler" value="domain" style="margin-right: 5px; margin-left: 5px;" /><?= Yii::t('circuits', "Domains"); ?></label>

<div id="div_table_user" class="toHide">
	<?php Pjax::begin([
	            'id' => 'pjax-status-user',
	            'enablePushState' => false,
	]); ?>
	
	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data_user,
			'columns' => array(
					[
						'class'=> LinkColumn::className(),
						'image'=>'/images/eye.png',
						'label' => '',
						'url' => 'view',
						'headerOptions'=>['style'=>'width: 3%;'],
					],
					[
						'label' => Yii::t('circuits', 'Name'),
						'value' => 'name',
						'headerOptions'=>['style'=>'width: 15%;'],
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
	                            Domain::find()->all(), 'name', 'name'),
	                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]
						),
						'headerOptions'=>['style'=>'width: 17%;'],
					],
					[
						'label' => Yii::t('circuits', 'Destination Domain'),
						'value' => function($model) {
							return $model->getDestinationDomain();
						},
						'filter' => Html::activeDropDownList($searchModel, 'dst_domain', 
	                        ArrayHelper::map(
	                            Domain::find()->all(), 'name', 'name'),
	                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]
						),
						'headerOptions'=>['style'=>'width: 17%;'],
					],
					[
						'label' => Yii::t('circuits', 'Bandwidth'),
						'value' => 'bandwidth',
						'headerOptions'=>['style'=>'width: 8%;'],
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
						'headerOptions'=>['style'=>'width: 30%;'],
					],
				),
		]);
	?>
	
	<?php Pjax::end(); ?>
</div>

<div id="div_table_domain" class="toHide" style="display:none">
	<?php Pjax::begin([
	            'id' => 'pjax-status-domain',
	            'enablePushState' => false,
	]); ?>
	
	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data_domain,
			'filterModel' => $searchModel,
			'columns' => array(
					[
						'class'=> LinkColumn::className(),
						'image'=>'/images/eye.png',
						'label' => '',
						'url' => 'view',
						'headerOptions'=>['style'=>'width: 3%;'],
					],
					[
						'label' => Yii::t('circuits', 'Name'),
						'value' => 'name',
						'headerOptions'=>['style'=>'width: 15%;'],
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
	                            $allowedDomains, 'name', 'name'),
	                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]
						),
						'headerOptions'=>['style'=>'width: 17%;'],
					],
					[
						'label' => Yii::t('circuits', 'Destination Domain'),
						'value' => function($model) {
							return $model->getDestinationDomain();
						},
						'filter' => Html::activeDropDownList($searchModel, 'dst_domain', 
	                        ArrayHelper::map(
	                            $allowedDomains, 'name', 'name'),
	                        ['class'=>'form-control','prompt' => Yii::t("topology", 'any')]
						),
						'headerOptions'=>['style'=>'width: 17%;'],
					],
					[
						'label' => Yii::t('circuits', 'Bandwidth'),
						'value' => 'bandwidth',
						'headerOptions'=>['style'=>'width: 8%;'],
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
						'headerOptions'=>['style'=>'width: 30%;'],
					],
				),
		]);
	?>
	
	<?php Pjax::end(); ?>
</div>