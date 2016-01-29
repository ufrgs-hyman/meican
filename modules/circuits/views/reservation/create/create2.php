<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;

use meican\circuits\assets\reservation\CreateAsset;

CreateAsset::register($this);

$this->params['hide-content-section'] = true;
$this->params['hide-footer'] = true; 

?>

<div id="lsidebar" class="lsidebar collapsed">
    <!-- Nav tabs -->
    <div class="lsidebar-tabs">
        <ul role="tablist">
            <li><a href="#help" role="tab"><i class="fa fa-info-circle"></i></a></li>
            <li><a href="#home" role="tab"><i class="fa"><img src="https://maxcdn.icons8.com/Android_L/PNG/24/Maps/route-24.png" width="21"></i></a></li>
            <li><a href="#requirements" role="tab"><i class="fa fa-sliders"></i></a></li>
            <li><a href="#calendar" role="tab"><i class="fa fa-calendar"></i></a></li>
            <li><a href="#check" role="tab"><i class="fa fa-check danger"></i></a></li>
        </ul>

        <ul role="tablist">
            <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
        </ul>
    </div>

    <!-- Tab panes -->
    <div class="lsidebar-content">
        <div class="lsidebar-pane" id="home">
            <h1 class="lsidebar-header">
                Step 1: Path
                <span class="lsidebar-close"><i class="fa fa-caret-left"></i></span>
            </h1>
            <br>
            <div class="nav-tabs-custom" style="margin-right: 15px;">
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-search"></i> on map</a></li>
                  <li><a id="add-waypoint" href="#"><i class="fa fa-plus"></i></a></li>
                  <li><a href="#"><i class="fa fa-file-text"></i></a></li>
                </ul>
                <div class="tab-content">
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
                    <li class="path-point">
                        <!-- timeline icon -->
                        <i class="fa fa-map-marker bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">
                                none
                                <div class="pull-right">
                                    <a href="#" class="text-muted"><i class="fa fa-minus"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-up"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-down"></i></a>
                                </div>
                          </h3>
                        <div class="timeline-body">
                            <div class="point-default">
                              Network: <label class="point-net">none</label><br>
                              Device: <label class="point-dev">none</label><br>
                              Port: <label class="point-port">none</label><br>
                              <input type="hidden" name="ReservationForm[path][port][]">
                            </div>
                            <div class="point-advanced" hidden>
                              URN: <label class="point-urn">none</label><br>
                              <input type="hidden" name="ReservationForm[path][urn][]">
                            </div>
                            VLAN: <label class="point-vlan">Auto</label>
                            <input type="hidden" name="ReservationForm[path][vlan][]">
                            <div class="pull-right">
                                <a href="#" class="text-muted"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="text-muted"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                      </div>
                    </li>
                    <li class="path-point">
                        <!-- timeline icon -->
                        <i class="fa fa-map-marker bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">
                                <label class="point dom">none</label>
                                <div class="pull-right">
                                    <a href="#" class="text-muted"><i class="fa fa-minus"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-up"></i></a>
                                    <a href="#" class="text-muted"><i class="fa fa-arrow-down"></i></a>
                                </div>
                            </h3>
                            <div class="timeline-body">
                                <div class="point-default">
                                  Network: <label class="point net">none</label><br>
                                  Device: <label class="point dev">none</label><br>
                                  Port: <label class="point port">none</label><br>
                                  <input class="port-input" type="hidden" name="ReservationForm[path][port][]">
                                </div>
                                <div class="point-advanced" hidden>
                                  URN: <label class="point urn">none</label><br>
                                  <input class="urn-input" type="hidden" name="ReservationForm[path][urn][]">
                                </div>
                                VLAN: <label class="point vlan">Auto</label>
                                <input class="vlan-input" type="hidden" name="ReservationForm[path][vlan][]">
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
            <div class="pull-right">
                <button type="button" class="btn btn-primary"><span class="fa fa-arrow-right"></span> Next step</button>
            </div><br><br><br>
        </div>

        <div class="lsidebar-pane" id="requirements">
            <h1 class="lsidebar-header">Step 2: Requirements<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            <br>
            <div class="form-group">
                <label>Bandwidth</label>
                <div class="input-group">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-primary"><span class="fa fa-minus"></span></button>
                    </div>
                    <input type="text" class="form-control" placeholder="Mbps">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-primary"><span class="fa fa-plus"></span></button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Protection</label> <i class="fa fa-question-circle" data-toggle="tooltip" title="A protected circuit means that you accept losing the guaranteed bandwidth, but requires availability of the service."></i>
                <br>
                <input type="checkbox" checked data-toggle="toggle">
            </div>
            <br>
            <div class="pull-right">
                <button type="button" class="btn btn-primary"><span class="fa fa-arrow-right"></span> Next step</button>
            </div> 
        </div>

        <div class="lsidebar-pane" id="calendar">
            <h1 class="lsidebar-header">Step 3: Schedule<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            gdfgdfgfg
        </div>

        <div class="lsidebar-pane" id="check">
            <h1 class="lsidebar-header">Step 4: Confirmation<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            xvdfd
        </div>

        <div class="lsidebar-pane" id="help">
            <h1 class="lsidebar-header">Welcome to reservation page<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
            <br><p>
                This is the reservation page. In this page a map is used for friendly search
                of endpoints. A search text field also is available. The interval or the recurrence of
                the reservation are available on the respective option in the map sidebar. The below list
                contains all steps involved in a reservation request.
            </p><br>
            <p>1. Select your endpoints.</p>
            <p>2. Define the requirements, e.g., bandwidth.</p>
            <p>3. Set the duration of the circuit.</p>
            <p>4. Confirm your request and submit.</p>

            <div class="pull-right">
                <button type="button" class="btn btn-primary"><span class="fa fa-arrow-right"></span> Start</button>
            </div>
        </div>

        <div class="lsidebar-pane" id="settings">
            <h1 class="lsidebar-header">Settings<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
        </div>
    </div>
</div>

<div id="canvas" class="lsidebar-map"></div>
