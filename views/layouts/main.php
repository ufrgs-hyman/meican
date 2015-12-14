<?php
use yii\helpers\Html;
use app\assets\MeicanAsset;
use yii\helpers\Url;
use app\components\AnalyticsWidget;

/* @var $this \yii\web\View */
/* @var $content string */

MeicanAsset::register($this);

?>
<!doctype html>
<?php $this->beginPage() ?>
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

<?= AnalyticsWidget::build(); ?>

</html>
<?php $this->endPage() ?>
