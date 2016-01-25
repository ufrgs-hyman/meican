<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\helpers\Html;

$this->params['header'] = ["About", ['Home', 'About']];

?>
      <div class="row">
        <!-- left column -->
        <div class="col-md-6">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">The Project</h3>
            </div>
            <div class="box-body">
                <p><?= Html::img("@web/images/meican_new.png", ['style'=>'width: 300px;','title' => 'MEICAN']); ?></p>
                <p><b>Management Environment of Inter-domain Circuits for Advanced Networks</b> <?= Yii::t("home", 'is a Web application that enables users to request VCs between well-defined end-points that, depending on operation policies and human authorisation located in the intermediate domains that connect source and destination end-points.'); ?></p>
                <p><?= Yii::t("home", 'Our solution uses Business Process Management (BPM) concepts for managing the VCs establishment process, since VC requested by end-user to network devices configurations.'); ?></p>
                <p><?= Yii::t("home", 'The main contribution of the proposed solution is to provide dynamic authorization strategies composed for policies and human support.'); ?></p>
            </div>
          </div>
          <!-- /.box -->

          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Information</h3>
            </div>
            <div class="box-body">
              <b><?= Yii::t("home", 'Version');?></b>
              <p><?= Yii::$app->version; ?></p>

              <b><?= Yii::t("home", 'Documentation'); ?></b>
              <p><?= Yii::t("home", 'The documentation is only in portuguese and is available on <a href="{url}" target="blank">RNP Wiki</a>.</p>', ['url'=> 'https://wiki.rnp.br/display/secipo/Guia+MEICAN']); ?>

              <b><?= Yii::t("home", 'License'); ?></b>
              <p><?= Yii::t("home", 'MEICAN is licenced under BSD2 License.');?></p>
              <b><?= Yii::t("home", 'Source code');?></b>
              <p><?= Yii::t("home", 'The project is hosted by');?> <a href="https://github.com/ufrgs-hyman/meican2" target="blank">GitHub</a>.</p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        </div>
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-6">
          <!-- Horizontal Form -->
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Team</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              <div class="box-body">
                <p class="text-center">
                <a href="https://www.rnp.br" target="_blank"><?= Html::img("@web/images/rnp.png", 
                ['style'=>'height: 60px;','title' => 'Rede Nacional de Ensino e Pesquisa']); ?></a>
                <a href="https://www.ufrgs.br" target="_blank"><?= Html::img("@web/images/support/ufrgs.png", 
                ['style'=>'height: 60px;margin-left: 10%; margin-right: 10%;','title' => 'Federal University of Rio Grande do Sul']); ?></a>
                <a href="https://networks.inf.ufrgs.br" target="_blank"><?= Html::img("@web/images/networks.jpg", 
                ['style'=>'height: 60px;','title' => 'UFRGS Computer Networks Group']); ?></a></p>

                <b><?= Yii::t("home", 'UFRGS Developers'); ?></b>
                <p>
                <table>
                <tr>
                    <td>Maurício Quatrin Guerreiro (mqguerreiro at inf.ufrgs.br) <a target="_blank" href="https://github.com/mqgmaster"><i class="fa fa-github"></i></a></td>
                </tr>
                <tr>
                    <td>Diego Pittol (diegokindin at gmail.com) <a target="_blank" href="https://github.com/DiegoPittol"><i class="fa fa-github"></i></a></td>
                </tr>
                </table>
                </p>
                <b><?= Yii::t("home", 'UFRGS Coordinators'); ?></b>
                <p>
                <table>
                <tr>
                    <td>Lisandro Zambenedetti Granville (granville at inf.ufrgs.br)</td>
                </tr>
                <tr>
                    <td>Luciano Paschoal Gaspary (paschoal at inf.ufrgs.br)</td>
                </tr>
                <tr>
                    <td>Juliano Araujo Wickboldt (jwickboldt at inf.ufrgs.br) <a target="_blank" href="https://github.com/julianowick"><i class="fa fa-github"></i></a></td>
                </tr>
                </table></p>
                <b><?= Yii::t("home", 'RNP Coordinators'); ?></b>
                <p>
                <table>               
                <tr>
                    <td>Alex Soares Moura</td>
                </tr>
                <tr>
                    <td>Marcos Felipe Schwarz</td>
                </tr>
                </table></p>
                
                <p><b><?= Yii::t("home", 'Previous developers');?></b></p>
                <p>
                <table>
                <tr>
                    <td>Henrique Resende</td>
                </tr>
                <tr>
                    <td>Pedro Isolani <a target="_blank" href="https://github.com/phisolani"><i class="fa fa-github"></i></a></td>
                </tr>
                <tr>
                    <td>Luine Gallois</td>
                </tr>
                <tr>
                    <td>Augusto Ferreira</td>
                </tr>
                <tr>
                    <td>Leonardo Faganello <a target="_blank" href="https://github.com/lfaganello"><i class="fa fa-github"></i></a></td>
                </tr>
                <tr>
                    <td>Luís Armando Bianchin <a target="_blank" href="https://github.com/labianchin"><i class="fa fa-github"></i></a></td>
                </tr>
                <tr>
                    <td>Jair Santanna <a target="_blank" href="https://github.com/jjsantanna"><i class="fa fa-github"></i></a></td>
                </tr>
                <tr>
                    <td>Felipe Nesello <a target="_blank" href="https://github.com/fanesello"><i class="fa fa-github"></i></a></td>
                </tr>
                <tr>
                    <td>Pietro Biasuz</td>
                </tr>
                </table>
                </p>
              </div>
              <!-- /.box-body -->
          </div>
          <!-- /.box -->
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Related projects</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body text-center">
              <a href="https://www.ogf.org" target="_blank"><?= Html::img("@web/images/ogf.gif", 
              ['style'=> 'margin-right: 10%;','title' => 'Open Grid Forum']); ?></a>
              <a href="https://www.glif.is" target="_blank"><?= Html::img("@web/images/glif.png", 
              ['title' => "Global Lambda Integrated Facility"]); ?></a>
              <a href="https://icons8.com">Icon pack by Icons8</a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
