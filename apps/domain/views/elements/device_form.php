<?php

$networks = isset($argsToElement->networks) ? $argsToElement->networks : NULL;
$device = isset($argsToElement->device) ? $argsToElement->device : NULL;

?>

<table>

    <tr>
        <th>
            <?php echo _("Name"); ?>:
        </th>
        <td>
            <input type="text" id="dev_descr" name="dev_descr" value="<?php if ($device) echo $device->dev_descr; ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("IP Address"); ?>:
        </th>
        <td>
            <input type="text" id="dev_ip" name="ip_addr" value="<?php if ($device) echo $device->dev_ip; ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Trademark"); ?>:
        </th>
        <td>
            <input type="text" name="trademark" value="<?php if ($device) echo $device->trademark; ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Model"); ?>:
        </th>
        <td>
            <input type="text" name="model" value="<?php if ($device) echo $device->model; ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Number of ports"); ?>:
        </th>
        <td>
            <input type="text" name="nr_ports" value="<?php if ($device) echo $device->nr_ports; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th>
            <?php echo _("Latitude"); ?>:
        </th>
        <td>
            <input type="text" name="dev_lat" value="<?php if ($device) echo $device->dev_lat; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th>
            <?php echo _("Longitude"); ?>:
        </th>
        <td>
            <input type="text" name="dev_lng" value="<?php if ($device) echo $device->dev_lng; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th>
            <?php echo _("Topology Node ID"); ?>:
        </th>
        <td>
            <input type="text" name="topo_node_id" value="<?php if ($device) echo $device->topo_node_id; ?>"/>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Select a network"); ?>:
        </th>
        <td>
            <select name="network" id="dev_network">
                <option value="-1"></option>
                <?php
                if ($device) {
                    foreach ($networks as $n) {
                        if ($device->net_id == $n->net_id)
                            echo "<option selected='true' value='$n->net_id'>$n->net_descr</option>";
                        else
                            echo "<option value='$n->net_id'>$n->net_descr</option>";
                    }
                } else
                    foreach ($networks as $n)
                        echo "<option value='$n->net_id'>$n->net_descr</option>";
                ?>
            </select>

            <a href="<?php echo $this->buildLink(array("controller" => "networks", "action" => "add_form")); ?>">
                <?php echo _("Add network"); ?>
            </a>
        </td>
    </tr>

</table>