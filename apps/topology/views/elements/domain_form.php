<?php

$dom_descr = isset($argsToElement->dom_descr) ? $argsToElement->dom_descr : NULL;
$oscars_ip = isset($argsToElement->oscars_ip) ? $argsToElement->oscars_ip : NULL;
$topology_id = isset($argsToElement->topology_id) ? $argsToElement->topology_id : NULL;
$ode_ip = isset($argsToElement->ode_ip) ? $argsToElement->ode_ip : NULL;
$ode_wsdl_path = isset($argsToElement->ode_wsdl_path) ? $argsToElement->ode_wsdl_path : NULL;
$dom_version = isset($argsToElement->dom_version) ? $argsToElement->dom_version : NULL;

?>

<table class="withoutBorder add">
    <tr>
        <th class="right">
            <?php echo _("Name"); ?>:
        </th>
        <td class="left">
            <input type="text" name="dom_descr" size="30" value="<?php echo $dom_descr; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("OSCARS IP"); ?>:
        </th>
        <td class="left">
            <input type="text" name="oscars_ip" size="30" value="<?php echo $oscars_ip; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("OSCARS Version"); ?>:
        </th>
        <td class="left">
            <select name="dom_version" size="1">
                <option <?php if ($dom_version == '0.5.3') echo 'selected="true"'; ?> value="0.5.3"> OSCARS 0.5.3 </option>
                <option <?php if ($dom_version == '0.5.4') echo 'selected="true"'; ?> value="0.5.4"> OSCARS 0.5.4 </option>
                <option <?php if ($dom_version == '0.6') echo 'selected="true"'; ?> value="0.6">   OSCARS 0.6   </option>
            </select>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("Topology ID"); ?>:
        </th>
        <td class="left">
            <input type="text" name="topology_id" size="30" value="<?php echo $topology_id; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("ODE IP"); ?>:
        </th>
        <td class="left">
            <input type="text" name="ode_ip" size="30" value="<?php echo $ode_ip; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("ODE WSDL path"); ?>:
        </th>
        <td class="left">
            <input type="text" name="ode_wsdl_path" size="30" value="<?php echo $ode_wsdl_path; ?>"/>
        </td>
    </tr>
</table>