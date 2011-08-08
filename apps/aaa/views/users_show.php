<?php $users = $this->passedArgs; ?>

<h1><?php echo _("Users"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table class="list">
        <!-- tr>
            <td colspan="3" align="right">
                <a href="modules/export/xls/users.php"><img src="img/excel.png" border="0" width="26px" height="26px" /></a>
            </td>
        </tr -->

        <thead>
        <tr>
            <th></th>
            <th></th>
            <th><?php echo _('User'); ?></th>
            <th><?php echo _('Name'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($users as $u): ?>
        <tr>
            <td>
                    <?php if ($u->deletable): ?>
                    <input type="checkbox" name="del_checkbox[]" value="<?php echo $u->id; ?>" />
                    <?php endif; ?>
            </td>
            <td>
                <?php if ($u->editable): ?>
                <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "usr_id:$u->id")); ?>">
                    <img class="edit" src="layouts/img/edit_1.png"/>
                </a>
                <?php endif; ?>
            </td>
            <td>       
                <?php echo $u->login; ?>
            </td>

            <td>
                    <?php echo $u->name; ?>
            </td>
           
        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="3">
                <input type="button" class="add" name="addButton" class="add" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');">
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input type="submit" class="delete" value="<?php echo _('Delete'); ?>" onClick="return confirm('<?php echo _('The selected users will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>