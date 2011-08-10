<?php

include_once("libs/domain.inc");

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

    private $path; //deve conter o srcEndpoint e o destEndpoint e os hops intermediários separados por ;
    private $status;
    private $requestTime;
    private $pathSetupMode;
    private $grisString;
    private $statusArray = Array();

    function OSCARSReservation() {

//        $this->setOscarsUrl($oscars_ip);
//        $this->setDescription($reservation_info->res_name);
//        $this->setSrcEndpoint($reservation_info->urn_src);
//        $this->setDestEndpont($reservation_info->urn_dst);
//        $this->setBandwidth($resDetails->bandwidth);
//        $this->setStartTimestamp($resDetails->begin_timestamp);
//        $this->setEndTimestamp($resDetails->end_timestamp);
//        $this->setPath($resDetails->path);
        $this->path = "null";
        $this->startTimestamp = 132412312;
        $this->endTimestamp = 1232131322;
        $this->srcIsTagged = "null";
        $this->srcTag = "null";
        $this->destIsTagged = "null";
        $this->destTag = "null";
        $this->pathSetupMode = "xml-signal";
        $this->description = "Reservation from MEICAN";
        $this->bandwidth = 100;

    }

    public function setOscarsUrl($oscars_ip) {
        $this->oscarsUrl = "http://$oscars_ip/axis2/services/OSCARS";
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
    private function setSrcIsTagged($isTagged) {
        if ($isTagged)
            $this->srcIsTagged = "true";
        else $this->srcIsTagged = "false";
    }
    private function setSrcTag($vlan) {
        $this->srcTag = $vlan;
    }
    private function setDestIsTagged($isTagged) {
        if ($isTagged)
            $this->destIsTagged = "true";
        else $this->destIsTagged = "false";
    }
    private function setDestTag($vlan) {
        $this->destTag = $vlan;
    }
    public function setVlan($isTagged, $vlan=null) {
        $this->setSrcIsTagged($isTagged);
        $this->setDestIsTagged($isTagged);

        if ($vlan){
        $this->setDestTag($vlan);
        $this->setSrcTag($vlan);
        }
    }
    public function setStatus($status) {
        $this->status = $status;
    }
    public function getStatus() {
        return $this->status;
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
        $this->grisString = implode(";",$gris);
    }


//    //concatena o endpointsrc no inicio do path e o endpointdst no fim
//    public function buildPath() {
//        $path[]=$this->srcEndpoint;
//        $path[]=$this->path;
//        $path[]=$this->destEndpoint;
//        $this->path = implode(";", $path);
//    }

    function createReservation() {

        if (!(isset($this->srcEndpoint) && isset($this->destEndpoint) && 
                isset($this->oscarsUrl) && isset($this->startTimestamp) &&
                        isset($this->endTimestamp))) {
            Framework::debug("parametros insuficientes no createreservation");
            return false;
        }

        $envelope = array (
                'oscars_url' => $this->oscarsUrl,
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
        );
        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->createReservation($envelope);

            if (!$err = array_shift($result->return)){
                 $this->setGri($result->return[0]);
                 $this->setStatus($result->return[1]);
                 Framework::debug("retorno do create reservation", $result->return);
                 return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ",$err);
                return false;
            }
           
        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function queryReservation() {
        if (!isset($this->gri)) {
            Framework::debug("gri not set in query reservation");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
                "gri" => $this->gri
        );

        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->queryReservation($envelope);
            if (!$err = array_shift($result->return)) {
                $this->setGri($result->return[0]);
                $this->setDescription($result->return[1]);
                $this->setLogin($result->return[2]);
                $this->setStatus($result->return[3]);
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
                Framework::debug("retorno do query reservation", $result->return);
                return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ",$err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

     function modifyReservation() {

        if (!(isset($this->oscarsUrl) && isset($this->startTimestamp) &&
                        isset($this->endTimestamp))) {
            Framework::debug("parametros insuficientes no modify reservation");
            return false;
        }

        $envelope = array (
                'oscars_url' => $this->oscarsUrl,
                'startTimestamp' => $this->startTimestamp,
                'endTimestamp' => $this->endTimestamp
        );
        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->modifyReservation($envelope);

            if (!$err = array_shift($result->return)){
                 $this->setGri($result->return[0]);
                 $this->setStatus($result->return[1]);
                 Framework::debug("retorno do create reservation", $result->return);
                 return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ",$err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function cancelReservation() {
        if (!isset($this->gri)) {
            Framework::debug("gri not set in cancel reservation");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
                "gri" => $this->gri
        );
        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->cancelReservation($envelope);
            if (!$err = array_shift($result->return)){
                $this->setGri($result->return[0]);
                $this->setStatus($result->return[1]);
                Framework::debug("retorno do cancel reservation", $result->return);
                return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ",$err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function listReservations() {
        if (!isset($this->oscarsUrl)) {
            Framework::debug("oscarsUrl not set in list reservations");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
                "grisString" => $this->grisString
        );

        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->listReservations($envelope);

            if (!$err = array_shift($result->return)){ //tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
                foreach ($result->return as $r)
                        $this->statusArray[] = $r;

                return true;

            } else {
                Framework::debug("OSCARS Bridge Error: ",$result->return[0]);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function getTopology(){
        if (!isset($this->oscarsUrl)) {
            Framework::debug("oscarsUrl not set in get topology");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
        );

        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->getTopology($envelope);

            if (!$err = array_shift($result->return)){ //tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
                //precisa tratar
                return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ", $err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function createPath(){
        if (!isset($this->oscarsUrl)) {
            Framework::debug("oscarsUrl not set in create path");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
                "gri" => $this->gri
        );

        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->createPath($envelope);

            if (!$err = array_shift($result->return)){ //tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
                $this->setGri($result->return[0]);
                $this->setStatus($result->return[1]);
                return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ", $err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function teardownPath(){
        if (!isset($this->oscarsUrl)) {
            Framework::debug("oscarsUrl not set in create path");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
                "gri" => $this->gri
        );

        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->teardownPath($envelope);

            if (!$err = array_shift($result->return)){ //tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
                $this->setGri($result->return[0]);
                $this->setStatus($result->return[1]);
                return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ", $err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }

    function refreshPath(){
        if (!isset($this->oscarsUrl)) {
            Framework::debug("oscarsUrl not set in create path");
            return false;
        }

        $envelope = array (
                "oscars_url" => $this->oscarsUrl,
                "gri" => $this->gri
        );

        try {
            $client = new SoapClient(Framework::$OSCARSBridgeEPR, array('cache_wsdl' => 0));
            $result = $client->refreshPath($envelope);

            if (!$err = array_shift($result->return)){ //tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
                $this->setGri($result->return[0]);
                $this->setStatus($result->return[1]);
                return true;
            } else {
                Framework::debug("OSCARS Bridge Error: ", $err);
                return false;
            }

        } catch (Exception $e) {
            Framework::debug("Caught exception: ",  $e->getMessage());
            return false;
        }
    }
}


?>
