<?php

include_once 'OSCARSDriver.php';

/**
* Driver class to operate with OSCARS v0.5.3 and v0.5.4 ONLY!!
* - For OSCARS v0.6 functionality, refer to OSCARSDriver06 class.
*
* Receives calls from MEICAN and packages appropriate reservation query parameters and triggers a SOAP call to 
* OSCARSBridge.java to invoke the corresponding client methods.
**/
class OSCARSDriver05 extends OSCARSDriver 
{
    /**
    * ------------------------------------------------------------------
    * The following variables are inherited (concrete) from parent class:
    * ------------------------------------------------------------------
    * protected $oscarsUrl;
	* protected $topoBridgeUrl;		// USED ONLY FOR v0.6
    * protected $gri;
    * protected $description;
    * protected $srcEndpoint;
    * protected $destEndpoint;
    * protected $bandwidth;
    * protected $startTimestamp;
    * protected $endTimestamp;
    * protected $srcIsTagged;
    * protected $destIsTagged;
    * protected $srcTag;
    * protected $destTag;
    * protected $version;
    * protected $path; //deve conter o srcEndpoint e o destEndpoint e os hops intermediários separados por ';'
    * protected $status;
    * protected $requestTime;
    * protected $pathSetupMode;
    * protected $grisString;		// Used for call to listReservations()
    * protected $statusArray = Array();
    * public $urns = Array();
	* protected $domainID;			// USED ONLY FOR v0.6
    **/
	
    /**
    * Constructor
    **/
    function OSCARSDriver05() 
    {
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
	* ------------------------------------------------------------------
    * The following functions are inherited (concrete) from parent class:
	* ------------------------------------------------------------------
    *   public function setOscarsUrl($idc_url)
	*	public function setDomainID($domain)
	*	public function getDomainID()
	*   public function setGri($gri) 
	*   public function getGri() 
	*   public function setDescription($description) 
	*   public function getDescription() 	
	*   public function setSrcEndpoint($srcEndpoint) 
	*   public function setDestEndpoint($destEndpoint) 
	*   public function setBandwidth($bandwidth)
	*   public function setStartTimestamp($startTimestamp) 
	*   public function setEndTimestamp($endTimestamp) 
	*   public function setPath($path)
	*   public function getPath() 
	*   public function setSrcIsTagged($isTagged) 
	*   public function setSrcTag($vlan) 
	*   public function setDestIsTagged($isTagged)
	*   public function setDestTag($vlan)
	*   public function setStatus($status) 
	*	public function getBandwidth() 
	*   public function getStatus() 
	*   public function getStatusArray() 
	*   public function getStartTimestamp() 
	*   public function getEndTimestamp()
	*   public function setLogin($login) 
	*   public function setPathSetupMode($psm)
	*	public function getPathSetupMode()
	*   public function setRequestTime($date) 
	*   public function setGrisString($gris) 
	*   public function checkOscarsUrl() 
    *   protected function error($error)
	*   protected function setGriStatus($result)
    *	protected function setGriStatus($result)
    **/
    
    
    /** 
     * @Override parent abstract function.
     * 
     * Packages data to be sent by OSCARS calls to reduce parameter passing.
     * - Called by most of the OSCARS calls for dynamic array building.
     * @param array $params, array of arguments to merge with oscars_url
    **/
    protected function makeEnvelope($params = array()) 
    {
    	return array_merge(array('oscars_url' => $this->oscarsUrl), $params);
    }

    /**
    * @Override parent abstract function.
    * 
    * Calls JAVA Bridge that handles actual invocation of methods on OSCARS
    * - Handles exceptions and errors.
    * @param string $method, SOAP method being called
    * @param array $envelope, Arguments used to call the method
    * @return mixed If some error happened, return false, otherwise, 
    * return an array of results
    **/
    protected function callBridge($method, $envelope) 
    {
        Log::write("debug", print_r($method,TRUE));
        try 
        {
            $wsdl = Configure::read('OSCARSBridgeEPR');
            if (!@file_get_contents($wsdl))  //testa disponibilidade do wsdl 
            { 
                throw new SoapFault('Server', 'No WSDL found at ' . $wsdl);
            }

            $client = new SoapClient($wsdl, array('trace' => 1,
						  'cache_wsdl' => WSDL_CACHE_NONE,
						  'exceptions' => true));
									
            $result = $client->__soapCall($method, array($envelope));
            Log::write("debug", print_r($result,TRUE));

            if (is_string($result->return)) 
            {
                return $this->error("OSCARS Bridge Error: " . $result->return);
            } 
            elseif (!$err = array_shift($result->return)) //tira o primeiro elemento do array e retorna o conteudo do primeiro elemento do array
            {
                return $result;
            } 
            else 
            {
                return $this->error("OSCARS Bridge Error: " . $err);
            }
        } 
        catch (Exception $e) 
        {
            return $this->error("Caught exception: " . $e->getMessage());
        } 
        catch (SoapFault $e) 
        {
            return $this->error("Caught SoapFault: " . $e->getMessage());
        }
    }
    
    /**********************************************************************************************
    * Functions that implement the OSCARS calls.
    * - These functions override the parent's abstract methods ONLY for OSCARS v0.5.3 and 0.5.4
    * - For OSCARS v0.6 implementation, refer to OSCARSDriver06.php
    **********************************************************************************************/
    
    /**
    * @Override parent abstract function.
    * 
    * Submit a circuit creation request to OSCARS
    * - Tells callBridge to call the SOAP method createReservation()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for create = OK, Error otherwise.
    **/
    function createReservation() 
    {
        if (!(isset($this->srcEndpoint) && isset($this->destEndpoint) && isset($this->startTimestamp) && isset($this->endTimestamp))) 
        {
            return $this->error("insufficient parameters for createreservation");
        } 
        else if (!$this->checkOscarsUrl()) 
        {
            return false;
        } 
        else if (!$result = $this->callBridge('createReservation', $this->makeEnvelope(array(
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
		))))
		{
            return $this->error("Error to create reservation. Result:\n".print_r($result,true));
        } 
        else 
        {
            Log::write("debug","Create reservation result:\n".print_r($result,true));
            return $this->setGriStatus($result);
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Query status of existing reservation from OSCARS
    * - Tells callBridge to call the SOAP method queryReservation()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP call worked, false otherwise.
    **/
    function queryReservation() 
    {
        if (!isset($this->gri)) 
        {
            return $this->error("gri not set in query reservation");
        } 
        else if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('queryReservation', $this->makeEnvelope(array("gri" => $this->gri))))
        {
            return false;
        }
        else 
        {
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
            Log::write('debug', "Query reservation return:\n" . print_r($result->return, true));

            return true;
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Update parameters of existing reservation in OSCARS
    * - Tells callBridge to call the SOAP method modifyReservation()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for modify = OK, Error otherwise.
    **/
    function modifyReservation() 
    {
        if (!(isset($this->startTimestamp) && isset($this->endTimestamp))) 
        {
            return $this->error("insufficient parameters for modifyReservation");
        } 
        else if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('modifyReservation', 
        			$this->makeEnvelope(array('oscars_url' => $this->oscarsUrl,
							  'startTimestamp' => $this->startTimestamp,
							  'endTimestamp' => $this->endTimestamp
		))))
		{
            return false;
        }
        else
        {
            return $this->setGriStatus($result);
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Cancel active/reserved reservation in OSCARS
    * - Tells callBridge to call the SOAP method cancelReservation()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for cancel = OK, Error otherwise.
    * **/
    function cancelReservation() 
    {
        if (!isset($this->gri)) 
        {
            return $this->error("gri not set in cancel reservation");
        } 
        else if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('cancelReservation', $this->makeEnvelope(array("gri" => $this->gri))))
        {
            return false;
        }
        else
        {
            return $this->setGriStatus($result);
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Queries OSCARS for a list of status of specific reservations (as described by global variable $grisString)
    * - Tells callBridge to call the SOAP method listReservations()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for list = OK, false otherwise.
    * **/
    function listReservations() 
    {
        if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('listReservations', $this->makeEnvelope(array("grisString" => $this->grisString))))
        {
            return false;
        }
        else 
        {
            foreach ($result->return as $r)
                $this->statusArray[] = $r;

            return true;
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Retrieve all elements of OSCARS topology as a single String
    * - Tells callBridge to call the SOAP method getTopology()
    * @return true, if OSCARS SOAP status for getTopology = OK, false otherwise.
    **/
    function getTopology() 
    {
        if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('getTopology', $this->makeEnvelope()))
        {
            return false;
        }
        else 
        {
            $this->topology = $result->return;
            return true;
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Parse specific URNs from the OSCARS topology
    * - Tells callBridge to call the SOAP method getTopology()
    * - Parses the result of getTopology() call and stores its components into a urn object 
    * - Pushes each urn object onto the $urns global array
    * @return true, if OSCARS SOAP status for getTopology = OK, false otherwise.
    * **/
    function getUrns() 
    {
        if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('getTopology', $this->makeEnvelope()))
        {
            return false;
        }
        else 
        {
            foreach ($result->return as $i) 
            {
                if ($array = explode(" ", $i)) 
                {
                    //0- linkId
                    //1- remoteLinkId		-- not used here?
                    //2- capacidade
                    //3- granularidade
                    //4- capacidade mínima reservável
                    //5- capacidade máxima reservável
                    //6- vlan range
                    if (!empty($array[1]) && ($array[1] == "urn:ogf:network:domain=*:node=*:port=*:link=*")) 
                    {
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
    
    /**
    * @Override parent abstract function.
    * Build path in OSCARS
    * - Tells callBridge to call the SOAP method createPath()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for createPath = OK, false otherwise.
    **/
    function createPath() 
    {
        if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('createPath', $this->makeEnvelope(array("gri" => $this->gri))))
        {
            return false;
        }
        else
        {
            return $this->setGriStatus($result);
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Destroy path in OSCARS
    * - Tells callBridge to call the SOAP method teardownPath()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for teardownPath = OK, false otherwise.
    **/
    function teardownPath() 
    {
        if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('teardownPath', $this->makeEnvelope(array("gri" => $this->gri))))
        {
            return false;
        }
        else
        {
            return $this->setGriStatus($result);
        }
    }
    
    /**
    * @Override parent abstract function.
    * 
    * Refresh path in OSCARS
    * - Tells callBridge to call the SOAP method refreshPath()
    * - Passes in appropriate global request parameters after enveloping them together
    * @return true, if OSCARS SOAP status for refreshPath = OK, false otherwise.
    **/
    function refreshPath() 
    {
        if (!$this->checkOscarsUrl()) 
        {
            return;
        } 
        else if (!$result = $this->callBridge('refreshPath', $this->makeEnvelope(array("gri" => $this->gri))))
        {
            return false;
        }
        else
        {
            return $this->setGriStatus($result);
        }
    }

}