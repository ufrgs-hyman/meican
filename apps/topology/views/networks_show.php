<?php

$networks = $this->passedArgs;

?>

<h1><?php echo _("Networks"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table class="list">
        
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th><?php echo _("Name"); ?></th>
            <th><?php echo _("Latitude"); ?></th>
            <th><?php echo _("Longitude"); ?></th>
            <th><?php echo _("Devices"); ?></th>
            <th><?php echo _("Domain"); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($networks as $n): ?>
        <tr>
            <td>
                <input type="checkbox" name="del_checkbox[]" value="<?php echo $n->id; ?>">
            </td>
            <td>
                <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "net_id:$n->id")); ?>">
                    <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png"/>
                </a>
            </td>                  
            <td>
                    <?php echo $n->descr; ?>
            </td>
            <td>
                <?php echo $n->latitude; ?>
            </td>
            <td>
                <?php echo $n->longitude; ?>
            </td>
            <td>
                <?php echo $n->devices; ?>
            </td>
            <td>
                <?php echo $n->parent_domain; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="7">
                <input class="add" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');">
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _('Delete'); ?>" onClick="return confirm('<?php echo _('The selected networks will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>