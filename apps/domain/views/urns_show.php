<?php

$urns = $this->passedArgs;

?>

<h1><?php echo _("URNs (Uniform Resource Name)"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table id="urn_table" class="list">
        
        <thead>
        <tr>
            <th rowspan="2" colspan="3"></th>
            <th rowspan="2"><?php echo _("Network"); ?></th>
            <th rowspan="2"><?php echo _("Device"); ?></th>
            <th rowspan="2"><?php echo _("Port"); ?></th>
            <th rowspan="2"><?php echo _("URN Value"); ?></th>
            <th colspan="4"><?php echo _("Link Settings"); ?></th>
        </tr>
        
        <tr>
            <th><?php echo _("VLAN Values"); ?></th>
            <th><?php echo _("Maximum Capacity"); ?></th>
            <th><?php echo _("Minimum Capacity"); ?></th>
            <th><?php echo _("Granularity"); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($urns as $u): ?>
        <tr id="line<?php echo $u->id; ?>">
            <td>
                <input type="checkbox" name="del_checkbox[]" value="<?php echo $u->id; ?>">
            </td>
            <td class="edit">
                <img class="edit" src="layouts/img/edit_1.png" onclick="editURN('<?php echo $u->id; ?>');">
            </td>
            <td class="edit">
                <img class="delete" src="layouts/img/remove.png" onclick="deleteURN('<?php echo $u->id; ?>');">
            </td>

            <td id="network_box<?php echo $u->id; ?>" title="<?php echo $u->net_id; ?>"><?php echo $u->network; ?></td>
            <td id="device_box<?php echo $u->id; ?>" title="<?php echo $u->dev_id; ?>"><?php echo $u->device; ?></td>
            <td><?php echo $u->port; ?></td>
            <td><?php echo $u->string; ?></td>
            <td><?php echo $u->vlan; ?></td>
            <td><?php echo $u->max_capacity; ?></td>
            <td><?php echo $u->min_capacity; ?></td>
            <td><?php echo $u->granularity; ?></td>

        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="11">
                <img class="loading" style="display:none" id="loading" src="includes/images/ajax-loader.gif" />
                <input class="add" type="button" id="new_button" value="<?php echo _("Add"); ?>" onclick="newURN();" />
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="save" id="save_button" style="display:none" type="button"  value="<?php echo _("Save"); ?>" onclick="saveURN();">
        <input class="cancel" id="cancel_button" style="display:none" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">

        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected URNs will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>
    
</form>