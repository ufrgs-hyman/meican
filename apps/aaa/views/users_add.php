
<?php $args = $this->passedArgs; ?>

<h1><?php echo _('Add new user')?></h1>

<form onSubmit="selectAll('used');" method="POST" action="<?php echo $this->buildLink(array('action' => 'add')); ?>">

    <table class="withoutBorder add" style="min-width: 30%">
        <tr>
            <th class="right">
                <?php echo _('Login'); ?>:
            </th>
            <td class="left">
                <input type="text" size="20" name="usr_login" value="">
            </td>
        </tr>
         <tr>
                <th class="right">
                    <?php echo _('New password'); ?>:
                </th>
                <td class="left">
                    <input type="password" size="20" name="usr_password" value="">
                </td>
            </tr>

            <tr>
                <th class="right">
                    <?php echo _('Retype new password'); ?>:
                </th>
                <td class="left">
                    <input type="password" size="20" name="retype_password" value="">
                </td>
            </tr>
    </table> <br/><br/>
    
    <?php $this->addElement('identification_userAdd'); ?>

    <br/><br/>
    <table style="min-width: 0">
        <?php $this->addElement('associative_table', $args); ?>
            
        <div class="controls">
            <input class="save" type="submit" value="<?php echo _('Save'); ?>">
            <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
        </div>
    </table>

</form>
