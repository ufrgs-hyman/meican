<?php $user = $this->passedArgs; ?>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'update_settings', 'param' => 'usr_id:' . $user->usr_id)); ?>">        
    <div id="settingsWrapper" style="width: 31%">
        <?php $this->addElement('identification_settings', $user); ?>

        <table id="userInfo" class="withoutBorder" style="margin-left: 19px; min-width: 0;">
            <tr>
                <th class="right">
                    <?php echo _("Language"); ?>:
                </th>
                <td class="left">
                    <select name="lang">
                        <option <?php if ($user->lang == "en_US.utf8") echo 'selected="true"'; ?> value="en_US.utf8"><?php echo _("English"); ?></option>
                        <option <?php if ($user->lang == "pt_BR.utf8") echo 'selected="true"'; ?> value="pt_BR.utf8"><?php echo _("Portuguese"); ?></option>
                    </select>
                </td>
            </tr>



            <tr style="display:none">
                <th class="right">
                    <?php echo _("Date Format"); ?>:
                </th>
                <td class="left">
                    <select name ="dateformat">
                        <option <?php if ($user->dateformat == "dd/mm/yyyy") echo 'selected="true"'; ?> value = "dd/mm/yyyy"><?php echo _("dd / mm / yyyy"); ?></option>
                        <option <?php if ($user->dateformat == "mm/dd/yyyy") echo 'selected="true"'; ?> value = "mm/dd/yyyy"><?php echo _("mm / dd / yyyy"); ?></option>
                        <option <?php if ($user->dateformat == "yyyy/mm/dd") echo 'selected="true"'; ?> value = "yyyy/mm/dd"><?php echo _("yyyy / mm / dd"); ?></option>
                    </select>
                </td>
            </tr>
        </table>

        <!-- input class="password" id="changePassword" type="button" value="<?php //echo _('Change password'); ?>" onclick="showPasswdBox();"/ -->
        <input type="checkbox" name="changePassword" id="changePassword"  onclick="showPasswdBox();"/>
        <?php echo _("Change password") ?>

        <div id="tpassword" style="display: none">
            <table class="withoutBorder" style="min-width: 0;">
                <tr>
                    <th class="right">
                        <?php echo _('Current password'); ?>
                    </th>
                    <td class="left">
                        <input type="password" size="20" name="old_usr_password" value=""/>
                    </td>

                </tr>
                <tr>
                    <th class="right">
                        <?php echo _('New password'); ?>
                    </th>
                    <td class="left">
                        <input  size="20" type="password" name="usr_password" value=""/>
                    </td>
                </tr>

                <tr>
                    <th class="right">
                        <?php echo _('Retype new password'); ?>
                    </th>
                    <td class="left">
                        <input size="20" type="password" name="retype_password" value=""/>
                    </td>
                </tr>
            </table>
            <br/>
        </div>    
        <div class="controls">
            <input type="submit" class="save" value="<?php echo _('Save'); ?>"/>
            <input type="button" class="cancel" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'edit_settings')); ?>');"/>
        </div>        

    </div>
</form>