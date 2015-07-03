<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Notification;

?>

<ul>
	<li><?php if (!(Yii::$app->user->isGuest)) {
		echo Html::a(Yii::t("init", "Logout ({user.name})", ['user.name' => Yii::$app->session["user.name"]]), 
			array('/init/login/logout')); 
		}
	?></li>
	<li><?= Html::a(Yii::t("init", "My account"),array('/aaa/user/account')); ?></li>
	<li><?= Html::a(Yii::t("init", "Help"),array('/init/support/help')); ?></li>
	<li><?= Html::a(Yii::t("init", "About"),array('/init/support/about')); ?></li>
	<li class='feedback_link'><?= Html::a('Feedback'); ?></li>
	<li id="notification_li">
		<?php $nots = Notification::getNumberNotifications();	
			if($nots > 0) echo "<div class='requests_info' id='notification_link'><div class='full'><span>$nots</span></div></div>";
			else echo "<div class='requests_info' id='notification_link'><div class='empty'><span>0</span></div></div>";
		?>
		<div id="notification_container">
			<div id="notification_body">
				<ul id="notification_ul">
				</ul>
				<?= Html::img('@web'.'/images/ajax-loader.gif', ['id' => "notification_loader", 'style'=>'padding: 10px;']); ?>
			</div>
			<!--<div id="notification_footer1"><?= Html::a(Yii::t("notification", "See All"),array('/notification/notification/index')); ?></a></div>-->
			<div id="notification_footer2"><?= Html::a(Yii::t("notification", "See Authorizations")." (<span id='authN'>".Notification::getNumberAuthorizations()."</span>)",array('/circuits/authorization/index')); ?></div>
		</div>
	</li>
</ul>