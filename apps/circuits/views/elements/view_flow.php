<?php

$flow = $argsToElement;

?>

<table class="flow">

    <tr>
        <td></td>
        <th>
            <?php echo _("Source"); ?>
        </th>
        <th>
            <?php echo _("Destination"); ?>
        </th>
    </tr>

    <tr>
        <th>
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
        <th>
            <?php echo _("Network"); ?>
        </th>
        <td>
            <label id="confirmation_src_network"></label>
        </td>
        <td>
            <label id="confirmation_dst_network"></label>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Device"); ?>
        </th>
        <td>
            <label id="confirmation_src_device"></label>
        </td>
        <td>
            <label id="confirmation_dst_device"></label>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Port"); ?>
        </th>
        <td>
            <label id="confirmation_src_port"></label>
        </td>
        <td>
            <label id="confirmation_dst_port"></label>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("VLAN"); ?>
        </th>
        <td>
            <?php
            if ($flow->source->vlan)
                echo _("Tagged")."<br>".$flow->source->vlan;
            else
                echo _("Untagged");
            ?>
        </td>
        <td>
            <?php
            if ($flow->dest->vlan)
                echo _("Tagged")."<br>".$flow->dest->vlan;
            else
                echo _("Untagged");
            ?>
        </td>
    </tr>
</table>