<div class="feedback-panel tab_content ui-corner-all" style="top: 27px; " id="feedback-panel">
    <div class="ui-state-default ui-corner-all feedback-link" style="float: left; margin: -20px -20px; cursor: pointer;">
        <span class="ui-icon ui-icon-closethick"></span>
    </div>
    <div id="emotion_select" style="left: 140px; top: 240px; display:none;" class="pos_right">
        <a href="#" class="<?= _("happy") ?>"><img alt="Feedback-happy" src="http://assets1.getsatisfaction.com/images/emoticons/feedback-happy.png?355ab45"><?= _("Happy") ?></a>
        <a href="#" class="<?= _("silly") ?>"><img alt="Feedback-silly" src="http://assets4.getsatisfaction.com/images/emoticons/feedback-silly.png?355ab45"><?= _("Silly") ?></a>
        <a href="#" class="<?= _("indifferent") ?>"><img alt="Feedback-indifferent" src="http://assets4.getsatisfaction.com/images/emoticons/feedback-indifferent.png?355ab45"><?= _("Indifferent") ?></a>
        <a href="#" class="<?= _("sad") ?>"><img alt="Feedback-sad" src="http://assets3.getsatisfaction.com/images/emoticons/feedback-sad.png?355ab45"><?= _("Sad") ?></a>
    </div>
    <form method="post" action="http://inf.ufrgs.br/~labianchin/meican/feedback.php">
        <h1><?= _("Send Us Feedback") ?></h1>


        <fieldset id="topic_details" class="ui-widget ui-corner-all">

            <ul class="clearfix" id="feedback-tabs">
                <li class="idea active" style="">
                    <a href="#"><strong><?= _("Idea") ?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
                <li class="question" style="">
                    <a href="#"><strong><?= _("Question") ?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
                <li class="problem " style="">
                    <a href="#"><strong><?= _("Problem") ?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
                <li class="praise last " style="">
                    <a href="#"><strong><?= _("Praise") ?></strong></a>
                    <img alt="Feedback_tab_arrow" src="http://assets2.getsatisfaction.com/images/feedback_tab_arrow.png?355ab45">
                </li>
            </ul>

            <div><input id="topic_style" name="topic[style]" type="hidden" value="idea" class="ui-widget ui-widget-content"></div>
            <div class="row text_box">
                <label class="prompted" for="topic_additional_detail" id="topic_additional_detail_label">...</label>
                <textarea class="additional_detail text ui-widget ui-widget-content" cols="35" id="topic_additional_detail" name="topic[additional_detail]" rows="5" tabindex="1" style="margin-left: 0px; margin-right: 0px; width: 318px; margin-top: 0px; margin-bottom: 0px; height: 70px; "></textarea>
            </div>
            <div class="row text_box">
                <label class="prompted" for="topic_subject"><?= _("Sum it up with a short title") ?></label>
                <input class="subject text ui-widget ui-widget-content" id="topic_subject" name="topic[subject]" tabindex="2" type="text">
            </div>
            <div class="row text_box" style="z-index:10">
                <label for="topic_emotitag_feeling" id="emotion_label"><?= _("It makes me feel:") ?></label>
                <div id="emotion_picker">
                    <a href="#" id="emotion_selected"><img alt="Feedback-happy" src="http://assets1.getsatisfaction.com/images/emoticons/feedback-happy.png?b829cae"></a>
                    <a href="#" id="emotion_activate"></a>
                    <input id="topic_emotitag_feeling" name="topic[emotitag][feeling]" size="14" style="float: left;padding:2px 4px;" tabindex="6" type="text" class="ui-widget ui-widget-content">
                </div>
                <div><input id="topic_emotitag_face" name="topic[emotitag][face]" type="hidden" class="ui-widget ui-widget-content"></div>
            </div>
            <div class="row clearfix" id="submit_row">
                <input class="submit ui-button ui-widget ui-state-default ui-corner-all" id="topic_submit" name="commit" tabindex="7" type="submit" value="<?= _("Send") ?>" role="button" aria-disabled="false">
            </div>
        </fieldset>


    </form>  
</div>

<script type="text/javascript">
    var feedback_descrbs = {
        idea : "<?= _("Describe your idea") ?>",
        question: "<?= _("Describe your question") ?>",
        praise: "<?= _("Describe your praise") ?>",
        problem: "<?= _("Describe your problem") ?>"
    };
</script>