<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\jui\Dialog;

use meican\base\components\LinkColumn;	
use meican\topology\models\Domain;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;	
use meican\circuits\assets\authorization\Asset;

Asset::register($this);

?>

<script>
	var jsonEvents = <?php echo json_encode($events); ?>;
	var domain = <?php echo json_encode($domain); ?>;
	var reservationId = <?php echo $info->reservation_id; ?>;
</script>

<h1><?= Yii::t('circuits', 'Reply request as ').Domain::findOne(['name' => $domain])->name ?></h1>

<table id="table" style="min-height: 225px; width:100%">
	<tr style="vertical-align: top; ">
  		<td style="width: 50%; word-wrap: break-word; overflow-wrap: break-word;">
			<div class="float-left">
		    	<?= DetailView::widget([
				    'options' => ['class' => 'list', 'style'=>'margin-top: 4px !important'],
				    'model' => $info,
				    'attributes' => [
				        'reservation_name',
						'source_domain',
						'destination_domain',
						'requester',
						'bandwidth',
						'port_in:html',
						'port_out:html',
				    ],
				]); ?>
		    	
		    	
			</div>
		</td>
		
	  	<td style="width: 50%">
			<?php \yii\widgets\Pjax::begin([
			    	'id' => 'pjaxContainer',
				]);
			?>
			<table style="width: 100%;">
			<tr>
				<td id="auth_controls" style="text-align: left; width: 66%; padding: 0px;">
					<?php
						$notWaiting = true;
						foreach($requests as $req){
							if($req->status == Connection::AUTH_STATUS_PENDING){
								$notWaiting = false;
								break;
							}
						}
						$domainTop = json_encode($domain);
						echo Html::button(Yii::t('circuits', 'Accept All'), ['disabled' => $notWaiting, 'onclick' => "acceptAll($info->reservation_id, $domainTop)"]);
						echo Html::button(Yii::t('circuits', 'Reject All'), ['disabled' => $notWaiting, 'onclick' => "rejectAll($info->reservation_id, $domainTop)"]);
					?>
				</td>
				<td id="map_controls" style="text-align: right; width: 33%;">
					<?php
						echo Html::a(Yii::t('circuits', 'See Map'), ['/circuits/reservation/view', 'id' => $info->reservation_id]);
					?>
				</td>
				</tr>
			</table>
			
			<?= GridView::widget([
					'options' => ['class' => 'list'],
					'dataProvider' => new ArrayDataProvider([
		    				'allModels' => $requests,
		    				'sort' => false,
		    				'pagination' => [
						        'pageSize' => 4,
						    ],
		    		]),
					'formatter' => new Formatter(['nullDisplay'=>'']),
					'id' => 'gridRequest',
					'layout' => "{items}{summary}{pager}",
					'rowOptions' => function ($model, $key, $index, $grid){
						if($model->status == Connection::AUTH_STATUS_APPROVED){
							return ['style'=>'background-color: #e4ffe4; border-bottom: 1px solid #d4eed4;', 'id' => $model['id'], 'onclick' => 'toDate(id)'];
						}
						else if($model->status == Connection::AUTH_STATUS_REJECTED){
							return ['style'=>'background-color: #ffe6e6; border-bottom: 1px solid #eed6d6', 'id' => $model['id'], 'onclick' => 'toDate(id)'];
						}
						else return ['id' => $model['id'], 'onclick' => 'toDate(id)'];
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
									return $req->getStatus();
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
		    <input style="margin-right: 5px;" type="checkbox" id="checkPending" checked><?= Yii::t('circuits', 'Show others (Pending)'); ?></input>
		    <input style="margin-left: 10px; margin-right: 5px;" type="checkbox" id="checkConfirmed" checked><?= Yii::t('circuits', 'Show others (Confirmed)'); ?></input>
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