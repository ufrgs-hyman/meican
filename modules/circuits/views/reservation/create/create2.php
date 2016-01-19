<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\Tabs;

use meican\circuits\assets\reservation\CreateAsset;

CreateAsset::register($this);

$this->params['header'] = ["Create a Reservation", ['Home', 'Reservation', 'Create']]

?>

<div class="box box-solid">
    <div class="box-body no-padding">
        <div class="nav-tabs-custom no-margin">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#map" data-toggle="tab">Map</a></li>
              <li><a href="#tab_2-2" data-toggle="tab">Graph</a></li>
              <li><a href="#tab_3-2" data-toggle="tab">Advanced Form</a></li>
            </ul>
            <div class="tab-content no-padding">
              <div class="tab-pane active" id="map">
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2-2">
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_3-2">
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
    </div>
    <div class="box-footer no-border">
        <div class="pull-right">
            <a href="#" class="btn btn-primary"><i class="fa fa-pencil"></i> Next step</a>
        </div>
    </div> 
</div>

