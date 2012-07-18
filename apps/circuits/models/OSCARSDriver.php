<?php

/**
* Driver class to operate with OSCARS. MEICAN will call the functions herein, which will result in a connection 
* establishment with the appropriate Bridge (JAVA or PHP).
* 
* This class is Abstract and extended by concrete classes OSCARSDriver05 and OSCARSDriver06.
* - Concrete classes inherit all variables, and getter/setter functions.
*/
abstract class OSCARSDriver {

	/**********************************************************************************************
	* GLOBAL VARIABLES -- Inherited by ALL concrete children.
	**********************************************************************************************/	
    protected $oscarsUrl;
    protected $gri;
    protected $description;
    protected $srcEndpoint;
    protected $destEndpoint;
    protected $bandwidth;
    protected $startTimestamp;
    protected $endTimestamp;
    protected $srcIsTagged;
    protected $destIsTagged;
    protected $srcTag;
    protected $destTag;
    protected $version;
    protected $path; //deve conter o srcEndpoint e o destEndpoint e os hops intermediários separados por ';'
    protected $status;
    protected $requestTime;
    protected $pathSetupMode;
    protected $grisString;		// Used for call to listReservations()
    protected $statusArray = Array();
    public $urns = Array();
    
    /**********************************************************************************************
    * GETTERS and SETTER functions -- Inherited by ALL concrete children.
    **********************************************************************************************/
    public function setOscarsUrl($idc_url) 
    {
        $this->oscarsUrl = $idc_url;
    }

    public function setGri($gri) 
    {
        $this->gri = $gri;
    }

    public function getGri() 
    {
        return $this->gri;
    }

    public function setDescription($description) 
    {
        $this->description = $description;
    }

    public function setSrcEndpoint($srcEndpoint) 
    {
        $this->srcEndpoint = $srcEndpoint;
    }

    public function setDestEndpoint($destEndpoint) 
    {
        $this->destEndpoint = $destEndpoint;
    }

    public function setBandwidth($bandwidth) 
    {
        $this->bandwidth = $bandwidth;
    }

    public function setStartTimestamp($startTimestamp) 
    {
        $this->startTimestamp = $startTimestamp;
    }

    public function setEndTimestamp($endTimestamp) 
    {
        $this->endTimestamp = $endTimestamp;
    }

    public function setPath($path) 
    {
        $this->path = $path;
    }
    
    public function getPath() 
    {
        return $this->path;
    }

    public function setSrcIsTagged($isTagged) 
    {
        if ($isTagged)
            $this->srcIsTagged = "true";
        else
            $this->srcIsTagged = "false";
    }

    public function setSrcTag($vlan) 
    {
        $this->srcTag = $vlan;
    }

    public function setDestIsTagged($isTagged) 
    {
        if ($isTagged)
            $this->destIsTagged = "true";
        else
            $this->destIsTagged = "false";
    }

    public function setDestTag($vlan) 
    {
        $this->destTag = $vlan;
    }

    public function setStatus($status) 
    {
        $this->status = $status;
    }

    public function getStatus() 
    {
        return $this->status;
    }

    public function getStatusArray() 
    {
        return $this->statusArray;
    }

    public function getStartTimestamp() 
    {
        return $this->startTimestamp;
    }

    public function getEndTimestamp() 
    {
        return $this->endTimestamp;
    }

    public function setLogin($login) 
    {
        $this->login = $login;
    }

    public function setPathSetupMode($psm) 
    {
        $this->pathSetupMode = $psm;
    }

    public function setRequestTime($date) 
    {
        $this->requestTime = $date;
    }

    public function setGrisString($gris) 
    {
        $this->grisString = implode(";", $gris);
    }

    protected function setGriStatus($result) 
    {
        $this->setGri($result->return[0]);
        $this->setStatus($result->return[1]);
        return true;
    }

    public function checkOscarsUrl() 
    {
        if (!isset($this->oscarsUrl)) 
        {
            return $this->error("oscarsUrl not set");
        }
        return true;
    }

    protected function error($error) 
    {
        Log::write('error', "OSCARSDriver: " . $error);
        return false;
    }
    
    /**********************************************************************************************
    * ABSTRACT FUNCTIONS -- Concrete implementations exist for each version of OSCARS
    **********************************************************************************************/
    abstract protected function makeEnvelope($params = array());  // Packages data to be sent by OSCARS calls to reduce parameter passing.
    
    // OSCARS v0.5 client results in call to OSCARSBridge.java (via SOAP messages)
    // OSCARS v0.6 client results in call to OSCARSBridge.php
    abstract protected function callBridge($method, $envelope);	// Calls bridge that handles actual invocation of methods on OSCARS

    /**
    * Functions that implement the OSCARS calls.
    **/
    abstract function createReservation(); 	// Submit a circuit creation request to OSCARS

    abstract function queryReservation();	// Query status of existing reservation from OSCARS

    abstract function modifyReservation(); 	// Update parameters of existing reservation in OSCARS

    abstract function cancelReservation();	// Cancel active/reserved reservation in OSCARS

    abstract function listReservations();	// Queries OSCARS for a list of status of specific reservations (as described by global variable $grisString)

    abstract function getTopology(); 		// Retrieve all elements of OSCARS topology as a single String

    abstract function getUrns();			// Parse specific URNs from the OSCARS topology

    abstract function createPath(); 		// Build path in OSCARS

    abstract function teardownPath();		// Destroy path in OSCARS

    abstract function refreshPath(); 		// Refresh path in OSCARS

}