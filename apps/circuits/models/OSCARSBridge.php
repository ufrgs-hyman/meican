<?php

/**
* Driver class to operate with OSCARS v0.5.3 and v0.5.4 ONLY!!
* - For OSCARS v0.6 functionality, refer to OSCARSDriver06 class.
*
* Receives calls from MEICAN and packages appropriate reservation query parameters and triggers a SOAP call to 
* OSCARSBridge.java to invoke the corresponding client methods.
**/
class OSCARSBridge
{
	private $repoDir = "repo";
	
    public function createReservation($oscars_url, $description, $srcUrn, $isSrcTagged, $srcTag, $destUrn, $isDestTagged, 		
									  $destTag, $path, $bandwidth, $pathSetupMode, $startTimestamp, $endTimestamp) 
	{
        $repo = $repoDir;
        
		$retorno = Array(); 
        $message = "";

        /**
        * pathSetupMode (string)
        * "timer-automatic" means that the reserved circuit will be instantiated by the scheduler process
        * "signal-xml" means the user will signal to instantiate the reserved circuit
        * accepts any string in the path setup mode
		**/
		
		##################################################################################################
		# Stuff in Red is incomplete: Don't have requisite classes to implement in PHP  
		#  - However, all syntax has been modified from Java to PHP so when the classes are made, it should work (except 		
		#    for method calls).
		##################################################################################################
/**
        $oscarsClient = new OSCARSClient();
*/
        /**
         * ResCreateContent
         *      PathInfo
         *          layer2Info
         **/



/**

        $layer2Info = new Layer2Info();
        $layer2Info.setSrcEndpoint($srcUrn);
        $layer2Info.setDestEndpoint($destUrn);

        $pathInfo = new PathInfo();

        $pathInfo.setPathSetupMode($pathSetupMode);
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
            $pathContent.setId("userPath");

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
                    $hop.setId($i . "");
                    
					if ($hopType == 4) 
					{
                        $hop.setDomainIdRef($hopId);
                    } 
					else if ($hopType == 5) 
					{
                        $hop.setNodeIdRef($hopId);
                    } 
					else if ($hopType == 6) 
					{
                        $hop.setPortIdRef($hopId);
                    } 
					else 
					{
                        $hop.setLinkIdRef($hopId);
                    }

                    $pathContent.addHop($hop);
*/   
             	}
            }
            if ($hasEro) 
			{
/**
                $pathInfo.setPath($pathContent);
*/                //pathInfo.setPathType("0");
            }
        }
/**
        $request = new ResCreateContent();
        $request.setBandwidth($bandwidth);
        $request.setStartTime($startTimestamp);
        $request.setEndTime($endTimestamp);
        $request.setDescription($description);
        $request.setPathInfo($pathInfo);

        $srcVtag = new VlanTag();

        if ($isSrcTagged == "true") 
		{
            $srcVtag.setTagged(true);
            $srcVtag.setString($srcTag);
        } 
		else 
		{
            $srcVtag.setTagged(false);
            $srcVtag.setString("0");
        }

        $layer2Info.setSrcVtag($srcVtag);

        $destVtag = new VlanTag();

        if ($isDestTagged == "true") 
		{
            $destVtag.setTagged(true);
            $destVtag.setString($destTag);
        } 
		else 
		{
            $destVtag.setTagged(false);
            $destVtag.setString("0");
        }
        $layer2Info.setDestVtag(destVtag);

        $pathInfo.setLayer2Info($layer2Info);
*/


        try 
		{
/**
            $response = $oscarsClient.createReservation($request);  #CreateReply response
            $gri = $response.getGlobalReservationId();
            $status = $response.getStatus();

            echo "GRI: $gri";
            echo "Initial Status: $status";

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
}