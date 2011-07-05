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
            <?php echo $flow->source->domain; ?>
        </td>
        <td>
            <?php echo $flow->dest->domain; ?>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Network"); ?>
        </th>
        <td>
            <?php echo $flow->source->network; ?>
        </td>
        <td>
            <?php echo $flow->dest->network; ?>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Device"); ?>
        </th>
        <td>
            <?php echo $flow->source->device; ?>
        </td>
        <td>
            <?php echo $flow->dest->device; ?>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Port"); ?>
        </th>
        <td>
            <?php echo $flow->source->port; ?>
        </td>
        <td>
            <?php echo $flow->dest->port; ?>
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