<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\DetailView;
use yii\bootstrap\Html;

use meican\base\grid\Grid;

\meican\circuits\assets\connection\View::register($this);

$this->params['header'] = [Yii::t('circuits',"Circuit Details"), ['Home', 'Circuits']];

?>

<data id="circuit-id" value="<?= $conn->id; ?>"></data>
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div id="status" data-value="reservating" class="info-box">
        <span class="info-box-icon"><i class="ion ion-clock"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Status</span>
          <span class="info-box-number"><small>Waiting reservation</small></span>
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
          <span class="info-box-number"><small>Provisioned</small></span>
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
          <span class="info-box-number"><small>Approved</small></span>
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
          <span class="info-box-number"><small>20/03/2016 21:12<br>by Provider</small></span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
<!-- /.col -->
</div>

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
                        <li><a href="#">Edit circuit</a></li>
                        <li><a href="#">Cancel circuit</a></li>
                      </ul>
                    </div>
                  </div>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'id' => 'circuit-info',
                    'model' => $conn,
                    'attributes' => [
                        //'name',
                        //'date',
                        [                      
                            'label' => 'Bandwidth',
                            'format' => 'raw',
                            'value' => '20 Mbps'
                        ], 
                        [                      
                            'attribute' => 'start',
                            'format' => 'raw',
                            'value' => '<data class="start-time" value="'.$conn->start.'"></data>'.Yii::$app->formatter->asDatetime($conn->start)
                        ],                        
                        'finish:datetime',
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
                <?php echo Grid::widget([
                    'id'=> 'history-grid',
                    'dataProvider' => $history,
                    'columns' => array(
                        'created_at',
                        'type',
                        'author_id'
                        ),
                    ]);
                ?>
            </div>
        </div>    
    </div>
</div> 
 
