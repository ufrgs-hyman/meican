<?php

include_once 'OSCARSDriver.php';

/**
* Driver class to operate with OSCARS v0.6 ONLY!!
* - For OSCARS v0.5.3 and v0.5.4 functionality, refer to OSCARSDriver05 class.
*
* Receives calls from MEICAN and packages appropriate reservation query parameters and triggers a SOAP call to 
* OSCARSBridge.phpto invoke the corresponding client methods.
**/
class OSCARSDriver06 extends OSCARSDriver 
{
    /**
	* ------------------------------------------------------------------
    * The following variables are inherited (concrete) from parent class:
	* ------------------------------------------------------------------
    * protected $oscarsUrl;
	* protected $topoBridgeUrl;
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
	* protected $domainID;
	**/
	
    /**
    * Constructor
    **/
    function OSCARSDriver06() 
	{
        $this->path = "null";
        $this->startTimestamp = time();
        $this->endTimestamp = time() + 3600;
        $this->srcIsTagged = "true";
        $this->srcTag = "any";
        $this->destIsTagged = "true";
        $this->destTag = "any";
        $this->pathSetupMode = "signal-xml";
        $this->description = "Reservation from MEICAN";
        $this->bandwidth = 0;
        $this->version = "0.6";
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
    **/

	/** 
	* @Override parent abstract function.
	*
	* NOT USED IN THIS CLASS
	**/
	protected function makeEnvelope($params = array()) 
	{
		// DELETE THIS AND REMOVE ABSTRACTION IN OSCARSDRIVER.php
    }


    /**
    * @Override parent abstract function.
	*
	* Calls JAVA Bridge that handles actual invocation of methods on OSCARS. This method encapsulates calls to bridge in one place.
	* - Handles exceptions and errors.
    * @param string $method, SOAP method being called
    * @param array $envelope, Arguments used to call the method
    * @return mixed If some error happened, return false, otherwise, 
    * return an array of results
    **/
    protected function callBridge($method, $envelope) 
	{        		
		try 
		{
            $wsdl = Configure::read('OSCARSBridgeEPRv6');
            if (!@file_get_contents($wsdl))  //testa disponibilidade do wsdl 
			{ 
                throw new SoapFault('Server', 'No WSDL found at ' . $wsdl);
            }

            $client = new SoapClient($wsdl, array('trace' => 1,
                        						  'cache_wsdl' => WSDL_CACHE_NONE,
                        						  'exceptions' => true,
												  'encoding' => 'UTF-8'
												 )
									);		

			if($method != "getTopology")
			{
				$params = array('oscarsUrl' => $this->oscarsUrl);
				$result = $client->buildBridge($params);
			}
			
            $result = $client->$method($envelope);

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
		else if (!$result = $this->callBridge(
                'createReservation', array(
                    'description' => $this->description,
                    'srcEndpoint' => $this->srcEndpoint,
                    'srcIsTagged' => $this->srcIsTagged,
                    'srcTag' => $this->srcTag,
                    'destEndpoint' => $this->destEndpoint,
                    'destIsTagged' => $this->destIsTagged,
                    'destTag' => $this->destTag,
                    'path' => $this->path,
                    'bandwidth' => $this->bandwidth,
                    'pathSetupMode' => $this->pathSetupMode,
                    'startTimestamp' => $this->startTimestamp,
                    'endTimestamp' => $this->endTimestamp
                ))) 
		{
            return $this->error("Error to create reservation. Result:\n".print_r($result,true));
        } 
		else 
		{		
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
		else if (!$result = $this->callBridge('queryReservation', array("gri" => $this->gri)))
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
		else if (!$result = $this->callBridge('modifyReservation', array('gri' => $this->gri,
                    													'startTimestamp' => $this->startTimestamp,
                    													'endTimestamp' => $this->endTimestamp)))
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
    **/
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
		else if (!$result = $this->callBridge('cancelReservation', array("gri" => $this->gri)))
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
    **/
    function listReservations() 
	{
		error_log("LIST IN LIST: \n" . print_r($this->grisString, true));
		
        if (!$this->checkOscarsUrl()) 
		{
            return;
        } 
		else if (!$result = $this->callBridge('listReservations', array("grisString" => $this->grisString)))
		{
            return false;
		}
        else 
		{
            foreach ($result->return as $r)
                $this->statusArray[] = $r;

			error_log("STATUS ARRAY: \n" . print_r($this->statusArray, true));

            return true;
        }
    }


	/**
    * @Override parent abstract function.
	*
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
		else if (!$result = $this->callBridge('createPath', array("gri" => $this->gri)))
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
		else if (!$result = $this->callBridge('teardownPath', array("gri" => $this->gri)))
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
    * Retrieve all elements of OSCARS topology as a single String
    * - Tells callBridge to call the SOAP method getTopology()
    * @return true, if OSCARS SOAP status for getTopology = OK, false otherwise.
    **/
    function getTopology()
	{
        if (!$this->checkOscarsUrl() || !$this->domainID) 
		{
            return;
        }

		// Set topoBridge URL based on OSCARS URL (ASSUMES THEY ARE ON THE SAME MACHINE USING DEFAULT PORTS!) //
		$parsedURL = explode(":", $this->oscarsUrl);
		$this->topoBridgeUrl = $parsedURL[0] . ":" . $parsedURL[1] . ":9019/topoBridge";
		 		
		if (!$result = $this->callBridge('getTopology', array("topoBridge_url" => $this->topoBridgeUrl, "topologyID" => $this->domainID)))
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
        if (!$this->getTopology())
        {
			return false;
        }
        else 
        {
            foreach ($this->topology as $i) 
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

					$link_regEx = "/urn:ogf:network:domain=\S+:node=\S+:port=\S+:link=\S+/";
 
					if(preg_match($link_regEx, $array[0]))		//urn:ogf:network:domain=*:node=*:port=*:link=*
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

    function refreshPath(){ return $this->error("refreshPath() is NOT supported in your version of OSCARS!"); }

}