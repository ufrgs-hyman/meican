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

<?php if(false): ?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-default">
            <div class="box-header with-border">
                 <h3 class="box-title">Path</h3>
            </div>
            <div class="box-body">
                <form role="form">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Source</label>
                      <div class="input-group">
                        <!-- /btn-group -->
                        <input type="text" class="form-control" placeholder="Enter a domain, device, port or URN">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-primary"><span class="fa fa-search"></span></button>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Destination</label>
                      <div class="input-group">
                        <!-- /btn-group -->
                        <input type="text" class="form-control" placeholder="Enter a domain, device, port or URN">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-primary"><span class="fa fa-search"></span></button>
                        </div>
                      </div>
                    </div>
                </form>
            </div>
            <div class="box-footer">
                <button type="button" class="btn btn-primary"><span class="fa fa-plus"></span> Add waypoint</button>
            </div>
        </div>
    </div>
    <div class="col-md-9">
    <?php endif;?>

        <div id="lsidebar" class="lsidebar collapsed">
        <!-- Nav tabs -->
        <div class="lsidebar-tabs">
            <ul role="tablist">
                <li><a href="#help" role="tab"><i class="fa fa-info-circle"></i></a></li>
                <li><a href="#home" role="tab"><i class="fa fa-search"></i></a></li>
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
                    Step 1: Select your endpoints
                    <span class="lsidebar-close"><i class="fa fa-caret-left"></i></span>
                </h1>

                <br>
                <div>
                    <ul class="timeline">

                    <li class="time-label">
                          <span class="bg-gray">
                            <i class="fa fa-laptop"></i>
                            Source
                          </span>
                    </li>
                    <!-- timeline item -->
                    <li>
                        <!-- timeline icon -->
                        <i class="fa fa-map-marker bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header no-padding">
                                <div class="input-group input-group-sm">
                                <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Enter a domain, device, port or URN">
                                    <div class="input-group-btn">
                                      <button type="button" class="btn btn-primary"><span class="fa fa-search"></span></button>
                                    </div>
                                  </div>
                          </h3>
                         <div class="timeline-body">
                          Domain<br>Network<br>Device<br>Port<br>VLAN
                        </div>
                        <div class="timeline-footer">
                          <a class="btn btn-danger btn-xs">Delete</a>
                        </div>
                      </div>
                    </li>
                    <li>
                        <!-- timeline icon -->
                        <i class="fa fa-map-marker bg-gray"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header no-padding">
                                <div class="input-group input-group-sm">
                                <!-- /btn-group -->
                                    <input type="text" class="form-control" placeholder="Enter a domain, device, port or URN">
                                    <div class="input-group-btn">
                                      <button type="button" class="btn btn-primary"><span class="fa fa-search"></span></button>
                                    </div>
                                  </div>
                            </h3>
                        </div>
                    </li>
                    <!-- END timeline item -->
                    <li class="time-label">
                          <span class="bg-gray">
                            <i class="fa fa-laptop"></i>
                            Destination
                          </span>
                    </li>
                    </ul>
                </div>
                
                <div class="pull-right">
                    <button type="button" class="btn btn-default"><span class="fa fa-plus"></span> Add waypoint</button>
                    <button type="button" class="btn btn-primary"><span class="fa fa-arrow-right"></span> Next step</button>
                </div>
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
            </div>

            <div class="lsidebar-pane" id="check">
                <h1 class="lsidebar-header">Step 4: Confirmation<span class="lsidebar-close"><i class="fa fa-caret-left"></i></span></h1>
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
