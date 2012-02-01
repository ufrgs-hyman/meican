<?php $user = $argsToElement; ?>
<?php
	$url = "http://www.gravatar.com/avatar/";
	$url .= md5( strtolower( trim( $user->usr_email ) ) );
	$s = 80;
	$d = 'mm';
	$r = 'g';
	$url .= "?s=$s&d=$d&r=$r";
 ?>
 
 

<div style="padding-bottom: 2%">
    <h1><?php echo _('Information'); ?></h1>    
</div>

<div style="width: 25%;">
    <div style="padding-bottom: 2%">
        <?php echo _('Login'); ?>:
        <?php if ($user) echo $user->usr_login; ?>
        <div style="float:right; padding-left: 5%px">
            <img src="<?php echo $url;?>">
        </div>
    </div>
    <div style="padding-bottom: 2%;">
        <?php echo _('Name'); ?>:
        <input type="text" size="26" name="usr_name" value="<?php if ($user) echo $user->usr_name; ?>"/>

    </div>
    <div>
        <?php echo _('E-mail'); ?>:
        <input type="text" size="26" name="usr_email" value="<?php if ($user) echo $user->usr_email; ?>"/>            
    </div>
</div>
 
