<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ConnectionAuth;

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
	<a href="<?= Url::toRoute("/circuits/authorization/index"); ?>">
	<?php $auths = ConnectionAuth::getNumberAuth();	
		if($auths > 0) echo "<li class='requests_info' id='numberAuths'><div class='full'><span >$auths</span></div></li>";
		else echo "<li class='requests_info' id='numberAuths'><div class='empty'><span>0</span></div></li>";
		?>
	</a>
</ul>