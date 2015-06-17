<?php

namespace app\models;

use Yii;

use app\components\AggregatorSoapClient;

/**
 * This is the model class for table "{{%provider}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $nsa
 * @property string $connection_url
 * @property string $discovery_url
 *
 * @property Aggregator $aggregator
 * @property Domain $domain
 * @property Reservation[] $reservations
 */
class Provider extends \yii\db\ActiveRecord
{
	const TYPE_DUMMY 		= "DUMMY";
	const TYPE_BRIDGE 		= "BRIDGE";
	const TYPE_AGGREGATOR 	= "AGGREGATOR";
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%provider}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string'],
            [['nsa'], 'string', 'max' => 200],
            [['connection_url', 'discovery_url'], 'string', 'max' => 300],
            [['nsa'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'nsa' => 'NSA ID',
            'connection_url' => 'Connection Service URL',
            'discovery_url' => 'Discovery Service URL',
        ];
    }
    
    public function isDummy() {
    	return Yii::$app->params["provider.force.dummy"] || ($this->type == self::TYPE_DUMMY);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservations()
    {
        return $this->hasMany(Reservation::className(), ['provider_id' => 'id']);
    }
    
    public function requestCreate($conn) {
    	if ($this->isDummy()) {
    		sleep(4);
    		$date = new \DateTime();
    		$conn->external_id = $date->format('YmdHis');
    		$conn->confirmCreate();
    		$conn->confirmCreatePath();
    		return;
    	} 
    	
    	$reserve = $conn->getReservation()->one();
    	
    	$directionality = "Bidirectional";
    	$symmetricPath = "true";
    	$parameter = "PROTECTED";
    	$serviceType = "http://services.ogf.org/nsi/2013/12/descriptions/EVTS.A-GOLE";
    	$firstPath = $reserve->getFirstPath()->one();
    	$lastPath = $reserve->getLastPath()->one();
    	$sourceSTP = $firstPath->getUrnValue()."?vlan=".$firstPath->vlan;
    	$destSTP = $lastPath->getUrnValue()."?vlan=".$lastPath->vlan;
    	
    	$date = new \DateTime($conn->start);
    	$start = $date->format('Y-m-d\TH:i:s.000-00:00');
    	
    	$date = new \DateTime($conn->finish);
    	$finish = $date->format('Y-m-d\TH:i:s.000-00:00');
    	
    	/** Creating the SOAP request **/
    	$schedule = array(
    			"startTime" => $start, //start_time
    			"endTime" => $finish
    	);
    	
    	$paths = $reserve->getPaths()->all();
    	$pathSize = count($paths);
    	$waypoints = new \ArrayObject();
    	
    		if($pathSize > 2) {
    			for ($i = 1; $i < ($pathSize - 1); $i++) {
    				$stp = $paths[$i]->getUrnValue()."?vlan=".$paths[$i]->vlan;
    				$stp = new \SoapVar(['stp'=>$stp], SOAP_ENC_OBJECT, NULL, NULL, null, NULL);
    				$orderedSTP = new \SoapVar($stp, SOAP_ENC_OBJECT, NULL, NULL, 'orderedSTP', NULL);
    				$waypoints->append($orderedSTP);
    			}
    			
    			$ero = new \SoapVar($waypoints, SOAP_ENC_OBJECT, NULL, NULL, "ero", NULL);
    				
    			$p2ps = array(
    					"capacity" => $reserve->bandwidth, //Bandwidth
    					"directionality" => $directionality, //Auto
    					"symmetricPath" => $symmetricPath, //Auto
    					"sourceSTP" => $sourceSTP,  //src_urn
    					"destSTP" => $destSTP, //dest_urn
    					"parameter" => $parameter, //Auto
    					$ero
    			);
    		} else {
    			$p2ps = array(
    					"capacity" => $reserve->bandwidth, //Bandwidth
    					"directionality" => $directionality, //Auto
    					"symmetricPath" => $symmetricPath, //Auto
    					"sourceSTP" => $sourceSTP,  //src_urn
    					"destSTP" => $destSTP, //dest_urn
    					"parameter" => $parameter, //Auto
    			);
    		}
    	
    		$schedule = new \SoapVar($schedule, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
    		$p2ps = new \SoapVar($p2ps, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
    	
    		$criteria = array(
    				"schedule" => $schedule,
    				"serviceType" => $serviceType,
    				"p2ps" => $p2ps
    		);
    	
    		$criteria = new \SoapVar($criteria, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
    	
    		/** Tirando os espaços do Global Reservation ID, pois não é permitido **/
    		$globalReservationId = str_replace(" ", "", $reserve->name);
    	
    		$params = array(
    				"globalReservationId" => $globalReservationId, //Reservation name
    				"description" => $reserve->name,
    				"criteria" => $criteria
    		);
    	
    		try{
    			$client = new AggregatorSoapClient($this->nsa, $this->connection_url);
    		}catch(\SoapFault $error){
    			Yii::trace($error);
    			return false;
    		}
    		
    		$client->setAggHeader();
    		
    		try{
    			$client->reserve($params);
    		}catch(\SoapFault $error){
    			Yii::trace($error);
    			return false;
    		}
    	
    		$dom = new \DOMDocument('1.0', 'UTF-8');
    		$dom->preserveWhiteSpace = false;
    		$response = $client->__getLastResponse();
    		Yii::trace($response);
    		$dom->loadXML($response);
    	
    		$connectionId = $dom->getElementsByTagName("connectionId")->item(0);
    		$connectionId = $connectionId->textContent;
    	
    		if(isset($connectionId)){
    			$conn->external_id = $connectionId;
    			$conn->confirmCreate();
    				
    			/** Connectivity Log **/
    			$log = "Sent\n".
    					"Connection Id: ".$connectionId."\n".
    					"Action: reserve\n".
    					"DateTime: ".date(DATE_RFC822)."\n\n";
    	
    			Yii::trace($log);
    				
    		}
    		else {
    			$conn->failedCreate();
    			Yii::trace("Failed");
    			return FALSE;
    		}
    		return TRUE;
    }
    
    public function requestCancel($conn) {
    	if ($this->isDummy()) {
    		$conn->confirmCancel();
    		return;
    	}
    	
    		$params = array(
    				"connectionId" => $conn->external_id
    		);
    		 
    		$client = new AggregatorSoapClient($this->nsa, $this->connection_url);
    		$client->setAggHeader();
    		
    		try{
    			$client->terminate($params);
    			Yii::trace($client->__getLastResponse());
    		}catch(SoapFault $error){
    			return false;
    		}
    }
    
    public function requestReadPath($conn) {
    	if ($this->isDummy()) {
    		$resPaths = $conn->getReservation()->one()->getPaths()->all();
    		$i = 0;
    		foreach ($resPaths as $resPath) {
    			$connPath = new ConnectionPath;
    			$connPath->path_order = $i;
    			$connPath->conn_id = $conn->id;
    			$connPath->domain = $resPath->domain;
    			$i++;
    			$connPath->setSourceUrn($resPath->getUrnValue());
    			$connPath->src_vlan = $resPath->vlan;
    			$connPath->setDestinationUrn($resPath->getUrnValue());
    			$connPath->dst_vlan = $resPath->vlan;
    			
    			$connPath->save();
    		}
    		$conn->confirmReadPath();
    		return;
    	}
    	
    	$params = array(
    			"connectionId" => $conn->external_id
    	);
    	
    	$client = new AggregatorSoapClient($this->nsa, $this->connection_url);
    	$client->setAggHeader();
    	$client->querySummary($params);
    	
    		/** Connectivity Log **/
    		$log = "Sent\n".
    				"Connection Id: ".$conn->external_id."\n".
    				"Action: querySummary\n".
    				"DateTime: ".date(DATE_RFC822)."\n\n";
    			
    		Yii::trace($log);
    	
    }
    
    public function requestCommit($conn) {
    	if ($this->isDummy()) {
    		$conn->confirmCommit();
    		return;
    	}
    	
    	$params = array(
    			"connectionId" => $conn->external_id
    	);
    	
    	$client = new AggregatorSoapClient($this->nsa, $this->connection_url);
    	$client->setAggHeader();
    	$client->reserveCommit($params);
    	
    		/** Connectivity Log **/
    		$log = "Sent\n".
    				"Connection Id: ".$conn->external_id."\n".
    				"Action: reserveCommit\n".
    				"DateTime: ".date(DATE_RFC822)."\n\n";
    	
    		Yii::trace($log);
    }
    
    public function requestProvision($conn) {
    	if ($this->isDummy()) {
    		$conn->confirmProvision();
    		return;
    	}
    	
    	$params = array(
    			"connectionId" => $conn->external_id
    	);
    	 
    	$client = new AggregatorSoapClient($this->nsa, $this->connection_url);
    	$client->setAggHeader();
    	$client->provision($params);
    	
    		/** Connectivity Log **/
    		$log = "Sent\n".
    				"Connection Id: ".$conn->external_id."\n".
    				"Action: provision\n".
    				"DateTime: ".date(DATE_RFC822)."\n\n";
    		 
    		Yii::trace($log);
    }
}
