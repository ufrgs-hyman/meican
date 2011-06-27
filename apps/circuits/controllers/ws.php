<?php
include_once 'libs/controller.php';
require_once 'includes/nuSOAP/lib/nusoap.php';
include_once 'apps/circuits/models/reservation_info.inc';
include_once 'apps/circuits/models/flow_info.inc';
include_once 'apps/circuits/models/timer_info.inc';

class ws extends Controller {

    public function ws() {
        $this->app = 'circuits';
        $this->controller = 'ws';
        $this->defaultAction = '';

        $this_ip = Framework::$domIp;
        $namespace = "http://localhost/qame";
        $server = new nusoap_server();
        $server->configureWSDL("Circuits_Services", $namespace, "http://$this_ip/".Framework::$systemDirName."/main.php?app=$this->app&amp;services");
        $server->wsdl->schemaTargetNamespace = $namespace;

        $server->wsdl->addComplexType('resType','complexType','struct','all','',
                array('res_name' => array('name' => 'res_name','type' => 'xsd:string')));

        $server->wsdl->addComplexType('resTypeList','complexType','array','','SOAP-ENC:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'tns:resType[]'),
                'tns:resType'));

        $server->wsdl->addComplexType('intTypeList','complexType','array','','SOAP-ENC:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'xsd:int[]'),
                'xsd:int'));

        $server->wsdl->addComplexType('flowType','complexType','struct','all','',
                array(
                'src_dom_ip' => array('name' => 'src_domain_ip', 'type' => 'xsd:string'),
                'src_urn_string' => array('name' => 'src_urn_string', 'type' => 'xsd:string'),
                'dst_dom_ip' => array('name' => 'dst_domain_ip', 'type' => 'xsd:string'),
                'dst_urn_string' => array('name' => 'dst_urn_string', 'type' => 'xsd:string'),
                'bandwidth' => array('name' => 'bandwidth', 'type' => 'xsd:int')));

        $server->wsdl->addComplexType('timerType','complexType','struct','all','',
                array(
                'start' => array('name' => 'start', 'type' => 'xsd:date'),
                'finish' => array('name' => 'finish', 'type' => 'xsd:date'),
                'recurrence' => array('name' => 'recurrence', 'type' => 'xsd:string')));
       
       $server->register(
                'getResInfo',
                array('res_id_list'=>'tns:intTypeList'),
                array('res_info_list'=>'tns:resTypeList'),
                $namespace,
                "http://$this_ip/".Framework::$systemDirName."/main.php?app=$this->app&amp;services/getResInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getFlowInfo',
                array('res_id'=>'xsd:int'),
                array('flow_info'=>'tns:flowType'),
                $namespace,
                "http://$this_ip/".Framework::$systemDirName."/main.php?app=$this->app&amp;services/getFlowInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getTimerInfo',
                array('res_id'=>'xsd:int'),
                array('timer_info'=>'tns:timerType'),
                $namespace,
                "http://$this_ip/".Framework::$systemDirName."/main.php?app=$this->app&amp;services/getTimerInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

       function getResInfo($res_id_list) {
            Framework::debug('getresinfo',$res_id_list);
            if (isset($res_id_list) && is_array($res_id_list)) {
                $ind = 0;
                unset($res_info_list);
                foreach ($res_id_list as $res_id) {
                    if (isset($res_id) && is_int($res_id)) {
                        $reservation = new reservation_info();
                        $reservation->res_id = $res_id;
                        $result = $reservation ->fetch(FALSE);

                        if ($result)
                            $res_info_list[$ind] = array('res_name' => $result[0]->res_name);
                        else $res_info_list[$ind] = NULL;

                        $ind++;
                    }
                }
                return $res_info_list;
            } else return NULL;
        }

        function getFlowInfo($res_id) {
            Framework::debug('getflowinfo',$res_id);
            if (isset($res_id) && is_int($res_id)) {
                $reservation = new reservation_info();
                $reservation->res_id = $res_id;

                $res = $reservation->fetch(FALSE);

                if (!$res) {
                    Framework::debug('reservation not found');
                    return NULL;
                } else {

                    $flow = new flow_info();
                    $flow->flw_id = $res[0]->flw_id;
                    $return = $flow->getFlowDetails2();

                    if ($return) {
                        return $return;
                        
                    } else {
                        Framework::debug('flow not found');
                        return NULL;
                    }
                }
            } else {
                Framework::debug('res_id not int');
                return NULL;
            }
        }

        function getTimerInfo($res_id) {
            Framework::debug('gettimerinfo',$res_id);
            if (isset($res_id) && is_int($res_id)) {
                $reservation = new reservation_info();
                $reservation->res_id = $res_id;

                $res = $reservation->fetch(FALSE);

                if (!$res) {
                    Framework::debug('reservation not found');
                    return NULL;
                }

                $timer = new timer_info();
                $timer->tmr_id = $res[0]->tmr_id;
                $timer_info = $timer->fetch(FALSE);

                if (!$timer) {
                    Framework::debug('timer not found');
                    return NULL;
                }

                $return = array(
                        'start' => $timer_info[0]->start,
                        'finish' => $timer_info[0]->finish,
                        'recurrence' => $timer_info[0]->summary);

                return $return;
            } else return NULL;
        }
        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        $server->service($POST_DATA);
    }
}

?>
