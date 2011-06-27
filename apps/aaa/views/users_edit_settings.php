<?php $user = $this->passedArgs; ?>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'update_settings', 'param' => 'usr_id:' . $user->usr_id)); ?>">
    <table>

        <tr>
            <th>
                <?php echo _('Login'); ?>
            </th>
            <td>
                <?php echo $user->usr_login; ?>
            </td>
        </tr>
        <tr>
            <td>
                <input class="password" id="changePassword" type="button" value="<?php echo _('Change password'); ?>" onclick="showPasswdBox();">
            </td>
        </tr>
    </table>

    <div id="tpassword" style="display: none">
        <table>
            <tr>
                <th>
                    <?php echo _('Current password'); ?>
                </th>
                <td>
                    <input type="password" size="50" name="old_usr_password" value="">
                </td>

            </tr>
            <tr>
                <th>
                    <?php echo _('New password'); ?>
                </th>
                <td>
                    <input  size="50" type="password" name="usr_password" value="">
                </td>
            </tr>

            <tr>
                <th>
                    <?php echo _('Retype new password'); ?>
                </th>
                <td>
                    <input size="50" type="password" name="retype_password" value="">
                </td>
            </tr>
        </table>
    </div>

    <?php $this->addElement('identification', $user); ?>

    <table>
        <tr>
            <th>
                <?php echo _("Language"); ?>
            </th>
            <td>
                <select name="lang">
                    <option <?php if ($user->lang == "en_US.utf8") echo 'selected="true"'; ?> value="en_US.utf8"><?php echo _("English"); ?></option>
                    <option <?php if ($user->lang == "pt_BR.utf8") echo 'selected="true"'; ?> value="pt_BR.utf8"><?php echo _("Portuguese"); ?></option>
                </select>
            </td>
        </tr>
    

    
        <tr>
            <th>
                <?php echo _("Date Format"); ?>
            </th>
            <td>
                <select name ="dateformat">
                    <option <?php if ($user->dateformat == "dd/mm/yyyy") echo 'selected="true"'; ?> value = "dd/mm/yyyy"><?php echo _("dd / mm / yyyy"); ?></option>
                    <option <?php if ($user->dateformat == "mm/dd/yyyy") echo 'selected="true"'; ?> value = "mm/dd/yyyy"><?php echo _("mm / dd / yyyy"); ?></option>
                    <option <?php if ($user->dateformat == "yyyy/mm/dd") echo 'selected="true"'; ?> value = "yyyy/mm/dd"><?php echo _("yyyy / mm / dd"); ?></option>
                </select>
            </td>
        </tr>
    </table>

    <div class="controls">
        <input type="submit" class="save" value="<?php echo _('Save'); ?>">
        <input type="button" class="cancel" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'edit_settings')); ?>');">
    </div>

</form>