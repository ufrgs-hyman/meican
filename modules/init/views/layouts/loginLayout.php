<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use meican\components\AnalyticsWidget;
use meican\modules\init\assets\LoginAsset;

LoginAsset::register($this);

?>

<html>
    <head>
        <meta charset="utf-8"/>
        <title><?= Html::encode(Yii::$app->name); ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <link rel="shortcut icon" href="<?= Url::base(); ?>/images/favicon.ico" type="image/x-icon" />
        <?= Html::cssFile(Url::base().'/css/style.css'); ?>
        <?= Html::cssFile(Url::base().'/css/login.css'); ?>
        <script type="text/javascript" src="<?= Url::base(); ?>/js/jquery.js"></script>
        <script type="text/javascript" src="<?= Url::base(); ?>/js/init/login.js"></script>
        
    </head>
    <body>
        <div id="header" class="header">
            &nbsp;
        </div>
        
        <div id="content">
        	<div id="login_box">
				<div id="login_form" class="tab_content">
           			<?= $content; ?>
           		</div>
			    <div class="logos-footer">
			    	<div style="width: 33%;  float: left;">
			    		<a href="http://www.rnp.br" title="RNP" target="_blank"><img src="<?= Url::base(); ?>/images/rnp.gif" alt="RNP" style="height:42px;"/></a>
			    	</div>
			    	<div style="width: 33%;  float: left;">
			    		<a href="http://www.ufrgs.br" title="UFRGS" target="_blank"><img src="<?= Url::base(); ?>/images/support/ufrgs.png" alt="UFRGS" style="height:42px;"/></a>
			    	</div>
			    	<div style="width: 34%;  float: left;">
			    		<a href="http://networks.inf.ufrgs.br/" title="Computer Networks UFRGS" target="_blank"><img src="<?= Yii::$app->request->baseUrl; ?>/images/networks.jpg" alt="networks" style="height:42px;"/></a>
			    	</div>
			    </div>
			</div>
			            
			<div id="text_info" class="main-info">
				<div class="center"><img src="<?= Url::base(); ?>/images/meican_new.png" class="logo" alt="MEICAN"/></div>
				<h2>Management Environment of Inter-domain Circuits for Advanced Networks</h2>
				
				<p><?= Yii::t('init', 'MEICAN allows network end-users to request, in a more user-friendly way, dedicated circuits in ') ?>
				    <a target="blank" href="http://en.wikipedia.org/wiki/Dynamic_circuit_network"><?= Yii::t('init', 'Dynamic Circuit Networks') ?></a>. 
				   <?= Yii::t('init', 'MEICAN also enables network operators to evaluate and accept end-users circuit requests in environments with multiple domains. With MEICAN, you can:') ?></p>
				
				<ul>
				    <li>
				        <img src="<?= Url::base(); ?>/images/bullet1.jpg" alt="Bullet" class="bullet"/>
				        <b><?= Yii::t('init', 'Request Circuits') ?></b>
				        <p><?= Yii::t('init', 'Network end-users circuits can be scheduled to be set up and teared down when it is more convenient.') ?></p>
				    </li>
				    <li>
				        <img src="<?= Url::base(); ?>/images/bullet2.jpg" alt="Bullet" class="bullet"/>
				        <b><?= Yii::t('init', 'Authorize Requests') ?></b>
				        <p><?= Yii::t('init', 'Network operators can be notified to accept or reject the requests of establishment of new circuits.') ?></p>
				    </li>
				    <li>
				        <img src="<?= Url::base(); ?>/images/bullet3.jpg" alt="Bullet" class="bullet"/>
				        <b><?= Yii::t('init', 'Build Automated Policies') ?></b>
				        <p><?= Yii::t('init', 'Authorization workflows can be used to automate the decision-making process along the multiple domains where end-users circuits pass through.') ?></p>
				    </li>
				</ul>
			</div>
			           		
           			
        </div>
        
        <div id="footer">
            
        </div>
        
    </body>

<?= AnalyticsWidget::build(); ?>

</html>
