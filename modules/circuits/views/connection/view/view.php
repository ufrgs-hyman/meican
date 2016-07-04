<?php 
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

use kartik\datetime\DateTimePicker;
use kartik\switchinput\SwitchInput;
use kartik\touchspin\TouchSpin;
use kartik\form\ActiveForm;

use meican\base\grid\Grid;
use meican\base\utils\DateUtils;
use meican\circuits\forms\ConnectionForm;
use meican\base\widgets\DetailView;

\meican\circuits\assets\connection\View::register($this);

$this->params['header'] = [Yii::t('circuits',"Circuit").' #'.$conn->id, ['Home', 'Circuits']];

?>

<data id="circuit-id" value="<?= $conn->id; ?>"></data>

<?php Pjax::begin(['id'=>'status-pjax']); ?>

<?= $this->render('status', array(
    'conn' => $conn,
    'lastEvent' => $lastEvent 
)); ?>

<?php Pjax::end(); ?>

<div class="row">
    <div class="col-md-8">
        <div id="path-box" class="box box-default">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a href="#path-info" data-toggle="tab">Info</a></li>
                <!--<li><a href="#path-graph" data-toggle="tab">Graph</a></li>-->
                <li class="active"><a href="#path-map" data-toggle="tab">Map</a></li>
                <li class="pull-left header"> Path</li>
            </ul>
            <div class="tab-content no-padding">
              <div class="tab-pane active" id="path-map">
              </div>
              <div class="tab-pane" id="path-graph">
                Comming soon.
              </div>
              <div class="tab-pane with-padding" id="path-info">
                <?php

                $provider = new \yii\data\ArrayDataProvider([
                    'allModels' => [['order'=>0, 'urn'=>'fdasfd', 'vlan'=> 123]],
                ]);

                echo Grid::widget([
                    'id'=> 'path-grid',
                    'summary' => false,
                    'dataProvider' => $provider,
                    'columns' => array(
                        [
                            'header' => 'Order',
                            'value' => function ($model){
                                return "";
                            },
                        ],
                         [
                            'header' => 'URN',
                            'value' => function ($model){
                                return "";
                            },
                        ],
                         [
                            'header' => 'VLAN',
                            'value' => function ($model){
                                return "";
                            },
                        ],
                        /*[
                            'header' => '',
                            'format' => 'raw',
                            'value' => function ($model){
                                return '<a href="#" class="show-stats">Show stats</a>';
                            },
                        ],*/
                    ),
                ]); ?>
              </div>
            </div>
        </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Details"); ?></h3>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button id="refresh-btn" type="button" class="btn btn-default btn-sm">Refresh</button>
                        <button id="edit-btn" type="button" class="btn btn-default btn-sm">Edit</button>
                        <button id="cancel-btn" type="button" class="btn btn-default btn-sm">Cancel</button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <?php Pjax::begin([
                    'id' => 'details-pjax',
                    'timeout' => 5000, 
                ]);

                echo $this->render('details', array(
                    'conn' => $conn,
                ));

                Pjax::end(); ?>
            </div>
        </div> 
    </div>
</div> 
<div class="row">
    <div class="col-md-8">
        <div id="stats-box" class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "Traffic monitoring"); ?></h3>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm active">Last hour</button>
                        <button type="button" class="btn btn-success btn-sm refresh-btn">Refresh</button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <!--<div class="pull-left">Target: <span id="stats-target"></span></div>-->
                <div id="stats-legend" class="pull-right"></div>
                <div id="stats" style="margin-top: 25px;"></div>
            </div>
            
            <div id='stats-loading' class="overlay">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>    
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("topology", "History"); ?></h3>
            </div>
            <div class="box-body">                
                <?php 

                Pjax::begin([
                    'id' => 'history-pjax',
                    'timeout' => 5000, 
                ]);

                echo $this->render('history', array(
                    'history' => $history,
                )); 

                Pjax::end();

                ?>
            </div>
        </div>    
    </div>
</div> 
 
<?php Modal::begin([
    'id' => 'event-message-modal',
    'header' => 'Detailed event message',
    'size' => Modal::SIZE_LARGE,
]); ?>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'edit-modal',
    'header' => 'Edit',
    'footer' => '<button type="button" class="confirm-btn btn btn-primary">Confirm</button>'.
        '<button type="button" class="btn btn-default undo-btn">Undo changes</button>'. 
        '<button type="button" class="btn btn-default close-btn">Close</button>'
]); 

$form = ActiveForm::begin([
    'id'=> 'edit-form',
    'action' => ['connection/update'],
    'enableAjaxValidation' => true]); ?>

<input name="ConnectionForm[id]" value="<?= $conn->id; ?>" hidden>

<?php

$editForm = new ConnectionForm;

echo $form->field($editForm, 'acceptRelease')->widget(SwitchInput::classname(), [
    'pluginOptions' => [
        'onText' => 'Yes',
        'offText' => 'No',
    ],
]);

echo $form->field($editForm, 'bandwidth')->widget(TouchSpin::classname(), [
    'pluginOptions' => [
        'postfix' => 'Mbps',
        'verticalbuttons' => true,
        'verticalupclass' => 'fa fa-plus',
        'verticaldownclass' => 'fa fa-minus',
        'min' => 10,
        'max' => 1000000,
        'step' => 10,
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
    'footer' => '<button type="button" class="btn btn-danger confirm-btn">Confirm</button>'.        
        '<button type="button" class="btn btn-default close-btn">Close</button>'
]); ?>

Do you want cancel this circuit?

<?php Modal::end(); ?>
