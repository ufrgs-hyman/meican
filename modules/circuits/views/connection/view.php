<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\DetailView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

use kartik\datetime\DateTimePicker;
use kartik\touchspin\TouchSpin;
use kartik\form\ActiveForm;

use meican\base\grid\Grid;
use meican\base\components\DateUtils;

\meican\circuits\assets\connection\View::register($this);

$this->params['header'] = [Yii::t('circuits',"Circuit Details"), ['Home', 'Circuits']];

?>

<data id="circuit-id" value="<?= $conn->id; ?>"></data>

<?php Pjax::begin(['id'=>'status-pjax']); ?>

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div id="status-box" class="info-box">
        <span class="info-box-icon"><i class="ion ion-clock"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Status</span>
          <span class="info-box-number"><small><?= $conn->dataplane_status; ?></small></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon"><i class="ion ion-clipboard"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Reservation</span>
          <span class="info-box-number"><small><?= $conn->status; ?></small></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon"><i class="ion ion-thumbsup"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Authorization</span>
          <span class="info-box-number"><small><?= $conn->auth_status; ?></small></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon"><i class="ion ion-loop"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Updated at</span>
          <span class="info-box-number"><small><?= Yii::$app->formatter->asDatetime($lastEvent->created_at)."<br>by ".$lastEvent->getAuthor(); ?></small></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
<!-- /.col -->
</div>

<?php Pjax::end(); ?>

<div class="row">
    <div class="col-md-8">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#canvas" data-toggle="tab">Map Viewer</a></li>
              <li><a href="#stats" data-toggle="tab">Graph Viewer</a></li>
            </ul>
            <div class="tab-content no-padding">
              <div class="tab-pane active" id="canvas">
              </div>
              <div class="tab-pane" id="graph">
                Comming soon.
              </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Stats"); ?></h3>
            </div>
            <div id="stats" class="box-body">
            </div>
        </div>    
    </div>
</div> 
<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Info"); ?></h3>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                      <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-wrench"></i></button>
                      <ul class="dropdown-menu" role="menu">
                        <li><a id="edit-btn" href="#">Edit circuit</a></li>
                        <li><a id="cancel-btn" href="#">Cancel circuit</a></li>
                      </ul>
                    </div>
                  </div>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'id' => 'circuit-info',
                    'model' => $conn,
                    'attributes' => [
                        'external_id',
                        [                      
                            'attribute' => 'bandwidth',
                            'format' => 'raw',
                            'value' => '<data id="info-bandwidth" value="'.$conn->bandwidth.'"></data>'.$conn->bandwidth." Mbps"                            
                        ],
                        [                      
                            'attribute' => 'start',
                            'format' => 'raw',
                            'value' => '<data id="info-start" value="'.Yii::$app->formatter->asDatetime($conn->start).'"></data>'.Yii::$app->formatter->asDatetime($conn->start)
                        ],                        
                        [                      
                            'attribute' => 'finish',
                            'format' => 'raw',
                            'value' => '<data id="info-end" value="'.Yii::$app->formatter->asDatetime($conn->finish).'"></data>'.Yii::$app->formatter->asDatetime($conn->finish)
                        ],  
                        'version',
                        [                      
                            'label' => 'Type',
                            'value' => 'NSI'                            
                        ],
                        [                      
                            'label' => 'Provider',
                            'format' => 'raw',
                            'value' => 'RNP Aggregator'.
                                '<data id="info-status" value='.$conn->status.'></data>'.
                                '<data id="info-auth" value='.$conn->auth_status.'></data>'.
                                '<data id="info-dataplane" value='.$conn->dataplane_status.'></data>'                           
                        ],
                    ],
                ]); ?>
            </div>
        </div> 
    </div>
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "History"); ?></h3>
            </div>
            <div class="box-body">                
                <?php 

                Pjax::begin([
                    'timeout' => 5000, 
                    'enablePushState' => false,
                    'clientOptions' => ['container' => 'history-pjax']]);

                echo Grid::widget([
                    'id'=> 'history-grid',
                    'dataProvider' => $history,
                    'columns' => array(
                        'created_at:datetime',
                        'type',
                        [
                            'attribute' => 'message',
                            'format' => 'raw',
                            'value' => function ($model){
                                return $model->message ? '<a href="#"><span class="event-message fa fa-file-text"></span></a>' : '';
                            },
                        ],
                        [
                            'attribute' => 'author_id',
                            'format' => 'raw',
                            'value' => function ($model){
                                return $model->getAuthor();
                            },
                        ],
                    ),
                ]);

                Pjax::end();

                ?>
            </div>
        </div>    
    </div>
</div> 
 
<?php Modal::begin([
    'id' => 'history-modal',
    'header' => 'History',
    'size' => Modal::SIZE_LARGE,
]); 

Pjax::begin([
    'timeout' => 5000, 
    'enablePushState' => false,
    'clientOptions' => ['container' => 'history-modal-pjax']]);

echo Grid::widget([
    'id'=> 'full-history-grid',
    'dataProvider' => $history,
    'columns' => array(
        [
            'header' => 'All Events',
            'format' => 'raw',
            'value' => function ($model){
                return $model->created_at." - ".$model->type." by ".$model->getAuthor().'<br>'.Html::textarea('message', $model->message, ['rows'=> 20, 'cols'=> 138]);
            },
        ],
    ),
]);

Pjax::end();

?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'edit-modal',
    'header' => 'Edit',
    'footer' => '<button type="button" class="btn btn-default close-btn">Close</button> <button type="button" class="confirm-btn btn btn-primary">Confirm</button>'
]); 

$form = ActiveForm::begin([
    'id'=> 'edit-form',
    'action' => ['connection/update'],
    'enableAjaxValidation' => true]); ?>

<input name="ConnectionForm[id]" value="<?= $conn->id; ?>" hidden>

<?php

echo $form->field($editForm, 'bandwidth')->widget(TouchSpin::classname(), [
    'pluginOptions' => [
        'postfix' => 'Mbps',
        'verticalbuttons' => true,
        'verticalupclass' => 'fa fa-plus',
        'verticaldownclass' => 'fa fa-minus',
        'max' => 1000000,
        'step' => 100,
    ]
]);

echo $form->field($editForm, 'start')->widget(DateTimePicker::classname(), [
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd/mm/yyyy hh:ii',
    ]
]);

echo $form->field($editForm, 'end')->widget(DateTimePicker::classname(), [
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd/mm/yyyy hh:ii',
    ]
]);

ActiveForm::end(); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'cancel-modal',
    'header' => 'Cancel',
    'footer' => '<button type="button" class="btn btn-default close-btn">Close</button> <button type="button" class="confirm-btn btn btn-danger">Confirm</button>'
]); ?>

Do you want cancel this circuit?

<?php Modal::end(); ?>
