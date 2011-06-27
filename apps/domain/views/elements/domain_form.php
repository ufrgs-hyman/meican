<?php

$dom_descr = isset($argsToElement->dom_descr) ? $argsToElement->dom_descr : NULL;
$oscars_ip = isset($argsToElement->oscars_ip) ? $argsToElement->oscars_ip : NULL;
$topo_ip = isset($argsToElement->topo_ip) ? $argsToElement->topo_ip : NULL;

?>

<table>
    <tr>
        <th>
            <?php echo _("Name"); ?>:
        </th>
        <td>
            <input type="text" name="dom_descr" size="50" value="<?php echo $dom_descr; ?>"/>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo _("OSCARS IP"); ?>:
        </th>
        <td>
            <input type="text" name="oscars_ip" size="50" value="<?php echo $oscars_ip; ?>"/>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo _("Topology Service IP"); ?>:
        </th>
        <td>
            <input type="text" name="topo_ip" size="50" value="<?php echo $topo_ip; ?>"/>
        </td>
    </tr>
</table>