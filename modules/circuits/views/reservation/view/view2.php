<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\widgets\DetailView;

use meican\base\grid\Grid;
use meican\base\widgets\GridButtons;

\meican\circuits\assets\reservation\View::register($this);

$this->params['header'] = [Yii::t('circuits',"Circuit Details"), ['Home', 'Circuits']];

?>

<div class="box box-default">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#canvas" data-toggle="tab">Viewer</a></li>
          <li><a href="#stats" data-toggle="tab">Stats</a></li>
        </ul>
        <div class="tab-content no-padding">
          <div class="tab-pane active" id="canvas">
          </div>
          <div class="tab-pane" id="stats">
            Comming soon.
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
                    'model' => $reservation,
                    'attributes' => [
                        'name',
                        'date',
                        'bandwidth',
                        'protected',
                        'start',
                        'finish',
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
                    'dataProvider' => $connHistory,
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
 
