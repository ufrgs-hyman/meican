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

<div class="form input">
    <label for="dom_descr"><?php echo _("Name"); ?></label>
    <input type="text" name="dom_descr" size="30" value="<?php echo $dom_descr; ?>"/>
</div>
<div class="form input">
    <label for="oscars_ip"><?php echo _("OSCARS IP"); ?></label>
    <input type="text" name="oscars_ip" size="30" id="oscars_ip" value="<?php echo $oscars_ip; ?>" onkeyup="buildIDC_URL()"/>
</div>
<div class="form input">
    <label for="oscars_protocol"><?php echo _("Protocol"); ?></label>
    <input type="radio" name="oscars_protocol" id="http" value="http" <?php if ($oscars_protocol == 'http') echo checked; ?> onchange="buildIDC_URL()"/> HTTP &nbsp;&nbsp;
    <input type="radio" name="oscars_protocol" id="https" value="https" <?php if ($oscars_protocol == 'https') echo checked; ?> onchange="buildIDC_URL()"/> HTTPS
</div>
<div class="form input">
    <label for="idc_url"><?php echo _("IDC URL"); ?></label>
    <label id="idc_url"><?php echo $idc_url; ?></label>
    <input type="text" name="idc_url" id="input_idcUrl" hidden="true" value="<?php echo $idc_url; ?>"/>
</div>
<div class="form input">
    <label for="dom_version"><?php echo _("OSCARS Version"); ?></label>
    <select name="dom_version" size="1" style="width:222px">
        <option <?php if ($dom_version == '0.5.3') echo 'selected="true"'; ?> value="0.5.3"> OSCARS 0.5.3 </option>
        <option <?php if ($dom_version == '0.5.4') echo 'selected="true"'; ?> value="0.5.4"> OSCARS 0.5.4 </option>
        <option <?php if ($dom_version == '0.6') echo 'selected="true"'; ?> value="0.6">   OSCARS 0.6   </option>
    </select>
</div>
<div class="form input">
    <label for="topology_id"><?php echo _("Topology ID"); ?></label>
    <input type="text" name="topology_id" size="30" value="<?php echo $topology_id; ?>"/>
</div>
<div class="form input">
    <label for="ode_ip"><?php echo _("ODE IP"); ?></label>
    <input type="text" name="ode_ip" size="30" value="<?php echo $ode_ip; ?>"/>
</div>
<div class="form input">
    <label for="ode_wsdl_path"><?php echo _("ODE WSDL path"); ?></label>
    <input type="text" name="ode_wsdl_path" size="30" value="<?php echo $ode_wsdl_path; ?>"/>
</div>
<div class="form input">
    <label for="ode_start"><?php echo _("ODE start function"); ?></label>
    <input type="text" name="ode_start" size="30" value="<?php echo $ode_start; ?>"/>
</div>
<div class="form input">
    <label for="ode_response"><?php echo _("ODE response function"); ?></label>
    <input type="text" name="ode_response" size="30" value="<?php echo $ode_response; ?>"/>
</div>