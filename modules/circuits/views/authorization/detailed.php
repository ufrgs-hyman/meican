<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use app\components\LinkColumn;
	use yii\helpers\Html;
	use yii\i18n\Formatter;
	use yii\widgets\ActiveForm;
	use app\models\Reservation;
	use app\models\Domain;
	use app\models\Connection;
	use app\models\ReservationPath;
	use app\models\Urn;
	use app\models\User;
	use yii\data\ArrayDataProvider;
	use yii\jui\Dialog;
	
	use app\modules\circuits\assets\AuthorizationAsset;
	AuthorizationAsset::register($this);
?>

<script>
	var jsonEvents = <?php echo json_encode($events); ?>;
	var domain = <?php echo json_encode($domain); ?>;
	var reservationId = <?php echo $info->id; ?>;
</script>

<h1><?= Yii::t('circuits', 'Reply request as ').Domain::findOne(['topology' => $domain])->name ?></h1>

<table id="table" style="width:100%">
	<tr style="vertical-align: top; ">
  		<td style="width: 50%; word-wrap: break-word; overflow-wrap: break-word;">
			<h4 class="float-left">
		    	<dl>
			        <dt><?php echo Yii::t('circuits', 'Reservation name:'); ?></dt>
			        <dd><?php echo $info->name; ?></dd>
			        <dt><?php echo Yii::t('circuits', 'Source Domain').":"; ?></dt>
			        <dd><?php
			        	$path = ReservationPath::findOne(['reservation_id' => $info->id, 'path_order' => 0]);
			        	if($path){
			        		echo $path->domain;
			        	}
			        	else{
			        		return Yii::t('circuits', 'deleted');
			        	};
			        ?></dd>
			        <dt><?php echo Yii::t('circuits', 'Source Urn').":"; ?></dt>
			        <dd><?php
			        	$path = ReservationPath::findOne(['reservation_id' => $info->id, 'path_order' => 0]);
			        	if($path){
			        		echo $path->urn;
			        	}
			        	else{
			        		return Yii::t('circuits', 'deleted');
			        	};
			        ?></dd>
			        <dt><?php echo Yii::t('circuits', 'Destination Domain').":"; ?></dt>
			        <dd><?php
			        	$path = ReservationPath::find()->where(['reservation_id' => $info->id])->orderBy("path_order DESC")->one();
	        			if($path){
			        		echo $path->domain;
			        	}
			        	else{
			        		return Yii::t('circuits', 'deleted');
			        	};
			        ?></dd>
			        <dt><?php echo Yii::t('circuits', 'Destination Urn').":"; ?></dt>
			        <dd><?php
			        	$path = ReservationPath::find()->where(['reservation_id' => $info->id])->orderBy("path_order DESC")->one();
	        			if($path){
			        		echo $path->urn;
			        	}
			        	else{
			        		return Yii::t('circuits', 'deleted');
			        	};
			        ?></dd>
			        <dt><?php echo Yii::t('circuits', 'Requester').":"; ?></dt>
			        <dd><?php
			        	echo User::findOne(['id' => $info->request_user_id])->name;
			        ?></dd>
			        <dt><?php echo Yii::t('circuits', 'Requested Bandwidth:'); ?></dt>
			        <dd><?php
			        	echo $info->bandwidth." Mbps";
			        ?></dd>
			    </dl>
			</h4>
		</td>
		
	  	<td style="width: 50%">
			<?php \yii\widgets\Pjax::begin([
			    	'id' => 'pjaxContainer',
				]);
			?>
			
			<div id="auth_controls">
				<?php
					$notWaiting = true;
					foreach($requests as $req){
						if($req->status == "WAITING"){
							$notWaiting = false;
							break;
						}
					}
					$domainTop = json_encode($domain);
					echo Html::button(Yii::t('circuits', 'Accept All'), ['disabled' => $notWaiting, 'onclick' => "acceptAll($info->id, $domainTop)"]);
					echo Html::button(Yii::t('circuits', 'Reject All'), ['disabled' => $notWaiting, 'onclick' => "rejectAll($info->id, $domainTop)"]);
				?>
			</div>
			
			<?= GridView::widget([
					'options' => ['class' => 'list'],
					'dataProvider' => new ArrayDataProvider([
		    				'allModels' => $requests,
		    				'sort' => false,
		    				'pagination' => false,
		    		]),
					'formatter' => new Formatter(['nullDisplay'=>'']),
					'id' => 'gridRequest',
					'layout' => "{items}{pager}",
					'rowOptions' => function ($model, $key, $index, $grid){
						if($model->status == "AUTHORIZED"){
							if($index % 2 == 0) return ['style'=>'background-color: #d9ffd9;', 'id' => $index, 'onclick' => 'toDate(id)'];
							else return ['style'=>'background-color: #e4ffe4;', 'id' => $index, 'onclick' => 'toDate(id)'];
						}
						else if($model->status == "DENIED"){
							if($index % 2 == 0) return ['style'=>'background-color: #ffdbdb;', 'id' => $index, 'onclick' => 'toDate(id)'];
							else return ['style'=>'background-color: #ffe6e6;', 'id' => $index, 'onclick' => 'toDate(id)'];
						}
						else return ['id' => $index, 'onclick' => 'toDate(id)'];
					},
					'columns' => array(
							[
								'format' => 'raw',
								'label' => Yii::t('circuits', 'Accept'),
								'value' => function ($req){
									return Html::img('@web'.'/images/hand_good.png', ['title' => Yii::t('circuits', 'Accept'), 'onclick' => "accept($req->id)"]);
								},
								'contentOptions'=>function($model) {
									return ['style'=>'width: 40px; cursor: pointer;', "disabled"=> $model->isAnswered()];
								},
							],
							[
								'format' => 'raw',
								'label' => Yii::t('circuits', 'Reject'),
								'value' => function ($req){
									return Html::img('@web'.'/images/hand_bad.png', ['title' => Yii::t('circuits', 'Reject'), 'onclick' => "reject($req->id)"]);
								},
								'contentOptions'=>function($model) {
									return ['style'=>'width: 40px; cursor: pointer;', "disabled"=> $model->isAnswered()];
								},
							],
				       		[
			        			'label'=> Yii::t('circuits', 'Initial Date/Time'),
				       			'value' => function($req){
				      				return Yii::$app->formatter->asDatetime(Connection::findOne(['id' => $req->connection_id])->start);
				       			},
				       			'contentOptions'=>['style'=>'min-width: 70px;']
							],
							[
								'label'=> Yii::t('circuits', 'Final Date/Time'),
								'value' => function($req){
				       				return Yii::$app->formatter->asDatetime(Connection::findOne(['id' => $req->connection_id])->finish);
				       			},
								'contentOptions'=>['style'=>'min-width: 70px;']
							],
							[
								'label'=> Yii::t('circuits', 'Duration'),
								'value' => function($req){
									$start = strtotime(Connection::findOne(['id' => $req->connection_id])->start);
									$finish = strtotime(Connection::findOne(['id' => $req->connection_id])->finish);
									$mins = ($finish - $start) / 60;
									$hours = 0;
									while($mins > 59){
										$hours++;
										$mins-=60;
									}
									return $hours."h".$mins."m";
								},
								'contentOptions'=>['style'=>'min-width: 70px;']
							],
							[
								'label'=> Yii::t('circuits', 'Status'),
								'value' => function($req){
									if($req->status == "WAITING") return Yii::t('circuits', 'Waiting');
									else if($req->status == "AUTHORIZED") return Yii::t('circuits', 'Approved');
									else if($req->status == "DENIED") return Yii::t('circuits', 'Rejected');
									else if($req->status == "EXPIRED") return Yii::t('circuits', 'Expired');
								},
								'contentOptions'=>['style'=>'min-width: 70px;']
							],
				        	),
				]);
			?>
			
			<?php \yii\widgets\Pjax::end(); ?>
				
		</td> 
  	</tr>
</table>

<table style="margin-top: 15px; width:100%">
  <tr style="vertical-align: top; ">
    <td style="width: 100%">
	    <div align="left" style="margin-bottom: 10px;">
	    	<!-- <input type="checkbox" id="checkAgenda">Agenda</input> -->
		    <input type="checkbox" id="checkPending" checked><?= Yii::t('circuits', 'Show others (Pending)'); ?></input>
		    <input type="checkbox" id="checkConfirmed" checked><?= Yii::t('circuits', 'Show others (Confirmed)'); ?></input>
	    </div>
	    <?php echo \talma\widgets\FullCalendar::widget([
			'id' => 'calendar',
    		'config' => [
    			'lang' => strtolower(Yii::$app->language),
    		],
		]);
	    ?>
	    
    </td>
  </tr>
</table>

<div style="display: none">
<?php Dialog::begin([
		'id' => 'dialog',
    	'clientOptions' => [
        	'modal' => true,
        	'autoOpen' => false,
        	'title' => "Meican",
    	],
	]);

	echo '<div align="center">';
    echo '<img align="center" id="MessageImg" alt="" src=""/></br>';
    echo '<label style="width:100%;" for="name" id="MessageLabel"></label>';
    echo '<textarea type="text" name="name" id="Message" class="text ui-widget-content ui-corner-all" style="width:97%;margin-top:10px;" cols="20" rows="5"></textarea>';
	echo '</div>';
    
	Dialog::end(); 
?>
</div>