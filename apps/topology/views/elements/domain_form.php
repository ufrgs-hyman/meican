<?php

$dom_descr = isset($argsToElement->dom_descr) ? $argsToElement->dom_descr : NULL;
$idc_url = isset($argsToElement->idc_url) ? $argsToElement->idc_url : NULL;
$oscars_ip = isset($argsToElement->oscars_ip) ? $argsToElement->oscars_ip : NULL;
$oscars_protocol = isset($argsToElement->oscars_protocol) ? $argsToElement->oscars_protocol : NULL;
$topology_id = isset($argsToElement->topology_id) ? $argsToElement->topology_id : NULL;

$ode_ip = isset($argsToElement->ode_ip) ? $argsToElement->ode_ip : NULL;
$ode_wsdl_path = isset($argsToElement->ode_wsdl_path) ? $argsToElement->ode_wsdl_path : NULL;
$ode_start = isset($argsToElement->ode_start) ? $argsToElement->ode_start : NULL;
$ode_response = isset($argsToElement->ode_response) ? $argsToElement->ode_response : NULL;

$dom_version = isset($argsToElement->dom_version) ? $argsToElement->dom_version : NULL;

?>

<table class="add">
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
            <input type="text" name="oscars_ip" id="oscars_ip" size="30" value="<?php echo $oscars_ip; ?>" onkeyup="buildIDC_URL()"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("Protocol");?>:
        </th>
        <td class="left">
            <input type="radio" name="oscars_protocol" id="http" value="http" <?php if ($oscars_protocol == 'http') echo checked; ?> onchange="buildIDC_URL()"/> HTTP &nbsp;&nbsp;
            <input type="radio" name="oscars_protocol" id="https" value="https" <?php if ($oscars_protocol == 'https') echo checked; ?> onchange="buildIDC_URL()"/> HTTPS
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("IDC URL"); ?>:
        </th>
        <td class="left">
            <label id="idc_url"><?php echo $idc_url; ?></label>
            <input type="text" name="idc_url" id="input_idcUrl" hidden="true" value="<?php echo $idc_url; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("OSCARS Version"); ?>:
        </th>
        <td class="left">
            <select name="dom_version" size="1" style="width:222px">
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
    <tr>
        <th class="right">
            <?php echo _("ODE start function"); ?>:
        </th>
        <td class="left">
            <input type="text" name="ode_start" size="30" value="<?php echo $ode_start; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("ODE response function"); ?>:
        </th>
        <td class="left">
            <input type="text" name="ode_response" size="30" value="<?php echo $ode_response; ?>"/>
        </td>
    </tr>
    
</table>