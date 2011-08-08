<?php $groups = $this->passedArgs; ?>

<h1><?php echo _("User Groups"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table class="list">

        <thead>
        <tr>
            <th></th>
            <th></th>
            <th><?php echo _("Name"); ?></th>
            <th><?php echo _("Parent Groups"); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($groups as $g): ?>
        <tr>
            <td>
                <?php if ($g->editable): ?>
                <input type="checkbox" name="del_checkbox[]" value="<?php echo $g->id; ?>" >
                <?php endif; ?>
            </td>
            <td>
                <?php if ($g->editable): ?>
                    <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "grp_id:$g->id")); ?>">
                        <img class="edit" src="layouts/img/edit_1.png"/>
                    </a>
                <?php endif; ?>
            </td>
            <td>
                    <?php echo $g->descr; ?>
            </td>
            
            <td>
                <?php echo $g->parents; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="3">
                <input class="add" type="button" name="addButton" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');" />
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected groups will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')" />
    </div>
    
</form>