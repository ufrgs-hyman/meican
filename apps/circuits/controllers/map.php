<?php
defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'apps/circuits/models/flow_info.php';
include_once 'apps/topology/models/domain_info.php';
include_once 'apps/circuits/controllers/reservations.php';
include_once 'apps/topology/models/topology.php';
include_once 'includes/nuSOAP/lib/nusoap.php';

class map extends Controller {

    public function map() {
        $this->app = 'circuits';
        $this->controller = 'map';
        $this->defaultAction = 'show';
    }

    public function show() {
        $min = 100;
        $max = 1000;
        $div = 100;

        $this->setAction('show');
        $this->setInLineScript('map_init');

        $domain = new domain_info();
        $domains = $domain->fetch(FALSE);

        $domToMapArray = array();
        foreach ($domains as $d) {
            $domain = new stdClass();
            $domain->id = $d->dom_id;
            $domain->name = $d->dom_descr;
            $endpoint = "http://{$d->dom_ip}/" . Framework::$systemDirName . "topology/ws";
            if ($ws = new nusoap_client($endpoint, array('cache_wsdl' => 0))) {
                if ($temp = $ws->call('getURNDetails', array())) {
                    //Framework::debug("$d->dom_descr networks",$temp);
                    $domain->networks = $temp;
                    $domToMapArray[] = $domain;
                }
            }
        }

        if ($domToMapArray) {
            //Framework::debug("RESULTADO DO WEBSERVICE", $domToMapArray);
            $this->setArgsToScript(array(
                    "band_min" => $min,
                    "band_max" => $max,
                    "band_div" => $div,
                    "flash_nameReq" => _("A name is required"),
                    "flash_bandInv" => _("Invalid value for bandwidth"),
                    "flash_sourceReq" => _("A source is required"),
                    "flash_srcVlanInv" => _("Invalid value for source VLAN"),
                    "flash_srcVlanReq" => _("Source VLAN type required"),
                    "flash_destReq" => _("A destination is required"),
                    "flash_dstVlanInv" => _("Invalid value for destination VLAN"),
                    "flash_dstVlanReq" => _("Destination VLAN type required"),
                    "domain_string" => _("Domain"),
                    "domains_string" => _("Domains"),
                    "network_string" => _("Network"),
                    "networks_string" => _("Networks"),
                    "device_string" => _("Device"),
                    "devices_string" => _("Devices"),
                    "from_here_string" => _("From Here"),
                    "to_here_string" => _("To Here"),
                    "cluster_information_string" => _("Information about cluster"),
                    "domains" => $domToMapArray
            ));
            $argsToBody = new stdClass();
            $argsToBody->bandwidthTip = "(" . $min . ", " . ($min + $div) . ", " . ($min + 2 * $div) . ", " . ($min + 3 * $div) . ", ... , " . $max . ")";
            $argsToBody->res_wizard = (Common::hasSessionVariable('res_wizard')) ? TRUE : FALSE;
            $this->setArgsToBody($argsToBody);
        }
        else
            $this->action = 'empty';

        $this->render();
    }
}
?>
