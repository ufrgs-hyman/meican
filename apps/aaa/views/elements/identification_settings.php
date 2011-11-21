<?php $user = $argsToElement; ?>
<?php
	$url = "http://www.gravatar.com/avatar/";
	$url .= md5( strtolower( trim( $user->usr_email ) ) );
	$s = 80;
	$d = 'mm';
	$r = 'g';
	$url .= "?s=$s&d=$d&r=$r";
 ?>
 
 

<h1><?php echo _('Information'); ?></h1>
<img src="<?php echo $url;?>" style="float:right;">

<table class="withoutBorder add" style="margin-left: 40px">
    <tr>
        <th class="right" style="height: 30px">
            <?php echo _('Login'); ?>:
        </th>
        <td class="left" style="height: 30px">
            <?php if ($user) echo $user->usr_login; ?>
        </td>
    </tr>    
    <tr>
        <th class="right">
            <?php echo _('Name'); ?>:
        </th>
        
        <td class="left">
            <input type="text" size="26" name="usr_name" value="<?php if ($user) echo $user->usr_name; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _('E-mail'); ?>:
        </th>
        <td class="left">
            <input type="text" size="26" name="usr_email" value="<?php if ($user) echo $user->usr_email; ?>"/>
        </td>
    </tr>
</table>
