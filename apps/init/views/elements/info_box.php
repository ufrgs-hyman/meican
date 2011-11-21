<?php
   /* $userLogin = $this->passedArgs->usr_login;
    $system_time = $this->passedArgs->system_time;*/
?>
<div id="info_box">
    	<ul>
            <li><a href="<?php echo $this->url(array('app' => 'init', 'controller' => 'login', 'action' => 'logout'));?>"><?php echo _('Sign out'); ?> (<?php echo AuthSystem::getUserLogin(); ?>)</a></li>
        	<li><a href="<?php echo $this->buildLink(array('app'=>'aaa','controller'=>'users','action'=>'edit_settings')); ?>">
    <?php echo _('My account'); ?> </a></li>
            <li><a href="#"><?php echo _('Help'); ?></a></li>
            <li><a href="#"><?php echo _('About'); ?></a></li>
            <li><a href="#" class="feedback-link"><?php echo _('Feedback'); ?></a></li>
        </ul>
</div>
