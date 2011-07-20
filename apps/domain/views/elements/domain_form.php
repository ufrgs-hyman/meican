<?php

$dom_descr = isset($argsToElement->dom_descr) ? $argsToElement->dom_descr : NULL;
$oscars_ip = isset($argsToElement->oscars_ip) ? $argsToElement->oscars_ip : NULL;
$topo_domain_id = isset($argsToElement->topo_domain_id) ? $argsToElement->topo_domain_id : NULL;

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
            <?php echo _("Topology Domain ID"); ?>:
        </th>
        <td>
            <input type="text" name="topo_domain_id" size="50" value="<?php echo $topo_domain_id; ?>"/>
        </td>
    </tr>
</table>