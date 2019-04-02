<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;

use kartik\touchspin\TouchSpin;
use kartik\form\ActiveForm;

use meican\topology\models\Domain;

\meican\circuits\assets\reservation\Create::register($this);

$this->params['hide-content-section'] = true;
$this->params['hide-footer'] = true;

$form = ActiveForm::begin([
    'id' => 'reservation-form',
]) 

?>

<div id="lsidebar" class="lsidebar collapsed">
    <!-- Nav tabs -->
    <div class="lsidebar-tabs">
        <ul role="tablist">
            <li><a title="Welcome to the reservation page" href="#home" role="tab"><i class="fa fa-info-circle"></i></a></li>
            <li><a title="Select your endpoints" href="#path" role="tab"><i class="fa"><img src="https://maxcdn.icons8.com/Android_L/PNG/24/Maps/route-24.png" width="21"></i></a></li>
            <li><a title="Set the circuit requirements" href="#requirements" role="tab"><i class="fa fa-sliders"></i></a></li>
            <li><a title="Choose the circuit duration" href="#schedule" role="tab" class="schedule-tab"><i class="fa fa-calendar"></i></a></li>
            <li><a title="Confirm and submit" href="#confirm" role="tab"><i class="fa fa-check danger"></i></a></li>
        </ul>

        <ul role="tablist">
            <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
        </ul>
    </div>

    <!-- Tab panes -->
    <div class="lsidebar-content">
        <div class="lsidebar-pane" id="path">
            <h1 class="lsidebar-header">
                Step 1: Path
                <span class="lsidebar-close"><i class="fa fa-caret-left"></i></span>
            </h1>
            <br>
            <div class="nav-tabs-custom" style="margin-right: 15px;">
                <ul class="nav nav-tabs">
                  <li><a href="#"><i class="fa fa-search"></i></a></li>
                  <li><a id="add-point" href="#" title="Add point"><i class="fa fa-plus"></i> <i class="fa fa-map-marker"></i></a></li>
                  <li><a href="#" title="Import path"><i class="fa fa-file-text"></i></a></li>
                </ul>
                <div class="tab-content" hidden>
                  <div class="tab-pane active" id="tab_1">
                    <div class="input-group input-group-sm">
                        <!-- /btn-group -->
                        <input type="text" class="form-control" placeholder="Enter a domain, device, port or URN">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-primary"><span class="fa fa-search"></span></button>
                        </div>
                      </div>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_2">
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_3">
                        <button type="button" class="btn btn-default"><span class="fa fa-plus"></span> Import path</button>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <div>
                <ul id="path" class="timeline">
                    <li class="time-label">
                          <span class="bg-gray">
                            <i class="fa fa-laptop"></i>
                            Source
                          </span>
                    </li>
                    <!-- timeline item -->
                    <li class="point">
                        <!-- timeline icon -->
                        <i class="fa fa-map-marker bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">
                                <label class="point-info dom-l">none</label>
                                <div class="pull-right">
                                    <a href="#" class="text-muted"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-up"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-down"></i></a>
                                </div>
                          </h3>
                        <div class="timeline-body" hidden>
                            <div class="point-normal">
                              Network: <label class="point-info net-l">none</label><br>
                              Port: <label class="point-info port-l">none</label><br>
                            </div>
                            <div class="point-advanced" hidden>
                              URN: <label class="point-info urn-l">none</label><br>
                              <input class="urn-input" type="hidden" name="ReservationForm[path][urn][]">
                            </div>
                            VLAN: <label class="point-info vlan-l">none</label>
                            <input class="vlan-input" type="hidden" name="ReservationForm[path][vlan][]">
                            <input class="mode-input" type="hidden" value="normal">
                            <div class="pull-right">
                                <a href="#" class="text-muted"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="text-muted"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                      </div>
                    </li>
                    <li class="point">
                        <!-- timeline icon -->
                        <i class="fa fa-map-marker bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">
                                <label class="point-info dom-l">none</label>
                                <div class="pull-right">
                                    <a href="#" class="text-muted"><i class="fa fa-plus"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-up"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-down"></i></a>
                                </div>
                            </h3>
                            <div class="timeline-body" hidden>
                                <div class="point-normal">
                                  Network: <label class="point-info net-l">none</label><br>
                                  Port: <label class="point-info port-l">none</label><br>
                                </div>
                                <div class="point-advanced" hidden>
                                  URN: <label class="point-info urn-l">none</label><br>
                                  <input class="urn-input" type="hidden" name="ReservationForm[path][urn][]">
                                </div>
                                VLAN: <label class="point-info vlan-l">none</label>
                                <input class="vlan-input" type="hidden" name="ReservationForm[path][vlan][]">
                                <input class="mode-input" type="hidden" value="normal">
                                <div class="pull-right">
                                    <a href="#" class="text-muted"><i class="fa fa-pencil"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->
                    <li id="destination-client" class="time-label">
                      <span class="bg-gray">
                        <i class="fa fa-laptop"></i>
                        Destination
                      </span>
                    </li>
                </ul>
            </div>
            <div class="pull-right margin">
                <button type="button" class="next-btn btn btn-primary"><span class="fa fa-arrow-right"></span> Next step</button>
            </div><br><br><br>
        </div>

        <div class="lsidebar-pane" id="requirements">
            <h1 class="lsidebar-header">Step 2: Requirements<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            <br>
            <p>
                Define your circuit bandwidth requirement. That bandwidth will be reserved and granted only for your circuit.
            </p>
            <br>
            <div class="row">
                <div class="col-xs-7">
                    <div class="form-group">
                        <?php echo $form->field($reserveForm, 'bandwidth')->widget(TouchSpin::classname(), [
                            'pluginOptions' => [
                                'postfix' => 'Mbps',
                                'verticalbuttons' => true,
                                'verticalupclass' => 'fa fa-plus',
                                'verticaldownclass' => 'fa fa-minus',
                                'max' => 100000,
                                'min' => 1,
                                'step' => 10,
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="pull-right margin">
                <button type="button" class="next-btn btn btn-primary"><span class="fa fa-arrow-right"></span> Next step</button>
            </div> 
        </div>

        <div class="lsidebar-pane" id="schedule">
            <h1 class="lsidebar-header">Step 3: Schedule<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
                <br>
                <p>                   
In this section you need to provide at least one interval, based in the start and end time of your circuit. To start, click over the start day of the circuit.
                </p>
                <br>
                <div class="box box-default">
                    <div id="calendar" loaded="false" class="box-body no-padding">
                    </div>
                </div>
                <!-- /.input group -->
              <br>
            <div class="pull-right margin">
                <button type="button" class="next-btn btn btn-primary"><span class="fa fa-arrow-right"></span> Next step</button>
            </div>
        </div>

        <div class="lsidebar-pane" id="confirm">
            <h1 class="lsidebar-header">Step 4: Confirmation<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            <br>
                <p>
                    To confirm your circuit reservation, type a description or a name for it. This name will be used to identify easily your circuit.
                </p>
                <br>
            <?php echo $form->field($reserveForm, 'name')->textInput(); ?>
            <?php echo $form->field($reserveForm, 'auth_user')->textInput()->label('User (optional)'); ?>
            <?php echo $form->field($reserveForm, 'auth_token')->textInput()->label('Access token (optional)'); ?>
            <br>
            <div class="pull-right margin">
                <button type="button" class="next-btn btn btn-primary"><span class="fa fa-arrow-right"></span> Submit</button>
            </div>
        </div>

        <div class="lsidebar-pane" id="home">
            <h1 class="lsidebar-header">Welcome to reservation page<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            <br><p>
                This is the reservation page. Here you can make a circuit reservation very quickly and easily.<br><br>Listed below you can see all steps involved in a circuit reservation:
            </p><br>
            <p>1. Select your endpoints.</p>
            <p>2. Define the requirements, e.g., bandwidth.</p>
            <p>3. Set the duration of the circuit.</p>
            <p>4. Confirm your request and submit.</p>

            <div class="pull-right margin">
                <button type="button" class="next-btn btn btn-primary"><span class="fa fa-arrow-right"></span> Start</button>
            </div>
        </div>

        <div class="lsidebar-pane" id="settings">
            <h1 class="lsidebar-header">Settings<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            <br>
            <div class="form-group">
                <label>Node type:</label><br>
                <input type="radio" name="node-type" value="net"> Networks
                <input type="radio" name="node-type" value="port" checked> Port Locations
            </div>
        </div>
    </div>
</div>

<div id="canvas" class="lsidebar-map"></div>

<?php ActiveForm::end();

Modal::begin([
    'id' => 'point-modal',
    'header' => 'Edit point',
    'footer' => '<button class="save-btn btn btn-primary">Save</button> <button class="cancel-btn btn btn-default">Cancel</button>',
]); ?>

<label class="point-order" hidden></label>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#normal" data-toggle="tab">Normal</a></li>
      <li><a href="#advanced" data-toggle="tab">Advanced</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="normal">
        <br>
        <?php $form = \yii\bootstrap\ActiveForm::begin(['id'=>'point-form', 'layout'=>'horizontal']);
        $pointForm = new \meican\circuits\forms\PointForm;

        echo $form->field($pointForm, 'domain')->dropDownList([],['disabled'=>true]); 
        echo $form->field($pointForm, 'network')->dropDownList([],['disabled'=>true]);
        echo $form->field($pointForm, 'location')->dropDownList([],['disabled'=>true]);  
        echo $form->field($pointForm, 'port')->dropDownList([],['disabled'=>true]); 
        echo $form->field($pointForm, 'vlan')->dropDownList([],['disabled'=>true]); 

        \yii\bootstrap\ActiveForm::end(); ?>
      </div>
      <!-- /.tab-pane -->
      <div class="tab-pane" id="advanced">
        <br>
        <?php $form = \yii\bootstrap\ActiveForm::begin(['id'=>'point-advanced-form']);
        $pointForm = new \meican\circuits\forms\PointForm;

        echo $form->field($pointForm, 'urn')->textInput(); 
        echo $form->field($pointForm, 'vlan_text')->textInput(); 

        \yii\bootstrap\ActiveForm::end(); ?>
      </div>
      <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'schedule-modal',
    'header' => 'Schedule Form',
    'footer' => '<button class="save-btn btn btn-primary">Save</button> <button class="remove-btn btn btn-danger">Remove</button> <button class="cancel-btn btn btn-default">Cancel</button>',
]); ?>

<div class="form-group"><br>
    <label>Date and time range:</label>
    <div class="input-group">
      <div class="input-group-addon">
        <i class="fa fa-clock-o"></i>
      </div>
      <input id="datetime-range" type="text" class="form-control">
    </div>
</div>

<?php Modal::end(); ?>
