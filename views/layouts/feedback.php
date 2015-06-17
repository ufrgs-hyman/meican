<?php 
use yii\helpers\Url;
use yii\helpers\Html;
?>

<?= Html::csrfMetaTags() ?>

<div class="feedback-panel tab_content ui-corner-all" style="top: 27px; display: none;" id="feedback-panel">
 
    <div class="ui-state-default ui-corner-all feedback-link" style="float: left; margin: -20px -20px; cursor: pointer;">
    	<span id='closeButtonFeedback' class="ui-icon ui-icon-closethick"></span>
    </div>
    
    <div id="emotion_select" style="left: 140px; top: 240px; display:none;" class="pos_right">
        <a href="#" class="happy"><img alt="Feedback-happy" src="http://assets1.getsatisfaction.com/images/emoticons/feedback-happy.png?355ab45"><?= Yii::t("init", 'Happy');?></a>
        <a href="#" class="silly"><img alt="Feedback-silly" src="http://assets4.getsatisfaction.com/images/emoticons/feedback-silly.png?355ab45"><?= Yii::t("init", 'Silly');?></a>
        <a href="#" class="indifferent"><img alt="Feedback-indifferent" src="http://assets4.getsatisfaction.com/images/emoticons/feedback-indifferent.png?355ab45"><?= Yii::t("init", 'Indifferent');?></a>
        <a href="#" class="sad"><img alt="Feedback-sad" src="http://assets3.getsatisfaction.com/images/emoticons/feedback-sad.png?355ab45"><?= Yii::t("init", 'Sad');?></a>
    </div>
    
    <form method="post" action="<?= Url::toRoute('init/gui/sendMail');?>">
        <h1><?= Yii::t("init", 'Send Us Feedback');?></h1>

        <fieldset id="topic_details" class="ui-widget ui-corner-all">

            <ul class="clearfix" id="feedback-tabs">
                <li class="idea active" style="">
                    <a href="#"><strong><?= Yii::t("init", 'Idea');?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
                <li class="question" style="">
                    <a href="#"><strong><?= Yii::t("init", 'Question');?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
                <li class="problem " style="">
                    <a href="#"><strong><?= Yii::t("init", 'Problem');?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
                <li class="praise last " style="">
                    <a href="#"><strong><?= Yii::t("init", 'Praise');?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
            </ul>

            <div><input id="topic_style" name="topic_style" type="hidden" value="idea" class="ui-widget ui-widget-content"></div>
            <div class="row text_box">
                <textarea class="additional_detail text ui-widget ui-widget-content" cols="35" id="topic_additional_detail" name="topic_additional_detail" rows="5" tabindex="1" style="margin-left: 0px; margin-right: 0px; width: 318px; margin-top: 0px; margin-bottom: 0px; height: 70px; " placeholder="Describe your idea"></textarea>
            </div>
            <div class="row text_box">
                <input class="subject text ui-widget ui-widget-content" id="topic_subject" name="topic_subject" tabindex="2" type="text" placeholder="<?= Yii::t("init", 'Sum it up with a short title');?>"/>
            </div>
            <div class="row text_box" style="z-index:10">
                <div id="emotion_picker">
                    <a href="#" id="emotion_selected"><img alt="Feedback-happy" src="http://assets1.getsatisfaction.com/images/emoticons/feedback-happy.png?b829cae"></a>
                    <a href="#" id="emotion_activate"></a>
                    <input id="topic_emotitag_feeling" name="topic_emotitag_feeling" size="22" style="float: left;padding:2px 4px;" tabindex="6" type="text" class="ui-widget ui-widget-content" placeholder="<?= Yii::t("init", 'It makes me feel:');?>"/>
                </div>
                <div><input id="topic_emotitag_face" name="topic_emotitag_face" type="hidden" class="ui-widget ui-widget-content"></div>
            </div>
            <div class="row clearfix" id="submit_row">
                <input class="submit ui-button ui-widget ui-state-default ui-corner-all" id="topic_submit" name="commit" tabindex="7" type="submit" value=<?= Yii::t("init", 'Send');?> role="button" aria-disabled="false">
            </div>
        </fieldset>

    </form>
    
</div>