
<?php $args = $this->passedArgs; ?>

<h1><?php echo _('Add new user')?></h1>

<form onSubmit="selectAll('used');" method="POST" action="<?php echo $this->buildLink(array('action' => 'add')); ?>">

    <table>
        <tr>
            <th>
                <?php echo _('Login'); ?>:
            </th>
            <td>
                <input type="text" size="50" name="usr_login" value="">
            </td>
        </tr>
         <tr>
                <th>
                    <?php echo _('New password'); ?>:
                </th>
                <td>
                    <input type="password" size="50" name="usr_password" value="">
                </td>
            </tr>

            <tr>
                <th>
                    <?php echo _('Retype new password'); ?>:
                </th>
                <td>
                    <input type="password" size="50" name="retype_password" value="">
                </td>
            </tr>
    </table>

    <?php $this->addElement('identification'); ?>

    <?php $this->addElement('associative_table', $args); ?>
            
    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Save'); ?>">
        <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
    </div>

</form>
