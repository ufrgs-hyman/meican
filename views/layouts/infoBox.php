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
	<li id="feedback_link" class='feedback_link'>
		<div id="feedback_li">
			<?= Html::a('Feedback'); ?>
		</div>
		<div id="feedback_panel">
		    <div id="emotion_select" class="pos_right" style="display: none;">
		        <a href="#" class="happy"><?= Html::img('@web'.'/images/emotion_happy.png');?><?= Yii::t("init", 'Happy');?></a>
		        <a href="#" class="silly"><?= Html::img('@web'.'/images/emotion_silly.png');?><?= Yii::t("init", 'Silly');?></a>
		        <a href="#" class="indifferent"><?= Html::img('@web'.'/images/emotion_indifferent.png');?><?= Yii::t("init", 'Indifferent');?></a>
		        <a href="#" class="sad"><?= Html::img('@web'.'/images/emotion_sad.png');?><?= Yii::t("init", 'Sad');?></a>
		    </div>
		    
		    <form id="feedback_form">
		        <h1><?= Yii::t("init", 'Send Us Feedback');?></h1>
		
		        <fieldset id="topic_details" class="ui-widget ui-corner-all">
		
		            <ul class="clearfix" id="feedback-tabs">
		                <li class="idea active" style="">
		                    <a href="#"><strong><?= Yii::t("init", 'Idea');?></strong></a>
		                </li>
		                <li class="question" style="">
		                    <a href="#"><strong><?= Yii::t("init", 'Question');?></strong></a>
		                </li>
		                <li class="problem " style="">
		                    <a href="#"><strong><?= Yii::t("init", 'Problem');?></strong></a>
		                </li>
		                <li class="praise last " style="">
		                    <a href="#"><strong><?= Yii::t("init", 'Praise');?></strong></a>
		                </li>
		            </ul>
		
		            <div><input id="topic_style" name="topic_style" type="hidden" value="idea" class="ui-widget ui-widget-content"></div>
		            
		            <div class="row text_box">
		                <textarea class="additional_detail text ui-widget ui-widget-content" id="topic_additional_detail" name="topic_additional_detail" rows="5" tabindex="1" placeholder="Describe your idea"></textarea>
		            </div>
		            
		            <div class="row text_box">
		                <input class="subject text ui-widget ui-widget-content" id="topic_subject" name="topic_subject" tabindex="2" type="text" placeholder="<?= Yii::t("init", 'Sum it up with a short title');?>"/>
		            </div>
		            
		            <div class="row text_box" style="z-index:10">
		                <div id="emotion_picker">
		                    <input id="topic_emotitag_feeling" name="topic_emotitag_feeling" size="22" placeholder="<?= Yii::t("init", 'It makes me feel:');?>"/>
		                	<a href="#" id="emotion_selected"><?= Html::img('@web'.'/images/emotion_happy.png', ['id' => "emotion_selected_img"]);?></a>
		                </div>
		                <div>
		                	<input id="topic_emotitag_face" name="topic_emotitag_face" type="hidden" class="ui-widget ui-widget-content">
		                </div>
		            </div>
		            
		            <div class="row clearfix" id="submit_row">
		            	<?= Html::button(Yii::t('circuits', 'Answer'), ['onclick' => "sendFeedback()"]);?>
		            </div>
		            
		        </fieldset>
		
		    </form>
		    
		</div>
	</li>
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