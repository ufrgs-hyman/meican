<?php

$flow = (isset($argsToElement->flow)) ? $argsToElement->flow : NULL;
$domains = $argsToElement->domains;

?>

<table>
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
            <select id="src_domain" onchange="changeDomain(this,'src');">
                <option value="-1"></option>
                <?php
                if ($flow) {
                    foreach ($domains as $d) {
                        if ($flow->source->dom_id == $d->id)
                            echo "<option selected='true' value='$d->id'>$d->name</option>";
                        else
                            echo "<option value='$d->id'>$d->name</option>";
                    }
                } else
                    foreach ($domains as $d)
                        echo "<option value='$d->id'>$d->name</option>";
                ?>
            </select>
            <img style="display:none" id="src_loading" src="<?php echo $this->url(''); ?>includes/images/ajax-loader.gif">
        </td>

        <td>
            <select id="dst_domain" onchange="changeDomain(this,'dst');">
                <option value="-1"></option>
                <?php
                if ($flow) {
                    foreach ($domains as $d) {
                        if ($flow->dest->dom_id == $d->id)
                            echo "<option selected='true' value='$d->id'>$d->name</option>";
                        else
                            echo "<option value='$d->id'>$d->name</option>";
                    }
                } else
                    foreach ($domains as $d)
                        echo "<option value='$d->id'>$d->name</option>";
                ?>
            </select>
            <img style="display:none" id="dst_loading" src="<?php echo $this->url(''); ?>includes/images/ajax-loader.gif">
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Network"); ?>
        </th>
        <td>
            <select style="display:none" id="src_network" onchange="changeNetwork(this,'src');"></select>
        </td>
        <td>
            <select style="display:none" id="dst_network" onchange="changeNetwork(this,'dst');"></select>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Device"); ?>
        </th>
        <td>
            <select id="src_device" style="display:none" onchange="changeDevice(this,'src');"></select>
        </td>
        <td>
            <select id="dst_device" style="display:none" onchange="changeDevice(this,'dst');"></select>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("Port"); ?>
        </th>
        <td>
            <select id="src_port" style="display:none" onchange="changePort(this,'src');"></select>
        </td>
        <td>
            <select id="dst_port" style="display:none" onchange="changePort(this,'dst');"></select>
        </td>
    </tr>
</table>