<?php $args = $this->passedArgs; ?>

<h1><?php echo _('Edit user')?></h1>

<form onSubmit="selectAll('used');" method="POST" action="<?php echo $this->buildLink(array('action' => 'update', 'param' => 'usr_id:'.$args->user->usr_id)); ?>">

    <?php $this->addElement('identification_settings', $args->user); ?>
    
    <input id="changePassword" type="button" value="<?php echo _('Change password'); ?>" onclick="$('#tpassword').slideToggle();"/>
    
    <div id="tpassword" style="display: none">
        <table class="withoutBorder" style="min-width: 0">
            <tr>
                <th class="right">
                    <?php echo _('New password'); ?>
                </th>
                <td class="left">
                    <input type="password" size="20" name="usr_password" value=""/>
                </td>
            </tr>

            <tr>
                <th class="right">
                    <?php echo _('Retype new password'); ?>
                </th>
                <td class="left">
                    <input type="password" size="20" name="retype_password" value=""/>
                </td>
            </tr>
        </table>
    </div>    

    <div style="width:78%">
    <?php $this->addElement('associative_table', $args); ?>

        <div class="controls">
            <input class="save" type="submit" value="<?php echo _('Save'); ?>"/>
            <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
        </div>
    </div>

</form>
