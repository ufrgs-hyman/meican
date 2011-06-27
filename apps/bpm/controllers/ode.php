<?php

include_once 'libs/controller.php';

class ode extends Controller {

    function ode() {
        $this->app = 'bpm';
        $this->controller = 'ode';
        $this->defaultAction = 'show';
        $this->ode_ip = "localhost:8080";
    }

    function show() {

        $this->setAction('show');

        try {
            $client = new SoapClient("http://$this->ode_ip/ode/processes/ProcessManagement?wsdl",array('cache_wsdl' => 0));
            $allProcess = $client->listAllProcesses(NULL);

            if (!empty($allProcess->{"process-info-list"}->{"process-info"}) && !is_array($allProcess->{"process-info-list"}->{"process-info"})) {
                $allProcess->{"process-info-list"}->{"process-info"} = array($allProcess->{"process-info-list"}->{"process-info"});
            }
            $processes = $allProcess->{"process-info-list"}->{"process-info"};

        }
        catch (Exception $e) {
            $this->setFlash('Unable to connect to ODE System', 'fatal');
        }

        $this->addScript('ajaxSubmit');
        $this->setArgsToBody($processes);
        $this->render();
    }

    function deployProcess() {

        if ($name_process = Common::POST('name_process')) {

            if ($_FILES['upload']['error'] > 0) {
                $this->setFlash(_('Error on upload file'), 'fatal');

            } else {
                $upl  = 'Upload: ' . $_FILES['upload']['name'];
                $upl .= 'Type: ' . $_FILES['upload']['type'];
                $upl .= 'Size: ' . ($_FILES['upload']['size'] / 1024);
                ' Kb';
                $upl .= 'Stored in: ' . $_FILES['upload']['tmp_name'];
                Framework::debug('upload file',$upl);

                try {
                    if ($client = new SoapClient("http://$this->ode_ip/ode/processes/DeploymentService?wsdl",array('cache_wsdl' => 0))) {

                        $filename = $_FILES['upload']['tmp_name'];
                        $handle = fopen($filename, "r");
                        $contents = fread($handle, filesize($filename));
                        fclose($handle);
                        $zipFile =  array('zip' => $contents);
                        $process = array('name' => $name_process, 'package' => $zipFile);

                        $client->deploy($process);
                        $this->setFlash( _('Process deployed successfully ').$result->response->name, 'success');
                    }

                } catch (Exception $e) {
                    Framework::debug('ODE Error: '. $e->getMessage());
                    $this->setFlash(_('Fail to deploy process: '.$error ), 'error');
                }
            }
        } else {
            $this->setFlash(_('invalid name for the process'), 'error');
        }
        $this->show();
        return;
    }

    function undeployProcess($input) {

        $package = $input['package'];

        $client = new SoapClient("http://$this->ode_ip/ode/processes/DeploymentService?wsdl",array('cache_wsdl' => 0));
        $pack = array('packageName' => $package);
        $result = $client->undeploy($pack);

        if ($result->response)
            $this->setFlash( _('Process undeployed successfully'),'success');
        else $this->setFlash(_('Fail to undeploy process'),'error');

        $this->show();
        return;
    }
}

?>
