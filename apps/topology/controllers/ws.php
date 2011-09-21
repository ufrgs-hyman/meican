<?php
include_once 'libs/controller.php';
require_once 'includes/nuSOAP/lib/nusoap.php';
include_once 'apps/topology/models/topology.inc';
include_once 'apps/topology/models/meican_info.inc';

class ws extends Controller {

    public function ws() {
        $this->app = 'topology';
        $this->controller = 'ws';
        $this->defaultAction = '';

        $this_meican = new meican_info();
        
        $this_ip = $this_meican->getLocalMeicanIp();
        $this_dir_name = $this_meican->getLocalMeicanDirName();
        
        $namespace = "http://MEICAN";
        $server = new nusoap_server();
        $server->configureWSDL("MEICAN_TOPOLOGY_SERVICES", $namespace, "http://$this_ip/$this_dir_name$this->app/ws/");
        $server->wsdl->schemaTargetNamespace = $namespace;

        $server->wsdl->addComplexType('urnType','complexType','struct','all','',
                array(
                'net_descr' => array('name' => 'net_descr', 'type' => 'xsd:string'),
                'dev_descr' => array('name' => 'dev_descr', 'type' => 'xsd:string'),
                'port_number' => array('name' => 'port_number', 'type' => 'xsd:int')));

        $server->wsdl->addComplexType('stringTypeList','complexType','array','','SOAP-ENC:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'xsd:string[]'),
                'xsd:string'));

        $server->wsdl->addComplexType('urnTypeList','complexType','array','','SOAP-ENC:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'tns:urnType[]'),
                'tns:urnType'));

        $server->wsdl->addComplexType('portType','complexType','struct','all','',
                array('port_number'  => array('name' => 'port_number','type' => 'xsd:int'),
                'vlan'         => array('name' => 'vlan','type' => 'xsd:string'),
                'max_capacity' => array('name' => 'max_capacity','type' => 'xsd:int'),
                'min_capacity' => array('name' => 'min_capacity','type' => 'xsd:int'),
                'granularity'  => array('name' => 'granularity','type' => 'xsd:int'),
        ));

        $server->wsdl->addComplexType('portTypeList','complexType','array','all','',
                array('ports' => array('name' => 'ports','type' => 'tns:portType')));

        $server->wsdl->addComplexType('deviceType','complexType','struct','all','',
                array('dev_id'  => array('name' => 'dev_id','type' => 'xsd:int'),
                'dev_descr' => array('name' => 'dev_descr','type' => 'xsd:string'),
                'latitude' => array('name'=>'latitude','type'=>'xsd:float'),
                'longitude' => array('name'=>'longitude','type'=>'xsd:float'),
                'ports' => array('name' => 'ports','type' => 'tns:portTypeList')
        ));

        $server->wsdl->addComplexType('deviceTypeList','complexType','array','all','',
                array('devices' => array('name' => 'devices','type' => 'tns:deviceType')));

        $server->wsdl->addComplexType('netType','complexType','struct','all','',
                array('net_id'  => array('name' => 'net_id','type' => 'xsd:int'),
                'net_descr' => array('name' => 'net_descr','type' => 'xsd:string'),
                'latitude' => array('name' => 'latitude','type' => 'xsd:float'),
                'longitude' => array('name' => 'longitude','type' => 'xsd:float'),
                'devices' => array('name' => 'devices','type' => 'tns:deviceTypeList')
        ));

        $server->wsdl->addComplexType('netTypeList','complexType','array','all','',
                array('nets' => array('name' => 'nets','type' => 'tns:netType')));

        $server->register(
                'getURNsInfo',
                array('urn_string_list'=>'tns:stringTypeList'),
                array('urn_info_array'=>'tns:urnTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getURNsInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getURNDetails',
                array('urn_string'=>'xsd:string'),
                array('urn_details'=>'tns:urnTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getURNDetails",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        function getURNsInfo($urn_string_list) {
            //Framework::debug('geturnsinfo',$urn_string_list);

            if (isset($urn_string_list) && is_array($urn_string_list)) {
                $ind = 0;
                unset($urn_info_list);

                foreach($urn_string_list as $urn_string) {

                    if (is_string($urn_string)) {
                        $urn = new urn_info();
                        $urn->urn_string = $urn_string;

                        $result = $urn->fetch(FALSE);

                        if (!$result) {
                            //posição inválida
                            $urn_info_list[$ind] = NULL;
                            $ind++;
                            continue;
                        } else {

                            $net = new network_info();
                            $net->net_id = $result[0]->net_id;
                            $r_net = $net->fetch(FALSE);

                            $dev = new device_info();
                            $dev->dev_id = $result[0]->dev_id;
                            $r_dev = $dev->fetch(FALSE);

                            $urn_info_list[$ind] = array ( 'net_descr' => $r_net[0]->net_descr,
                                    'dev_descr' => $r_dev[0]->dev_descr,
                                    'port_number' => $result[0]->port);
                            $ind++;
                        }

                    } else {
                        //posição não válida
                        $urn_info_list[$ind] = NULL;
                        $ind++;
                    }
                }
                //Framework::debug('geturninfo return',$urn_info_list);
                return $urn_info_list;
            } else return NULL;
        }

        function getURNDetails($urn_string) {

            if (!isset($urn_string)) {
                return MeicanTopology::getURNDetails();
            } elseif (is_string($urn_string)) {
                return MeicanTopology::getURNDetails($urn_string);
            }
            return NULL;
        }

        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

        $server->service($POST_DATA);
    }
}

?>
