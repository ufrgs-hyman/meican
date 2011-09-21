<?php
include_once 'libs/controller.php';
require_once 'includes/nuSOAP/lib/nusoap.php';
include_once 'apps/topology/models/topology.inc';

include_once 'apps/aaa/models/user_info.inc';
include_once 'apps/aaa/models/group_info.inc';
include_once 'apps/topology/models/meican_info.inc';


class ws extends Controller {

    public function ws() {
        $this->app = 'aaa';
        $this->controller = 'ws';
        $this->defaultAction = '';

        $this_meican = new meican_info();
        $this_ip = $this_meican->getLocalMeicanIp();
        $this_dir_name = $this_meican->getLocalMeicanDirName();

        $namespace = "http://MEICAN";
        $server = new nusoap_server();//TODO: verificar o $this_dir_name
        $server->configureWSDL("MEICAN_AAA_SERVICES", $namespace, "http://$this_ip/$this_dir_name{$this->app}/ws");
        //$server->wsdl->schemaTargetNamespace = "http://schemas.xmlsoap.org/soap/encoding/";

        $server->wsdl->addComplexType('userType','complexType','struct','all','',
                array('usr_id' => array('name' => 'usr_id','type' => 'xsd:int'),
                'usr_name' => array('name' => 'usr_name','type' => 'xsd:string')));

        $server->wsdl->addComplexType('userTypeList','complexType','array','','http://schemas.xmlsoap.org/soap/encoding/:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'tns:userType[]'),
                'tns:userType'));

        $server->wsdl->addComplexType('groupType','complexType','struct','all','',
                array('grp_id' => array('name' => 'grp_id','type' => 'xsd:int'),
                'grp_descr' => array('name' => 'grp_descr','type' => 'xsd:string')));

        $server->wsdl->addComplexType('groupTypeList','complexType','array','','SOAP-ENC:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'tns:groupType[]'),
                'tns:groupType'));

        $server->register(
                'getUsers',
                array('usr' => 'tns:userType'),
                array('usr_list'=> 'tns:userTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getUsers",
                'rpc',
                'encoded',
                'Complex Hello World Method');
        $server->register(
                'getGroups',
                array('grp' => 'tns:groupType'),
                array('grp_list'=> 'tns:groupTypeList'),
                $namespace,
                "http://$this_ip/".Framework::$systemDirName."$this->app/ws/getGroups",
                'rpc',
                'encoded',
                'Complex Hello World Method');


        function getUsers($usr) {
            Framework::debug('getusers',$usr);
            $user = new user_info();

            if (isset($usr) && is_array($usr)) {
                if (isset($usr['usr_id']) && is_int($usr['usr_id']))
                    $user->usr_id = $usr['usr_id'];
                if (isset($usr['usr_name']) && is_string($usr['usr_name']))
                    $user->usr_name = $usr['usr_name'];
            }

            $result = $user->fetch(FALSE);

            foreach ($result as $r)
                $return[] = array('usr_id' => $r->usr_id, 'usr_name' => $r->usr_name);

            if ($return)
                return $return;
            else return NULL;
        }
        function getGroups($grp) {
            $group = new group_info();

            if (isset($grp) && is_array($grp)) {
                if (isset($grp['grp_id']) && is_int($grp['grp_id']))
                    $group->grp_id = $grp['grp_id'];
                if (isset($grp['grp_descr']) && is_string($grp['grp_descr']))
                    $group->grp_descr = $grp['grp_descr'];
            }

            $result = $group->fetch(FALSE);

            foreach ($result as $r)
                $return[] = array('grp_id' => $r->grp_id, 'grp_descr' => $r->grp_descr);

            if ($return)
                return $return;
            else return NULL;
        }

        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

        $server->service($POST_DATA);
    }
}

?>
