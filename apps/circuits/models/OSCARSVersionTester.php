<?php

include_once 'apps/circuits/models/OSCARSDriver05.php';
include_once 'apps/circuits/models/OSCARSDriver06.php';

/**
* - Simple class used to elevate the level of OSCARS version inspection. This class has one method which returns an object of type OSCARSDriver05 or 
*   OSCARSDriver06 as appropriate to the OSCARS version in use. 
* - This functionality was originally encapsulated as part of the OSCARSReservation class, but since that class has been decomposed into the Driver 
*   classes, the version test has to come before instantiating a concrete driver.
**/
class OSCARSVersionTester
{
	private $version;
	
	/**
	* Constructor
	**/
    function OSCARSVersionTester($vers) 
    {
        $this->version = $vers;
    }
    
    // Returns an instance of the appropriate Driver for this version of OSCARS, false if current version is not supported.
    public function checkVersion() 
    {
        switch ($this->version) 
        {
        	case "0.5.3":
	        case "0.5.4":
	           return new OSCARSDriver05();
	        case "0.6":
			   return new OSCARSDriver06();
			default:
                return false;
        }
    }
}
