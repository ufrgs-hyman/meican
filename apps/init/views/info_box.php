<?php
    $userLogin = $this->passedArgs;
?>
<a href="#">
    <?php echo _('Help'); ?> </a> |
<a href="<?php echo $this->buildLink(array('app'=>'aaa','controller'=>'users','action'=>'edit_settings')); ?>">
    <?php echo _('My Settings'); ?> </a> |
<a href="main.php?app=init&controller=login&action=logout">
    <?php echo _('Sign Out'); ?>
</a> (<?php echo $userLogin; ?>)
