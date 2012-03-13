<?php

$flow = $argsToElement;

?>

<table class="flow" style="width: 100%">

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
            <label id="confirmation_src_domain"><?php if ($flow) echo $flow->source->domain; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_domain"><?php if ($flow) echo $flow->dest->domain; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Network"); ?>
        </th>
        <td>
            <label id="confirmation_src_network"><?php if ($flow) echo $flow->source->network; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_network"><?php if ($flow) echo $flow->dest->network; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Device"); ?>
        </th>
        <td>
            <label id="confirmation_src_device"><?php if ($flow) echo $flow->source->device; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_device"><?php if ($flow) echo $flow->dest->device; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Port"); ?>
        </th>
        <td>
            <label id="confirmation_src_port"><?php if ($flow) echo $flow->source->port; ?></label>
        </td>
        <td>
            <label id="confirmation_dst_port"><?php if ($flow) echo $flow->dest->port; ?></label>
        </td>
    </tr>

    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("VLAN"); ?>
        </th>
        <td>
            <label id="confirmation_src_vlan">
                <?php
                    debug($flow->source->vlan);
                    if ($flow) {
                        $vlan = ($flow->source->vlan == 0) ? "Untagged" : ($flow->source->vlan == NULL) ? "Tagged: "._("any") : "Tagged: ".$flow->source->vlan;
                        echo $vlan;
                    }
                ?>
            </label>
        </td>
        <td>
            <label id="confirmation_dst_vlan">
                <?php
                    if ($flow) {
                        $vlan = ($flow->dest->vlan == 0) ? "Untagged" : ($flow->dest->vlan == NULL) ? "Tagged: "._("any") : "Tagged: ".$flow->dest->vlan;
                        echo $vlan;
                    }
                ?>
            </label>
        </td>
    </tr>
</table>