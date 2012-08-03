<?php
require_once("../../../libs/Vendors/nuSOAP/lib/nusoap.php");

$dir = str_replace("/apps/bpmStrategy","",__DIR__);
ini_set('include_path',ini_get('include_path').':'.$dir.':');
include_once ('requests.php');
//include_once ('../../aaa/controllers/users.inc');

$namespace = "http://localhost/qame";
$server = new nusoap_server();
$server->configureWSDL("BPM_Strategy_Services");
$server->wsdl->schemaTargetNamespace = $namespace;

$server->wsdl->addComplexType('requestType','complexType','struct','all','',
		array( 'id' => array('name' => 'id','type' => 'xsd:int'),
		       'domainSrc' => array('name' => 'domainSrc','type' => 'xsd:string'),
		       'userSrc' => array('name' => 'userSrc','type' => 'xsd:int'),
		       'domainDst' => array('name' => 'domainDst','type' => 'xsd:string'),
		       'userDst' => array('name' => 'userDst','type' => 'xsd:int'),
		       'question' => array('name' => 'question','type' => 'xsd:string'),
                       'answer' => array('name' => 'answer','type' => 'xsd:string'),
                       'status' => array('name' => 'status','type' => 'xsd:string')));

$server->wsdl->addComplexType('returnTypeC','complexType','struct','all','',
		array( 'status' => array('name' => 'status','type' => 'xsd:boolean'),
		       'message' => array('name' => 'message','type' => 'xsd:string')));

$server->wsdl->addComplexType('userType','complexType','struct','all','',
		array('usrId' => array('name' => 'usrId','type' => 'xsd:int'),
                    'usrName' => array('name' => 'usrName','type' => 'xsd:string')));

$server->wsdl->addComplexType('userListType','complexType','array','all','',
		array('users' => array('name' => 'users','type' => 'tns:userType')));

$server->register(
                'saveRequestRemote',
                array('name'=>'tns:requestType'),
                array('return'=>'tns:returnTypeC'),
                $namespace,
                false,
                'rpc',
                'encoded',
                'Complex Hello World Method');

$server->register(
                'saveReplyRemote',
                array('name'=>'tns:requestType'),
                array('return'=>'tns:returnTypeC'),
                $namespace,
                false,
                'rpc',
                'encoded',
                'Complex Hello World Method');


$server->register(
                'getUsers',
                array(),
                array('return'=>'tns:userListType'),
                $namespace,
                false,
                'rpc',
                'encoded',
                'Complex Hello World Method');

function saveRequestRemote($request) {
    $msg->status = saveRequest($request);
    if ($msg->status==false) {
        $msg->message=getFlash();
    }
    else
         $msg->message='OK';
    return $msg;
}

function saveReplyRemote($request ) {
        $msg->status = saveReply($request);
    if ($msg->status==false) {
        $msg->message=getFlash();
    }
    else
         $msg->message='OK';
    return $msg;
}

function getUsers()
{
    $user = new user();
    $users = $user->fetch();

    while ($usr=$users->next()) {
            $return[]= array('usrId' => $usr->usrId,'usrName' => $usr->usrName);
        }
    return $return;
}

// Get our posted data if the service is being consumed
// otherwise leave this data blank.                
$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])
? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

// pass our posted data (or nothing) to the soap service                    
$server->service($POST_DATA);                
exit();
?>
