<?php

$domain = $argsToElement;

?>

<table id="urn_table<?php echo $domain->id; ?>" class="list">
    
    <thead>
        <tr>
            <th rowspan="2" colspan="3"></th>
            <th rowspan="2"><?php echo _("Network"); ?></th>
            <th rowspan="2"><?php echo _("Device"); ?></th>
            <th rowspan="2"><?php echo _("Port"); ?></th>
            <th rowspan="2" style="border-right: 1px solid black"><?php echo _("URN value"); ?></th>
            <th colspan="4"><?php echo _("Link settings"); ?></th>
        </tr>
            
        <tr>
            <th><?php echo _("VLAN values"); ?></th>
            <th><?php echo _("Maximum capacity (bps)"); ?></th>
            <th><?php echo _("Minimum capacity (bps)"); ?></th>
            <th><?php echo _("Granularity (bps)"); ?></th>
        </tr>
    </thead>
        
    <tbody>
        <?php foreach ($domain->urns as $u): ?>
            <tr id="line<?php echo $u->urn_id; ?>">
                <td>
                    <input type="checkbox" name="del_checkbox[]" value="<?php echo $u->urn_id; ?>">
                </td>
                <td class="edit">
                    <img class="edit" src="layouts/img/edit_1.png" onclick="editURN('<?php echo $domain->id; ?>', '<?php echo $u->urn_id; ?>');">
                </td>
                <td class="edit">
                    <img class="delete" src="layouts/img/remove.png" onclick="deleteURN('<?php echo $u->urn_id; ?>');">
                </td>
                    
                <td id="network_box<?php echo $u->urn_id; ?>" title="<?php echo $u->net_id; ?>"><?php echo $u->network; ?></td>
                <td id="device_box<?php echo $u->urn_id; ?>" title="<?php echo $u->dev_id; ?>"><?php echo $u->device; ?></td>
                <td><?php echo $u->port; ?></td>
                <td><?php echo $u->urn_string; ?></td>
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
                <img class="loading" style="display:none" id="loading<?php echo $domain->id; ?>" src="includes/images/ajax-loader.gif" />
                <input class="add" type="button" id="new_button" value="<?php echo _("Add"); ?>" onclick="newURN('<?php echo $domain->id; ?>');" />
            </td>
        </tr>
    </tfoot>
        
</table>