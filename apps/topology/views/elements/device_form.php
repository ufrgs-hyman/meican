<?php

$domains = isset($argsToElement->domains) ? $argsToElement->domains : NULL;
$device = isset($argsToElement->device) ? $argsToElement->device : NULL;

?>

<table class="withoutBorder add">

    <tr>
        <th class="right">
            <?php echo _("Name"); ?>:
        </th>
        <td class="left">
            <input type="text" id="dev_descr" name="dev_descr" value="<?php if ($device) echo $device->dev_descr; ?>"/>
        </td>
    </tr>

    <tr>
        <th class="right">
            <?php echo _("IP address"); ?>:
        </th>
        <td class="left">
            <input type="text" id="dev_ip" name="ip_addr" value="<?php if ($device) echo $device->dev_ip; ?>"/>
        </td>
    </tr>

    <tr>
        <th class="right">
            <?php echo _("Trademark"); ?>:
        </th>
        <td class="left">
            <input type="text" name="trademark" value="<?php if ($device) echo $device->trademark; ?>"/>
        </td>
    </tr>

    <tr>
        <th class="right">
            <?php echo _("Model"); ?>:
        </th>
        <td class="left">
            <input type="text" name="model" value="<?php if ($device) echo $device->model; ?>"/>
        </td>
    </tr>

    <tr>
        <th class="right">
            <?php echo _("Number of ports"); ?>:
        </th>
        <td class="left">
            <input type="text" name="nr_ports" value="<?php if ($device) echo $device->nr_ports; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th class="right">
            <?php echo _("Topology node ID"); ?>:
        </th>
        <td class="left">
            <input type="text" name="node_id" value="<?php if ($device) echo $device->node_id; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th class="right">
            <?php echo _("Select a domain"); ?>:
        </th>
        <td class="left">
            <select id="dev_domain" onchange="dev_changeDomain(this)" <?php if ($device) echo "disabled"; ?> >
                <option value="-1"/>
                <?php
                if ($device) {
                    $net_found = FALSE;
                    foreach ($domains as $d) {
                        // se não encontrou a rede do dispositivo, procura por ela
                        if (!$net_found) {
                            foreach ($d->networks as $n) {
                                if ($n->id == $device->net_id) {
                                    $dom_id = $d->id;
                                    $net_id = $n->id;
                                    $networks = $d->networks;
                                    $net_found = TRUE;
                                    break;
                                }
                            }
                        }
                            
                        if ($net_found && ($dom_id == $d->id)): ?>
                            <option selected="true" value="<?php echo $d->id; ?>"><?php echo $d->descr; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $d->id; ?>"><?php echo $d->descr; ?></option>
                        <?php endif;
                    }
                } else
                    foreach ($domains as $d)
                        echo "<option value='$d->id'>$d->descr</option>";
                ?>
            </select>

            <a href="<?php echo $this->buildLink(array("controller" => "domains", "action" => "add_form")); ?>">
                <?php echo _("Add domain"); ?>
            </a>
        </td>
    </tr>

    <tr>
        <th class="right">
            <?php echo _("Select a network"); ?>:
        </th>
        <td class="left">
            <?php if ($device && $net_found): ?>
                <select name="network" id="dev_network" <?php if ($device) echo "disabled"; ?> >
                    <option value="-1"/>
                    <?php foreach ($networks as $n): ?>
                        <?php if ($n->id == $net_id): ?>
                            <option selected="true" value="<?php echo $n->id; ?>"><?php echo $n->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $n->id; ?>"><?php echo $n->name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <select name="network" id="dev_network" style="display: none"/>
            <?php endif; ?>

            <a href="<?php echo $this->buildLink(array("controller" => "networks", "action" => "add_form")); ?>">
                <?php echo _("Add network"); ?>
            </a>
        </td>
    </tr>

</table>