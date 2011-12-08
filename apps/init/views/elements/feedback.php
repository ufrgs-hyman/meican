<div class="tab_content feedback-panel ui-corner-all">
    <form method="post" action="<?php $this->url(array('app' => 'init', 'controller' => 'mail'));?>">
    <h1><?php echo _('Send Us Feedback'); ?></h1>

    <fieldset id="topic_details">

        <ul class="clearfix" id="feedback-tabs">
            <li class="idea active" style="">
                <a href="#"><strong>Idea</strong></a>
            </li>
            <li class="question" style="">
                <a href="#"><strong>Question</strong></a>
            </li>
            <li class="problem " style="">
                <a href="#"><strong>Problem</strong></a>
            </li>
            <li class="praise last " style="">
                <a href="#"><strong>Praise</strong></a>
            </li>
        </ul>

        <div><input id="topic_style" name="topic[style]" type="hidden" value="idea"></div>
        <div class="row text_box">
            <label class="prompted" for="topic_additional_detail" id="topic_additional_detail_label">Describe your idea</label>
            <textarea class="additional_detail text" cols="35" id="topic_additional_detail" name="topic[additional_detail]" rows="5" tabindex="1" style="margin-left: 0px; margin-right: 0px; width: 318px; margin-top: 0px; margin-bottom: 0px; height: 70px; "></textarea>
        </div>
        <div class="row text_box">
            <label class="prompted" for="topic_subject">Sum it up with a short title</label>
            <input class="subject text" id="topic_subject" name="topic[subject]" tabindex="2" type="text">
        </div>
        <div class="row text_box" style="z-index:10">
            <input id="more_details_field" style="position: absolute; border: none; left:-1000px" tabindex="3" type="text"/>
            <label for="topic_emotitag_feeling" id="emotion_label">It makes me feel:</label>
            <div id="emotion_picker">
                <a href="#" id="emotion_selected" onclick="toggleEmotionSelect(this); return false;"><img alt="Feedback-happy" src="http://assets1.getsatisfaction.com/images/emoticons/feedback-happy.png?b829cae"></a>
                <a href="#" id="emotion_activate" onclick="toggleEmotionSelect('emotion_selected'); return false;"></a>
                <input id="topic_emotitag_feeling" name="topic[emotitag][feeling]" size="14" style="float: left;padding:2px 4px;" tabindex="6" type="text">
            </div>
            <div><input id="topic_emotitag_face" name="topic[emotitag][face]" type="hidden"></div>
        </div>
        <div class="row clearfix" id="submit_row">
            <input class="submit" id="topic_submit" name="commit" tabindex="7" type="submit" value="Continue">
        </div>
    </fieldset>
<?php /*
    <div id="form-wrap">
            <?php echo _('Message: '); ?><br />
            <textarea id="message" name="message" rows="10" cols="50" style="width:90%;padding:5px;" placeholder="<?php echo "Put here your feedback"; ?>"></textarea><br />
            <input type="submit" class="button" />
    </div>  */?> 
    
    </form>  
</div>
<?php //echo "Put here your feedback"; ?>
