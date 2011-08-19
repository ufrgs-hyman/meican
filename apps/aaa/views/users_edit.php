<?php $args = $this->passedArgs; ?>

<h1><?php echo _('Edit user')?></h1>

<form onSubmit="selectAll('used');" method="POST" action="<?php echo $this->buildLink(array('action' => 'update', 'param' => 'usr_id:'.$args->user->usr_id)); ?>">

    <table>
        <tr>
            <th>
                <?php echo _('User login'); ?>:
            </th>
            <td>
                <?php echo $args->user->usr_login; ?>
            </td>
        </tr>
        <tr>
            <td>
                <input id="changePassword" type="button" value="<?php echo _('Change password'); ?>" onclick="showPasswdBox();">
            </td>
        </tr>
    </table>

    <div id="tpassword" style="display: none">
        <table>
            <tr>
                <td>
                    <?php echo _('New password'); ?>
                </td>
                <td>
                    <input type="password" size="50" name="usr_password" value="">
                </td>
            </tr>

            <tr>
                <td>
                    <?php echo _('Retype new password'); ?>
                </td>
                <td>
                    <input type="password" size="50" name="retype_password" value="">
                </td>
            </tr>
        </table>
    </div>

    <?php $this->addElement('identification', $args->user); ?>

    <?php $this->addElement('associative_table', $args); ?>

    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Save'); ?>">
        <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
    </div>

</form>