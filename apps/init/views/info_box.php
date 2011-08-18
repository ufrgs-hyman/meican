<?php
    $userLogin = $this->passedArgs;
?>

<a href="<?php echo $this->buildLink(array('app'=>'aaa','controller'=>'users','action'=>'edit_settings')); ?>">
    <?php echo _('My settings'); ?> </a> |

<a href="#">
    <?php echo _('Help'); ?> </a> |

<a href="#">
    <?php echo _('About'); ?> </a> |    
    
<a href="main.php?app=init&controller=login&action=logout">
    <?php echo _('Sign out'); ?>
</a> (<?php echo $userLogin; ?>)
