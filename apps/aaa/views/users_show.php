<?php $users = $this->passedArgs; ?>

<h1><?php echo _("Users"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">
    <?php echo $this->element('controls', array('app' => 'init')); ?>
    <table class="list">
        <!-- tr>
            <td colspan="3" align="right">
                <a href="modules/export/xls/users.php"> src="<?php echo $this->url(''); ?>img/excel.png" border="0" width="26px" height="26px" /></a>
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
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <?php if ($u->deletable): ?>
                            <input type="checkbox" name="del_checkbox[]" value="<?php echo $u->id; ?>" />
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($u->editable): ?>
                            <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "usr_id:$u->id")); ?>">
                                <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png"/>
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

    </table>

</form>