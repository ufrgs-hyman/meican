<?php

/**
* Driver class to operate with OSCARS v0.5.3 and v0.5.4 ONLY!!
* - For OSCARS v0.6 functionality, refer to OSCARSDriver06 class.
*
* Receives calls from MEICAN and packages appropriate reservation query parameters and triggers a SOAP call to 
* OSCARSBridge.java to invoke the corresponding client methods.
**/

/**		
		##################################################################################################
		# Stuff in Red is incomplete: Don't have requisite classes to implement in PHP  
		#  - However, all syntax has been modified from Java to PHP so when the classes are made, it should work (except 		
		#    for method calls and Exception handling).
		##################################################################################################
*/
class OSCARSBridge
{
	private $repoDir = "repo";
	private $returnMSG = "";
	private $client;
	
	/**
	*	Constructor
	**/
	function OSCARSBridge($method, $envelope) 
	{
		$methodToCall = $method . '()';
		$returnMSG = $this->$methodToCall($envelope);
	}
	
	function __construct($method, $envelope) 
	{
		$methodToCall = $method . '()';
		$returnMSG = $this->$methodToCall($envelope);
	}
	
	
	private function configSoap()
	{
		//try 
		//{
            $wsdl = Configure::read('OSCARSBridgeEPRv6');
            if (!@file_get_contents($wsdl))  //testa disponibilidade do wsdl 
			{ 
                throw new SoapFault('Server', 'No WSDL found at ' . $wsdl);
            }

            global $client = new SoapClient($wsdl, array('trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'exceptions' => true));
			
			$response = $client->__soapCall('OSCARSClient', $wsdUrl);
            Log::write("debug", print_r($result,TRUE));

            /*if (is_string($result->return)) 
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
            }*/
        //} 
		//catch (Exception $e) 
		//{
        //    return $this->error("Caught exception: " . $e->getMessage());
        //} 
		//catch (SoapFault $e) 
		//{
        //    return $this->error("Caught SoapFault: " . $e->getMessage());
        //}
	}
	
	public function getOSCARSResponse()
	{
		return $returnMSG;
	}
	
	###########################################################
    public function createReservation($requestParams) 
	{
		$oscars_url = $requestParams['oscars_url'];
		$description = $requestParams['description'];
		$srcUrn = $requestParams['srcUrn'];
		$isSrcTagged = $requestParams['isSrcTagged'];
		$srcTag = $requestParams['srcTag'];
		$destUrn = $requestParams['destUrn'];
		$isDestTagged = $requestParams['isDestTagged'];
		$destTag = $requestParams['destTag'];
		$path = $requestParams['path'];
		$bandwidth = $requestParams['bandwidth'];
		$pathSetupMode = $requestParams['pathSetupMode'];
		$startTimestamp = $requestParams['startTimestamp'];
		$endTimestamp = $requestParams['endTimestamp'];
		
        $repo = $repoDir;
        
		$retorno = Array(); 
        $message = "";


        /**
        * pathSetupMode (string)
        * "timer-automatic" means that the reserved circuit will be instantiated by the scheduler process
        * "signal-xml" means the user will signal to instantiate the reserved circuit
        * accepts any string in the path setup mode
		**/

/**
		require_once('../../../lib/nusoap.php');
		$response = $client->
		$response = $client->__soapCall('createReservation', $request);
        */
        /**
         * ResCreateContent
         *      PathInfo
         *          layer2Info
         **/



/**

        $layer2Info = new Layer2Info();
        $layer2Info->setSrcEndpoint($srcUrn);
        $layer2Info->setDestEndpoint($destUrn);

        $pathInfo = new PathInfo();

        $pathInfo->setPathSetupMode($pathSetupMode);
*/
        $setPath = true;

        if ($path == "null") 
		{
            $hops = Array();
			$hops = explode(";", $path);
            
			/**
             *  pontos origem e destino devem ser colocados completos nos hops,
             *  se o ponto de início/fim for algum hop antes, ele configura
             *  somente até o hop discriminado pontos intermediários podem ser
             *  definidos parcialmente
             *
             */
/**
            $pathContent = new CtrlPlanePathContent();
            $pathContent->setId("userPath");

*/
            $hasEro = true;

            for ($i = 0; $i < count($hops); $i++) 
			{
                $propName = "ero_" $hops[$i];
                $hopId = $hops[$i];

                if ($hopId != null) 
				{
                    $hopId = trim($hopId);
                    $hopType = count(explode(":", $hopId));
                    $hasEro = true;
/**
                    $hop = new CtrlPlaneHopContent();
                    $hop->setId($i . "");
                    
					if ($hopType == 4) 
					{
                        $hop->setDomainIdRef($hopId);
                    } 
					else if ($hopType == 5) 
					{
                        $hop->setNodeIdRef($hopId);
                    } 
					else if ($hopType == 6) 
					{
                        $hop->setPortIdRef($hopId);
                    } 
					else 
					{
                        $hop->setLinkIdRef($hopId);
                    }

                    $pathContent->addHop($hop);
*/   
             	}
            }
            if ($hasEro) 
			{
/**
                $pathInfo->setPath($pathContent);
*/                //pathInfo->setPathType("0");
            }
        }
/**
        $request = new ResCreateContent();
        $request->setBandwidth($bandwidth);
        $request->setStartTime($startTimestamp);
        $request->setEndTime($endTimestamp);
        $request->setDescription($description);
        $request->setPathInfo($pathInfo);

        $srcVtag = new VlanTag();

        if ($isSrcTagged == "true") 
		{
            $srcVtag->setTagged(true);
            $srcVtag->setString($srcTag);
        } 
		else 
		{
            $srcVtag->setTagged(false);
            $srcVtag->setString("0");
        }

        $layer2Info->setSrcVtag($srcVtag);

        $destVtag = new VlanTag();

        if ($isDestTagged == "true") 
		{
            $destVtag->setTagged(true);
            $destVtag->setString($destTag);
        } 
		else 
		{
            $destVtag->setTagged(false);
            $destVtag->setString("0");
        }
        $layer2Info->setDestVtag(destVtag);

        $pathInfo->setLayer2Info($layer2Info);
*/


        try 
		{
/**
            $response = $oscarsClient->createReservation($request);  #CreateReply response
            $gri = $response->getGlobalReservationId();
            $status = $response->getStatus();

            echo "GRI: $gri\n";
            echo "Initial Status: $status\n";

            array_push($retorno, "", $gri, $status);
*/
        } 
/**
		catch(RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_push($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_push($retorno, "Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_push($retorno, "Error: Exception ($message)");
        }
*/
        return $retorno;
    }

	###########################################################
	public function queryReservation($oscars_url, $gri) 
	{
		$repo = $repoDir;

	    $retorno = new Array();
	    $message = "";
		/**
	    $oscarsClient = new OSCARSClient();
		*/

	    try 
		{
			/**
	    	$request = new GlobalReservationId();
	        $request->setGri(gri);

	        $response = $oscarsClient->queryReservation($request);

	        $pathInfo = $response->getPathInfo();
	        $path = $pathInfo->getPath();
	        $layer2Info = $pathInfo->getLayer2Info();
	//      $layer3Info = $pathInfo->getLayer3Info();
	//      $mplsInfo = $pathInfo->getMplsInfo();

	        echo "GRI: $response->getGlobalReservationId()\n";
	        echo "Status: $response->getStatus()\n";
	        echo "Description: $response->getDescription()\n";
	        echo "Login: $response->getLogin()\n";

	        echo "Time of request: $response->getCreateTime()\n";
	        echo "Start Time: $response->getStartTime()\n";
	        echo "End Time: $response->getEndTime()\n";
	        echo "Bandwidth: $response->getBandwidth()\n";
	        echo "Path Setup Mode: $pathInfo->getPathSetupMode()\n";

			array_push($retorno, $response->getGlobalReservationsId());
			array_push($retorno, $response->getStatus());
			array_push($retorno, $response->getDescription());
			array_push($retorno, $response->getLogin());
			
			array_push($retorno, "" . $response->getCreateTime());
			array_push($retorno, "" . $response->getStartTime());
			array_push($retorno, "" . $response->getEndTime());
			array_push($retorno, "" . $response->getBandwidth());
			
			array_push($retorno, $pathInfo->getPathSetupMode());
			*/
			
			/**
	        if ($layer2Info != null) 
			{
	        	echo "Source Endpoint: $layer2Info->getSrcEndpoint()\n";
	            $srcVtag = new VlanTag();
	            $srcVtag = $layer2Info->getSrcVtag();
	            $srcVlan = $srcVtag->getString();
	            $isTagged = $srcVtag->getTagged();
	            
				echo "Is Src tagged: $isTagged\n";
	            echo "Vlan Src value: $srcVlan\n";
	
				array_push($retorno, $layer2Info->getSrcEndpoint());
				array_push($retorno, (string)$isTagged);
				array_push($retorno, $srcVlan);
				
	            echo "Destination Endpoint: $layer2Info->getDestEndpoint()\n";
	            
				$destVtag = new VlanTag();
	            $destVtag = $layer2Info->getDestVtag();
	            $destVlan = $destVtag->getString();
	            $isTaggedDest = $destVtag->getTagged();
	            
				echo "Is Dest tagged: $isTaggedDest\n";
	            echo "Vlan Dest value: $destVlan\n";
	
				array_push($retorno, $layer2Info.getDestEndpoint());
				array_push($retorno, (string)$isTaggedDest);
				array_push($retorno, $destVlan);
			}
			*/
			
	//      if ($layer3Info != null) 
	//		{
	//      	echo "Source Host: $layer3Info->getSrcHost()\n";
	//          echo "Destination Host: $layer3Info->getDestHost()\n";
	//          echo "Source L4 Port: $layer3Info->getSrcIpPort()\n";
	//          echo "Destination L4 Port: $layer3Info->getDestIpPort()\n";
	//          echo "Protocol: $layer3Info->getProtocol()\n";
	//          echo "DSCP: $layer3Info->getDscp()\n";
	//		}
	//      if ($mplsInfo != null) 
	//		{
	//      	echo "Burst Limit: $mplsInfo->getBurstLimit()\n";
	//          echo "LSP Class: $mplsInfo.getLspClass()\n");
	//      }

		/**
	        echo "Path: ";
			$pathString = "";

	        foreach ($path->getHop() as $hop) 
			{
				$link = new CtrlPlaneLinkContent();
	        	$link = $hop->getLink();
	            
				if ($link == null) 
				{
	            	//should not happen
	                $pathString = $pathString . 'no link';
	                $pathString = $pathString . ';';
	                continue;
	            }
	            
				$pathString = $pathString . "$link.getId()";
	            $pathString = $pathString . ';';
			}
			
	        echo "$pathString\n");
	        array_push($retorno, $pathString);
			array_unshift($retorno, "");
		} 
		catch (RemoteException $e) 
		{
	            $e.printStackTrace();
	            $message = $e.getMessage();
				array_unshift($retorno, "Error: RemoteException ($message)");
	    } 
		catch (AAAFaultMessage $e) 
		{
	            $e.printStackTrace();
	            $message = $e.getMessage();
				array_unshift($retorno, "Error: AAAFaultMessage ($message)");
	
	    } 
		catch (BSSFaultMessage $e) 
		{
	            $e.printStackTrace();
	            $message = $e.getFaultMessage().getMsg();
	            System.out.println("Error: BSSFaultMessage (" + message + ")");
	 			array_unshift($retorno, "Error: BSSFaultMessage ($message)");
	    } 
		catch (Exception $e) 
		{
	            $e.printStackTrace();
	            $message = $e.getMessage();
	            array_unshift($retorno, "Error: Exception ($message)");
	    }
	    */
	    return $retorno;
	}

	###########################################################	
	public function cancelReservation($oscars_url, $gri) 
	{
        $repo = $repoDir;
        $retorno = new Array();
        $message = "";

		/**
        $oscarsClient = new OSCARSClient();
        */
		/**
        $rt = new GlobalReservationId();

        $rt->setGri($gri);

        try 
		{
            $status = $oscarsClient->cancelReservation($rt);
            echo "Global Reservation Id: $gri\n";
            echo "Cancel Status: $status\n";
            
			array_push($retorno, $gri);
            array_push($retorno, $status);
            array_unshift($retorno, "");

        } 
		catch (RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
			array_unshift($retorno, "Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
			array_unshift($retorno, "Error: Exception ($message)");
        }
        */
        return $retorno;
    }

	###########################################################
    public function listReservations($oscars_url, $grisString) 
	{
        $repo = $repoDir;
		$retorno = new Array();
        $message = "";
        
		$gris = new Array();
        $gris = $grisString.explode(";");

		/**
        $oscarsClient = new OSCARSClient();
		*/
		
		/**
		$response = new ResDetails();
        $rt = new GlobalReservationId();
        */
		$temp;

        for ($ind = 0; $ind < strlen($gris); $ind++) 
		{
			/**
            $rt->setGri($gris[$ind]);

            try 
			{
                $response = $oscarsClient->queryReservation($rt);
                $temp = $response->getStatus();
            } 
			catch (RemoteException $e) 
			{
                $e.printStackTrace();
                $message = $e.getMessage();
                $temp = "Error: RemoteException ($message)";
            } 
			catch (AAAFaultMessage $e) 
			{
                $e.printStackTrace();
                $message = $e.getMessage();
                $temp = "Error: AAAFaultMessage ($message)";
            } 
			catch (Exception $e) 
			{
                $e.printStackTrace();
                $message = $e.getMessage();
                $temp = "Error: Exception ($message)";
            }
            */
			echo "$temp\n";
            array_push($retorno, $temp);
        }
        
		array_unshift($retorno, "");
        
        return $retorno;
    }

	###########################################################
    public function modifyReservation($oscars_url, $gri, $startTimestamp, $endTimestamp) 
	{
        $repo = $repoDir;
		$retorno = new Array();
        $message = "";

		/**
        $oscarsClient = new OSCARSClient();
        */

		/**
        try 
		{
            $content = new ModifyResContent();

            $content->setGlobalReservationId($gri);
            $content->setStartTime($startTimestamp);
            $content->setEndTime($endTimestamp);

            //PARAMETROS INUTEIS (USELESS PARAMETERS)
            $content->setBandwidth(100);
            $content->setDescription("nao sera alterada");

			$response = new ModifyResReply();
			$reservation = new ResDetails();
            
			$response = $oscarsClient->modifyReservation($content);
            $reservation = $response->getReservation();

            echo "Response:\n";
            echo "GRI: $reservation->getGlobalReservationId()\n");
            echo "Status: (string)($reservation->getStatus())\n");
            
			array_push($retorno, "");
            array_push($retorno, $reservation->getGlobalReservationId());
            array_push($retorno, (string)($reservation->getStatus()));

        } 
		catch (RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
			array_unshift($retorno, "Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
			array_unshift($retorno, "Error: Exception ($message)");
        }
        */

        return $retorno;
    }

	###########################################################
    public function createPath($oscars_url, $gri) 
	{
        $repo = $repoDir;
        $retorno = new Array();
		$message = "";

		/**
        $oscarsClient = new OSCARSClient();
		*/

		/**
		$createRequest = new CreatePathContent();
        $createRequest->setGlobalReservationId($gri);

        try 
		{
			$createResponse = new CreatePathResponseContent();
            $createResponse = $oscarsClient->createPath($createRequest);
            
			echo "Global Reservation Id: $createResponse->getGlobalReservationId()\n");
            echo "Create Status: $createResponse->getStatus()\n");

            array_push($retorno, "");
			array_push($retorno, $createResponse->getGlobalReservationId());
			array_push($retorno, $createResponse->getStatus());
        } 
		catch (RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno,"Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: Exception ($message)");
        }
        */

        return $retorno;
    }

	###########################################################
	public function teardownPath($oscars_url, $gri) 
	{
        $repo = $repoDir;
        $retorno = new Array();
		$message = "";

		/**
        $oscarsClient = new OSCARSClient();
		*/
		
		/**
        try 
		{
			$teardownRequest = new TeardownPathContent();
			$teardownResponse = new TeardownPathResponseContent();
			
            $teardownRequest->setGlobalReservationId($gri);
            $teardownResponse = $oscarsClient->teardownPath($teardownRequest);
            
			echo "Global Reservation Id: $teardownResponse->getGlobalReservationId()\n");
            echo "Teardown Status: $teardownResponse->getStatus()\n");

            array_push($retorno, "");
			array_push($retorno, $teardownResponse->getGlobalReservationId());
			array_push($retorno, $teardownResponse->getStatus());
        } 
		catch (RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: Exception ($message)");
        }
		*/
		
        return $retorno;
    }

	###########################################################
	public function refreshPath($oscars_url, $gri) 
	{
        $repo = $repoDir;
        $retorno = new Array();
        $message = "";

		/**
        $oscarsClient = new OSCARSClient();
        */

		/**
        try 
		{
            $refreshRequest = new RefreshPathContent();
			$refreshResponse = new RefreshPathResponseContent ();
			
            $refreshRequest->setGlobalReservationId($gri);
            $refreshResponse = $oscarsClient->refreshPath($refreshRequest);
            
			echo "Global Reservation Id: $refreshResponse->getGlobalReservationId()\n");
            echo "Refresh Status: $refreshResponse->getStatus()\n");
            
			array_push($retorno, "");
			array_push($retorno, $refreshResponse->getGlobalReservationId());
			array_push($retorno, $refreshResponse->getStatus());
        } 
		catch (RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno,"Error: Exception ($message)");
        }
		*/
		
        return $retorno;
    }

	###########################################################
    public function getTopology($oscars_url) 
	{
        $repo = $repoDir;
        $retorno = new Array();
		$message = "";       
        $temp = "";

		/**
        $oscarsClient = new OSCARSClient();
		*/

		/**
        try 
		{
			$request = new GetTopologyContent();
            $response = new GetTopologyResponseContent();
			$domains = new Array();								//CtrlPlaneDomainContent[]
			
            $request->setTopologyType("all");
            $response = $oscarsClient->getNetworkTopology($request);
            
			$domains = $response->getTopology()->getDomain();

            foreach ($domains as $d) 
			{
				$nodes = new Array();							//CtrlPlaneNodeContent[]
				
                $temp = $d->getId();
                array_push($retorno, $temp);
                
				echo "$temp\n";
                
				$nodes = $d.getNode();
                
				foreach ($nodes as $n) 
				{
					$ports = new Array();						//CtrlPlanePortContent[]
					
                    $temp = $n->getId();
                    array_push($retorno, $temp);
                    
					echo "$temp\n";
					
                    $ports = $n->getPort();
                    
					foreach ($ports as $p) 
					{
						$ links = new Array();					//CtrlPlaneLinkContent[]
                        $temp = "$p->getId() $p->getCapacity() $p->getGranularity() $p->getMaximumReservableCapacity() $p->getMaximumReservableCapacity()";
                        array_push($retorno, $temp);
                        
						echo $temp;
                        
 						$links = $p->getLink();
                        
						if ($links != null) 
						{
                            foreach ($links as $l) 
							{
								$swcap = new CtrlPlaneSwcapContent();
								$swcapEsp = new CtrlPlaneSwitchingCapabilitySpecificInfo();
								
                                $swcap = $l->getSwitchingCapabilityDescriptors();
                                $swcapEsp = $swcap->getSwitchingCapabilitySpecificInfo();
                                $temp = "$l->getId() $l->getRemoteLinkId() $l->getCapacity() $l->getGranularity() $l->getMinimumReservableCapacity() $l->getMaximumReservableCapacity() $swcapEsp->getVlanRangeAvailability()";
                                
								array_push($retorno, $temp);
                                echo "$temp\n";
                            }
                        }
                    }
                }
            }
            
			array_unshift($retorno, "");
        } 
		catch (RemoteException $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: RemoteException ($message)");
        } 
		catch (AAAFaultMessage $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno, "Error: AAAFaultMessage ($message)");
        } 
		catch (Exception $e) 
		{
            $e.printStackTrace();
            $message = $e.getMessage();
            array_unshift($retorno,"Error: Exception ($message)");
        }
		*/
		
        return $retorno;
    }
}