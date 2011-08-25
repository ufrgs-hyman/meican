<?php $devices = $this->passedArgs; ?>

<h1><?php echo _("Devices"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table class="list">
        
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th><?php echo _("Name"); ?></th>
            <th><?php echo _("IP address"); ?></th>
            <th><?php echo _("Trademark"); ?></th>
            <th><?php echo _("Model"); ?></th>
            <th><?php echo _("Number of ports"); ?></th>
            <th><?php echo _("Latitude"); ?></th>
            <th><?php echo _("Longitude"); ?></th>
            <th><?php echo _("Network"); ?></th>
            <th><?php echo _("Topology node ID"); ?></th>
            <th><?php echo _("#Endpoints"); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($devices as $d): ?>
        <tr>
            <td>
                <?php if ($d->deletable): ?>
                <input type="checkbox" name="del_checkbox[]" value="<?php echo $d->id; ?>"/>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($d->editable): ?>
                <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "dev_id:$d->id")); ?>">
                    <img class="edit" src="layouts/img/edit_1.png"/>
                </a>
                <?php endif; ?>
            </td>             
            <td>
                <?php echo $d->descr; ?>
            </td>
            <td>
                <?php echo $d->ip; ?>
            </td>
            <td>
                <?php echo $d->trademark; ?>
            </td>
            <td>
                <?php echo $d->model; ?>
            </td>
            <td>
                <?php echo $d->nr_ports; ?>
            </td>
            <td>
                <?php echo $d->latitude; ?>
            </td>
            <td>
                <?php echo $d->longitude; ?>
            </td>
            <td>
                <?php echo $d->network; ?>
            </td>
            <td>
                <?php echo $d->node_id; ?>
            </td>
            <td>
                <?php echo $d->nr_endpoints; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="11">
                <input class="add" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');">
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _('Delete'); ?>" onClick="return confirm('<?php echo _('The selected devices will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>