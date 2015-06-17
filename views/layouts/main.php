<?php
use yii\helpers\Html;
use app\assets\MeicanAsset;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

MeicanAsset::register($this);
?>
<?php $this->beginPage() ?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title><?= Html::encode(Yii::$app->name); ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <link rel="shortcut icon" href="<?= Url::base(); ?>/images/favicon.ico" type="image/x-icon" />
        
		<script type="text/javascript">
			window.baseUrl = "<?= Url::base(); ?>";
			var language = "<?= Yii::$app->language; ?>";
		</script>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <body>
        <div class="fade-overlay" id="MainOverlay"> </div>
        <div id="left-panel">
            <div id="logo">
            	<img src="<?= Url::base(); ?>/images/meican_branco.png" class="logo" alt="MEICAN"/>
            </div>
            <div id="menu">
            	<?= $this->render('menu'); ?>
            </div>            
            <div id="system_date">
            </div>
        </div>

        <div id="canvas">
        	<div id="info_box">
        		<?= $this->render('infoBox'); ?>
        	</div>
            <div id="workspace">
            	<div class="tab-overlay fade-overlay" style="display: hidden;"> </div>
				<div id="map-canvas" style="width:100%; height:100%; float:left; overflow:hidden; display:none;"></div>
                <div id="main">
                	<?php
                		//Messages to inform user actions
						$flashMessages = Yii::$app->getSession()->getAllFlashes();
						if ($flashMessages) {
						    echo '<div id="flash_box" class="ui-widget">';
						    foreach($flashMessages as $key => $messages) {
								if (is_array($messages)) {
									foreach ($messages as $message) {
										showMessage($key, $message);
									}
								} else {
									showMessage($key, $messages);
								}
						    }
						    echo '</div>';
						}

						function showMessage($class, $message) {
							echo '<div class="'.$class.'"><p>';
							echo '<span class="ui-icon ui-icon-closethick close-button" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);">x</span>';
							echo $message;
							echo '</p></div>';
						}
					?>
					
                    <?= $content ?> 
                    <br></br> 
					<br></br>
                </div>
                <?= $this->render('feedback'); ?>
            </div>
            <div style="clear:both;"></div>
        </div>
        
        <div id="main-dialog" title="<?= Yii::t('init', 'Confirm'); ?>" hidden>
        <br><?= Yii::t('init', 'The selected items will be deleted.'); ?><br><?= Yii::t('init', 'Do you confirm?'); ?>
        <label id="yes-button-label" hidden><?= Yii::t('init', 'Yes'); ?></label>
        <label id="no-button-label" hidden><?= Yii::t('init', 'No'); ?></label>
        </div>

<?php $this->endBody() ?>
</body>
<?php if (Yii::$app->params['google.analytics.enabled']): ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?= Yii::$app->params['google.analytics.key'];?>', 'auto');
  ga('send', 'pageview');

</script>
<?php endif; ?>
</html>
<?php $this->endPage() ?>
