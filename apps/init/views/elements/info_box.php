<?php
   /* $userLogin = $this->passedArgs->usr_login;
    $system_time = $this->passedArgs->system_time;*/
?>

<a href="<?php echo $this->buildLink(array('app'=>'aaa','controller'=>'users','action'=>'edit_settings')); ?>">
    <?php echo _('My settings'); ?> </a> |

<a href="#">
    <?php echo _('Help'); ?> </a> |

<a href="#">
    <?php echo _('About'); ?> </a> |    
    
<a href="<?php echo $this->url(array('app' => 'init', 'controller' => 'login', 'action' => 'logout'));?>">
    <?php echo _('Sign out'); ?>
</a> (<?php echo AuthSystem::getUserLogin(); ?>) |

<label title="<?php echo _("Server time"); ?>" id="system_time">
    <?php echo date("d/m/Y H:i"); ?>
</label>
