<?php

/**
 * Driver class to operate with OSCARS
 */
class OSCARSReservation {

    private $oscarsUrl;
    private $gri;
    private $description;
    private $srcEndpoint;
    private $destEndpoint;
    private $bandwidth;
    private $startTimestamp;
    private $endTimestamp;
    private $srcIsTagged;
    private $destIsTagged;
    private $srcTag;
    private $destTag;
    private $version;
    private $path; //deve conter o srcEndpoint e o destEndpoint e os hops intermediários separados por ';'
    private $status;
    private $requestTime;
    private $pathSetupMode;
    private $grisString;
    private $statusArray = Array();
    public $urns = Array();

    /**
     * Construct
     */
    function OSCARSReservation() {
        $this->path = "null";
        $this->startTimestamp = 132412312;
        $this->endTimestamp = 1232131322;
        $this->srcIsTagged = "true";
        $this->srcTag = "any";
        $this->destIsTagged = "true";
        $this->destTag = "any";
        $this->pathSetupMode = "xml-signal";
        $this->description = "Reservation from MEICAN";
        $this->bandwidth = 100;
        $this->version = "0.5.4";
    }

    /**
     * 
     * GETTERS and SETTERS
     * 
     */
    public function setOscarsUrl($idc_url) {
        $this->oscarsUrl = $idc_url;
    }

    public function setGri($gri) {
        $this->gri = $gri;
    }

    public function getGri() {
        return $this->gri;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setSrcEndpoint($srcEndpoint) {
        $this->srcEndpoint = $srcEndpoint;
    }

    public function setDestEndpoint($destEndpoint) {
        $this->destEndpoint = $destEndpoint;
    }

    public function setBandwidth($bandwidth) {
        $this->bandwidth = $bandwidth;
    }

    public function setStartTimestamp($startTimestamp) {
        $this->startTimestamp = $startTimestamp;
    }

    public function setEndTimestamp($endTimestamp) {
        $this->endTimestamp = $endTimestamp;
    }

    public function setPath($path) {
        $this->path = $path;
    }
    
    public function getPath() {
        return $this->path;
    }

    public function setSrcIsTagged($isTagged) {
        if ($isTagged)
            $this->srcIsTagged = "true";
        else
            $this->srcIsTagged = "false";
    }

    public function setSrcTag($vlan) {
        $this->srcTag = $vlan;
    }

    public function setDestIsTagged($isTagged) {
        if ($isTagged)
            $this->destIsTagged = "true";
        else
            $this->destIsTagged = "false";
    }

    public function setDestTag($vlan) {
        $this->destTag = $vlan;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getStatusArray() {
        return $this->statusArray;
    }

    public function getStartTimestamp() {
        return $this->startTimestamp;
    }

    public function getEndTimestamp() {
        return $this->endTimestamp;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setPathSetupMode($psm) {
        $this->pathSetupMode = $psm;
    }

    public function setRequestTime($date) {
        $this->requestTime = $date;
    }

    public function setGrisString($gris) {
        $this->grisString = implode(";", $gris);
    }

    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function checkOscarsUrl() {
        if (!isset($this->oscarsUrl)) {
            return $this->error("oscarsUrl not set");
        }
        return true;
    }

    public function checkVersion() {
        switch ($this->version) {
            case "0.5.3":
            case "0.5.4":
                return true;
            case "0.6":
            default:
                return $this->error(sprintf("Versão %s não suportada", $this->version));
        }
    }

    protected function error($error) {
        CakeLog::write('error', "OSCARSReservation: " . $error);
        return false;
    }

    protected function makeEnvelope($params = array()) {
        return array_merge(
                        array('oscars_url' => $this->oscarsUrl), $params);
    }

    /**
     * Calls some method of of the Bridge SOAP. Handles exceptions and errors.
     * @param string $method SOAP method being called
     * @param array $envelope Arguments used to call the method
     * @return mixed If some error happened, return false, otherwise, 
     * return an array of results
     */
    protected function callBridge($method, $envelope) {
        CakeLog::write("debug", print_r($method,TRUE));
        try {
            $wsdl = Configure::read('OSCARSBridgeEPR');
            if (!@file_get_contents($wsdl)) { //testa disponibilidade do wsdl
                throw new SoapFault('Server', 'No WSDL found at ' . $wsdl);
            }
            $client = new SoapClient($wsdl, array(
                        'trace' => 1,
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        'exceptions' => true));
            $result = $client->__soapCall($method, array($envelope));
             CakeLog::write("debug", print_r($result,TRUE));
            if (is_string($result->return)) {
                return $this->error("OSCARS Bridge Error: " . $result->return);
            } elseif (!$err = array_shift($result->return)) {//tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
                return $result;
            } else {
                return $this->error("OSCARS Bridge Error: " . $err);
            }
        } catch (Exception $e) {
            return $this->error("Caught exception: " . $e->getMessage());
        } catch (SoapFault $e) {
            return $this->error("Caught SoapFault: " . $e->getMessage());
        }
    }

    protected function setGriStatus($result) {
        $this->setGri($result->return[0]);
        $this->setStatus($result->return[1]);
        return true;
    }

    /**
     *
     * 
     * Functions that implement the WS calls, only for OSCARS 0.5.3 and 0.5.4!!!
     * TODO: make new architeture using polymorphism, having one class for each OSCARS version 
     * 
     */
    function createReservation() {
        if (!(isset($this->srcEndpoint) && isset($this->destEndpoint) &&
                isset($this->startTimestamp) && isset($this->endTimestamp))) {
            return $this->error("insufficient parameters for createreservation");
        } else if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return false;
        } else if (!$result = $this->callBridge(
                'createReservation', $this->makeEnvelope(array(
                    'description' => $this->description,
                    'srcUrn' => $this->srcEndpoint,
                    'isSrcTagged' => $this->srcIsTagged,
                    'srcTag' => $this->srcTag,
                    'destUrn' => $this->destEndpoint,
                    'isDestTagged' => $this->destIsTagged,
                    'destTag' => $this->destTag,
                    'path' => $this->path,
                    'bandwidth' => $this->bandwidth,
                    'pathSetupMode' => $this->pathSetupMode,
                    'startTimestamp' => $this->startTimestamp,
                    'endTimestamp' => $this->endTimestamp
                )))) {
            return $this->error("Error to create reservation. Result:\n".print_r($result,true));
        } else {
            CakeLog::write("debug","Create reservation result:\n".print_r($result,true));
            return $this->setGriStatus($result);
        }
    }

    function queryReservation() {
        if (!isset($this->gri)) {
            return $this->error("gri not set in query reservation");
        } else if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                        'queryReservation', $this->makeEnvelope(array(
                            "gri" => $this->gri
                        ))))
            return false;
        else {
            $this->setGri($result->return[0]);
            $this->setStatus($result->return[1]);
            $this->setDescription($result->return[2]);
            $this->setLogin($result->return[3]);
            $this->setRequestTime($result->return[4]);
            $this->setStartTimestamp($result->return[5]);
            $this->setEndTimestamp($result->return[6]);
            $this->setBandwidth($result->return[7]);
            $this->setPathSetupMode($result->return[8]);
            $this->setSrcEndpoint($result->return[9]);
            $this->setSrcIsTagged($result->return[10]);
            $this->setSrcTag($result->return[11]);
            $this->setDestEndpoint($result->return[12]);
            $this->setDestIsTagged($result->return[13]);
            $this->setDestTag($result->return[14]);
            $this->setPath($result->return[15]);
            CakeLog::write('debug', "Query reservation return:\n" . print_r($result->return, true));
            return true;
        }
    }

    function modifyReservation() {
        if (!(isset($this->startTimestamp) && isset($this->endTimestamp))) {
            return $this->error("insufficient parameters for modifyReservation");
        } else if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'modifyReservation', $this->makeEnvelope(array(
                    'oscars_url' => $this->oscarsUrl,
                    'startTimestamp' => $this->startTimestamp,
                    'endTimestamp' => $this->endTimestamp
                ))))
            return false;
        else
            return $this->setGriStatus($result);
    }

    function cancelReservation() {
        if (!isset($this->gri)) {
            return $this->error("gri not set in cancel reservation");
        } else if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'cancelReservation', $this->makeEnvelope(array(
                    "gri" => $this->gri
                ))))
            return false;
        else
            return $this->setGriStatus($result);
    }

    function listReservations() {
        if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'listReservations', $this->makeEnvelope(array(
                    "grisString" => $this->grisString
                ))))
            return false;
        else {
            foreach ($result->return as $r)
                $this->statusArray[] = $r;
            return true;
        }
    }

    function getTopology() {
        if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'getTopology', $this->makeEnvelope()))
            return false;
        else {
            $this->topology = $result->return;
            return true;
        }
    }

    function getUrns() {
        if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'getTopology', $this->makeEnvelope()))
            return false;
        else {
            foreach ($result->return as $i) {
                if ($array = explode(" ", $i)) {
                    //0- linkId
                    //1- remoteLinkId
                    //2- capacidade
                    //3- granularidade
                    //4- capacidade mínima reservável
                    //5- capacidade máxima reservável
                    //6- vlan range
                    if (!empty($array[1]) &&
                            ($array[1] == "urn:ogf:network:domain=*:node=*:port=*:link=*")) {
                        //é urn de ponto final
                        $urn = new stdClass();
                        $urn->id = $array[0];
                        $urn->capacity = $array[2];
                        $urn->granularity = $array[3];
                        $urn->minimumReservable = $array[4];
                        $urn->maximumReservable = $array[5];
                        $urn->vlanRange = $array[6];
                        $this->urns[] = $urn;
                    }
                }
            }
            return true;
        }
    }

    function createPath() {
        if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'createPath', $this->makeEnvelope(array(
                    "gri" => $this->gri
                ))))
            return false;
        else
            return $this->setGriStatus($result);
    }

    function teardownPath() {
        if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'teardownPath', $this->makeEnvelope(array(
                    "gri" => $this->gri
                ))))
            return false;
        else
            return $this->setGriStatus($result);
    }

    function refreshPath() {
        if (!$this->checkVersion() || !$this->checkOscarsUrl()) {
            return;
        } else if (!$result = $this->callBridge(
                'refreshPath', $this->makeEnvelope(array(
                    "gri" => $this->gri
                ))))
            return false;
        else
            return $this->setGriStatus($result);
    }

}