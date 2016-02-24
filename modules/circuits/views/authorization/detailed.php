<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use meican\base\grid\Grid;
use yii\widgets\DetailView;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\jui\Dialog;

use yii\bootstrap\Modal;

use meican\base\components\LinkColumn;	
use meican\topology\models\Domain;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;	
use meican\circuits\assets\authorization\Asset;
use meican\base\assets\FullCalendar;

Asset::register($this);

FullCalendar::register($this);

?>

<script>
	var jsonEvents = <?php echo json_encode($events); ?>;
	var domain = <?php echo json_encode($domain); ?>;
	var reservationId = <?php echo $info->reservation_id; ?>;
	var language = <?php echo json_encode($language); ?>;
</script>

<h1><?= Yii::t('circuits', 'Reply request as ').Domain::findOne(['name' => $domain])->name ?></h1>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("circuits", "Informations"); ?></h3>
            </div>
            <div class="box-body">                
                <div class="table-responsive">
			    	<?= DetailView::widget([
					    'options' => ['class' => 'table table-condensed'],
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
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-default">
        	<?php \yii\widgets\Pjax::begin([
				'id' => 'pjaxContainer',
			]);?>
            <div class="box-header with-border">
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
								echo Html::button(Yii::t('circuits', 'Accept All'), ['class' => 'btn btn-primary', 'disabled' => $notWaiting, 'onclick' => "acceptAll($info->reservation_id, $domainTop)"]);
								echo " ";
								echo Html::button(Yii::t('circuits', 'Reject All'), ['class' => 'btn btn-primary', 'disabled' => $notWaiting, 'onclick' => "rejectAll($info->reservation_id, $domainTop)"]);
							?>
						</td>
						<td id="map_controls" style="text-align: right; width: 33%;">
							<?php
								echo Html::a(Yii::t('circuits', 'See Map'), ['/circuits/reservation/view', 'id' => $info->reservation_id]);
							?>
						</td>
					</tr>
				</table>
            </div>
            <div class="box-body">                
                <div class="table-responsive">
			    	<?= Grid::widget([
						'options' => ['class' => 'list'],
						'dataProvider' => new ArrayDataProvider([
			    				'allModels' => $requests,
			    				'sort' => false,
			    				'pagination' => [
							        'pageSize' => 4,
							    ],
			    		]),
						'id' => 'gridRequest',
						'layout' => "{items}{summary}{pager}",
						'rowOptions' => function ($model, $key, $index, $grid){
							return ['id' => $model['id'], 'onclick' => 'toDate(id)'];
						},
						'columns' => array(
								[
									'class' => 'yii\grid\ActionColumn',
									'template'=>'{accept}{reject}',
									'contentOptions' => function($model){
										return  ['style' => 'white-space: nowrap;'];
									},
									'buttons' => [
										'accept' => function ($url, $model) {
											return Html::a('<span class="fa fa-thumbs-o-up"></span>', null, ['disabled'=>$model->isAnswered(), 'class'=>'btn btn-accept', 'id' => $model->id]);
										},
										'reject' => function ($url, $model) {
											return Html::a('<span class="fa fa-thumbs-o-down"></span>', null, ['disabled'=>$model->isAnswered(), 'class'=>'btn btn-reject', 'id' => $model->id]);
										}
									],
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
									'format' => 'raw',
									'label'=> Yii::t('circuits', 'Status'),
									'value' => function($req){
										if($req->status == Connection::AUTH_STATUS_APPROVED) return '<span class="label label-success">'.$req->getStatus().'</span>';
										if($req->status == Connection::AUTH_STATUS_REJECTED || $req->status == Connection::AUTH_STATUS_EXPIRED) return '<span class="label label-danger">'.$req->getStatus().'</span>';
										return '<span class="label label-warning">'.$req->getStatus().'</span>';
									},
									'contentOptions'=>['style'=>'min-width: 70px;']
								],
					        	),
						]);
					?>
				</div>
            </div>
            <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="box box-default">
	    	<div class="box-header with-border">
	    		<input style="margin-right: 5px;" type="checkbox" id="checkPending" checked><?= Yii::t('circuits', 'Show others (Pending)'); ?></input>
			    <input style="margin-left: 10px; margin-right: 5px;" type="checkbox" id="checkConfirmed" checked><?= Yii::t('circuits', 'Show others (Confirmed)'); ?></input>
	        </div>
	        <div class="box-body">
	        	<div id='calendar' class="fc-unthemed"></div>
	        </div>
	    </div>
	</div>
</div>

<?php 

Modal::begin([
    'id' => 'auth-accept-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t("circuits", "Cancel").'</button><button id="accept-btn" class="btn btn-success">'.Yii::t("circuits", "Accept").'</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;">'.Yii::t("circuits", "Request will be accepted. If you want, provide a message:").'</p>';
echo '<textarea type="text" name="name" id="auth-accept-message" class="form-control" rows="5"></textarea>';

Modal::end();

Modal::begin([
		'id' => 'auth-reject-modal',
		'headerOptions' => ['hidden'=>'hidden'],
		'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t("circuits", "Cancel").'</button><button id="reject-btn" class="btn btn-danger">'.Yii::t("circuits", "Reject").'</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;">'.Yii::t("circuits", "Request will be rejected. If you want, provide a message:").'</p>';
echo '<textarea type="text" name="name" id="auth-reject-message" class="form-control" rows="5"></textarea>';

Modal::end();

Modal::begin([
		'id' => 'all-accept-modal',
		'headerOptions' => ['hidden'=>'hidden'],
		'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t("circuits", "Cancel").'</button><button id="accept-btn" class="btn btn-success">'.Yii::t("circuits", "Accept").'</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;">'.Yii::t("circuits", "All requests will be accepted. If you want, provide a message:").'</p>';
echo '<textarea type="text" name="name" id="all-accept-message" class="form-control" rows="5"></textarea>';

Modal::end();

Modal::begin([
		'id' => 'all-reject-modal',
		'headerOptions' => ['hidden'=>'hidden'],
		'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t("circuits", "Cancel").'</button><button id="reject-btn" class="btn btn-danger">'.Yii::t("circuits", "Reject").'</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;">'.Yii::t("circuits", "All requests will be rejected. If you want, provide a message:").'</p>';
echo '<textarea type="text" name="name" id="all-reject-message" class="form-control" rows="5"></textarea>';

Modal::end();

?>