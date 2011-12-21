<?php
$domain = $argsToElement;
?>

<table id="urn_table<?php echo $domain->id; ?>" class="list" style="width:100%">

    <thead>
<?php $this->addElement('urn_header'); ?>
    </thead>

    <tbody>
<?php foreach ($domain->urns as $u): ?>
            <tr id="line<?php echo $u->urn_id; ?>">
                <td>
                    <input type="checkbox" name="del_checkbox[]" value="<?php echo $u->urn_id; ?>">
                </td>
                <td class="edit">
                    <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png" onclick="editURN('<?php echo $domain->id; ?>', '<?php echo $u->urn_id; ?>');">
                </td>
                <td class="edit">
                    <img class="delete" src="<?php echo $this->url(''); ?>webroot/img/remove.png" onclick="deleteURN('<?php echo $u->urn_id; ?>');">
                </td>

                <td id="network_box<?php echo $u->urn_id; ?>" title="<?php echo $u->net_id; ?>"><?php echo $u->network; ?></td>
                <td id="device_box<?php echo $u->urn_id; ?>" title="<?php echo $u->dev_id; ?>"><?php echo $u->device; ?></td>
                <td><?php echo $u->port; ?></td>
                <td class="left"><?php echo $u->urn_string; ?></td>
                <td><?php echo $u->vlan; ?></td>
                <td><?php echo $u->max_capacity; ?></td>
                <td><?php echo $u->min_capacity; ?></td>
                <td><?php echo $u->granularity; ?></td>
            </tr>
<?php endforeach; ?>
    </tbody>

</table>
<div class="controls">
    <img class="loading" style="display:none" id="loading<?php echo $domain->id; ?>" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif" />
    <input class="add" type="button" id="add_button<?php echo $domain->id; ?>" value="<?php echo _("Add from topology"); ?>" onclick="newURN('<?php echo $domain->id; ?>');" />
    <input class="add" type="button" id="add_man_button<?php echo $domain->id; ?>" value="<?php echo _("Add manual"); ?>" onclick="newURNLine('<?php echo $domain->id; ?>');" />
</div>