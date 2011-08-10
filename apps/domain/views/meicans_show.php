<?php

$federations = $this->passedArgs;

?>

<h1><?php echo _("MEICANs"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table class="list">
        
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th><?php echo _("Name"); ?></th>
            <th><?php echo _("MEICAN IP"); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($federations as $f): ?>
        <tr>
            <td>
                <input type="checkbox" name="del_checkbox[]" value="<?php echo $f->id; ?>">
            </td>
            <td>
                <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "fed_id:$f->id")); ?>">
                    <img class="edit" src="layouts/img/edit_1.png"/>
                </a>                    
            </td>
            <td>
                    <?php echo $f->descr; ?>
            </td>
            <td>
                    <?php echo $f->ip; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="3">
                <input class="add" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');">
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _('Delete'); ?>" onClick="return confirm('<?php echo _('The selected federations will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>