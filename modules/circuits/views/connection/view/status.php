<?php 
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
?>

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