<?php

$flow = $argsToElement;

?>

<table class="flow">

    <tr>
        <th class="large" style="width:30%"></th>
        <th class="large">
            <?php echo _("Source"); ?>
        </th>
        <th class="large">
            <?php echo _("Destination"); ?>
        </th>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Domain"); ?>
        </th>
        <td>
            <label id="confirmation_src_domain"></label>
        </td>
        <td>
            <label id="confirmation_dst_domain"></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Network"); ?>
        </th>
        <td>
            <label id="confirmation_src_network"><?php echo $flow->source->network; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_network"><?php echo $flow->dest->network; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Device"); ?>
        </th>
        <td>
            <label id="confirmation_src_device"><?php echo $flow->source->device; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_device"><?php echo $flow->dest->device; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Port"); ?>
        </th>
        <td>
            <label id="confirmation_src_port"><?php echo $flow->source->port; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_port"><?php echo $flow->dest->port; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("VLAN"); ?>
        </th>
        <td>
            <label id="confirmation_src_vlan">Untagged</label>
        </td>
        <td>
            <label id="confirmation_dst_vlan">Untagged</label>
        </td>
    </tr>    
</table>