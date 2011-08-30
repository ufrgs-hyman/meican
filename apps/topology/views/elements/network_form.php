<?php

$network = isset($argsToElement->network) ? $argsToElement->network : NULL;
$domains = isset($argsToElement->domains) ? $argsToElement->domains : NULL;

?>

<table class="withoutBorder add">
    <tr>
        <th class="right">
            <?php echo _("Name"); ?>:
        </th>
        <td class="left">
            <input type="text" size="50" id="net_descr" name="net_descr" value="<?php if ($network) echo $network->net_descr; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th class="right">
            <?php echo _("Latitude"); ?>:
        </th>
        <td class="left">
            <input type="text" size="50" id="net_lat" name="net_lat" value="<?php if ($network) echo $network->net_lat; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th class="right">
            <?php echo _("Longitude"); ?>:
        </th>
        <td class="left">
            <input type="text" size="50" id="net_lng" name="net_lng" value="<?php if ($network) echo $network->net_lng; ?>"/>
        </td>
    </tr>
    
    <tr>
        <th class="right">
            <?php echo _("Select a domain"); ?>:
        </th>
        <td class="left">
            <select id="domain_select" name="domain">
                <option value="-1"></option>
                <?php
                if ($network) {
                    foreach ($domains as $d) {
                        if ($network->parent_domain == $d->dom_id)
                            echo "<option selected='true' value='$d->dom_id'>$d->dom_descr</option>";
                        else
                            echo "<option value='$d->dom_id'>$d->dom_descr</option>";
                    }
                } else
                    foreach ($domains as $d)
                        echo "<option value='$d->dom_id'>$d->dom_descr</option>";
                ?>
            </select>
        </td>
    </tr>
    
</table>