<?php
   /* $userLogin = $this->passedArgs->usr_login;
    $system_time = $this->passedArgs->system_time;*/
?>
<div id="info_box">
        <div id="loading" style="display: none"><span><?php echo _('Loading...'); ?></span></div>
    	<ul>
            <li><a href="<?php echo $this->url(array('app' => 'init', 'controller' => 'login', 'action' => 'logout'));?>"><?php echo _('Sign out'); ?> (<?php echo AuthSystem::getUserLogin(); ?>)</a></li>
        	<li><a href="<?php echo $this->buildLink(array('app'=>'aaa','controller'=>'users','action'=>'edit_settings')); ?>">
    <?php echo _('My account'); ?> </a></li>
            <li><a href="#"><?php echo _('Help'); ?></a></li>
            <li><a href="#"><?php echo _('About'); ?></a></li>
            <li><a href="#" class="feedback-link"><?php echo _('Feedback'); ?></a></li>
            <?php /*
            <li><a href="<?php echo $this->url(array('app' => 'init', 'controller' => 'gui', 'action' => 'language', 'pass' => array('pt_BR')));?>"><?php echo _('Porguguese'); ?></a></li>
            <li><a href="<?php echo $this->url(array('app' => 'init', 'controller' => 'gui', 'action' => 'language', 'pass' => array('en_US')));?>"><?php echo _('English'); ?></a></li>
             * 
             */?>
        </ul>
</div>
