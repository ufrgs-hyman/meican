<?php

namespace app\models;

use Yii;

use app\components\DateUtils;

use app\modules\topology\models\NSIParser;
use app\modules\topology\models\NMWGParser;
use app\modules\topology\models\TopologyNotification;

/**
 * This is the model class for table "{{%topo_synchronizer}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $provider_nsa
 * @property integer $cron_id
 * @property string $subscription_id
 * @property string $name
 * @property string $url
 */
class TopologySynchronizer extends \yii\db\ActiveRecord
{
    public $parser;
    public $syncEvent;
    public $detectedChanges = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topo_synchronizer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'auto_apply', 'name', 'url'], 'required'],
            [['provider_nsa'], 'unique', 'message'=> 'Only one Discovery Service is allowed for each NSI Provider. The NSA ID "{value}" has already in use.'],
            [['type'], 'string'],
            [['auto_apply'], 'boolean'],
            [['name', 'subscription_id'], 'string', 'max' => 200],
            [['url', 'provider_nsa'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'auto_apply' => Yii::t('topology', 'Auto Apply Changes'),
            'type' => Yii::t('topology', 'Type'),
            'subscription_id' => Yii::t('topology', 'Subscribe to Updates'),
            'name' => Yii::t('topology', 'Name'),
            'url' => Yii::t('topology', 'URL'),
            'provider_nsa' => Yii::t('topology', 'Provider NSA ID'),
        ];
    }

    public function getEvents() {
        return TopologySyncEvent::find()->where(['sync_id'=> $this->id])->orderBy(['started_at'=> SORT_DESC]);
    }

    public function getLastSyncDate() {
        $event = $this->getEvents()->select(['started_at'])->asArray()->one();
        return $event ? $event['started_at'] : null;
    }

    public function isAutoSyncEnabled() {
        return Cron::existsSyncTask($this->id);
    }

    public function buildChange() {
        if(!$this->detectedChanges) $this->detectedChanges = true;
        $change = new TopologyChange;
        $change->sync_event_id = $this->syncEvent->id;
        $change->status = TopologyChange::STATUS_PENDING;
        return $change;
    }

    public function getType() {
        switch ($this->type) {
            case Service::TYPE_NSI_DS_1_0: return Service::getTypeLabels()[Service::TYPE_NSI_DS_1_0];
            case Service::TYPE_NSI_TD_2_0: return Service::getTypeLabels()[Service::TYPE_NSI_TD_2_0];
            case Service::TYPE_NMWG_TD_1_0: return Service::getTypeLabels()[Service::TYPE_NMWG_TD_1_0];
            case Service::TYPE_PERFSONAR_TS_1_0: return Service::getTypeLabels()[Service::TYPE_PERFSONAR_TS_1_0];
            default: return Yii::t('topology', 'Unknown');
        }
    }

    static function getTypes() {
        return [
            ['id'=> Service::TYPE_NSI_TD_2_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NSI_TD_2_0]],
            ['id'=> Service::TYPE_NSI_DS_1_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NSI_DS_1_0]],
            ['id'=> Service::TYPE_NMWG_TD_1_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NMWG_TD_1_0]],
            ['id'=> Service::TYPE_PERFSONAR_TS_1_0, 'name'=> Service::getTypeLabels()[Service::TYPE_PERFSONAR_TS_1_0]],
        ];
    }

    static function findOneByNsa($nsa) {
        return self::find()->where(['provider_nsa'=>$nsa])->one();
    }

    public function execute() {
        $this->syncEvent = new TopologySyncEvent;
        $this->syncEvent->started_at = DateUtils::now();
        $this->syncEvent->progress = 0;
        $this->syncEvent->sync_id = $this->id;
        $this->syncEvent->status = TopologySyncEvent::STATUS_INPROGRESS;
        $this->syncEvent->save();

        if (!$this->parser) {

            switch ($this->type) {
                case Service::TYPE_NSI_DS_1_0: 
                case Service::TYPE_NSI_TD_2_0: 
                    $this->parser = new NSIParser; 
                    $this->parser->loadFile($this->url);
                    if (!$this->parser->isTD()) {
                        $this->syncEvent->status = TopologySyncEvent::STATUS_FAILED;
                        $this->syncEvent->save();
                        return;
                    }
                    $this->parser->parseTopology();
                    //Yii::trace($topo->getData());
                    break;

                case Service::TYPE_NMWG_TD_1_0: 
                case Service::TYPE_PERFSONAR_TS_1_0: 
                    $this->parser = new NMWGParser;
                    $this->parser->loadFile($this->url);
                    if (!$this->parser->isTD()) {
                        $this->syncEvent->status = TopologySyncEvent::STATUS_FAILED;
                        $this->syncEvent->save();
                        return;
                    }
                    $this->parser->parseTopology();
                    //Yii::trace($topo->getData());
                    break;
            }
        }

        $this->synchronize();

        if ($this->auto_apply) {
            $this->syncEvent->applyChanges();
        }

        if($this->detectedChanges) TopologyNotification::create(1, $this->syncEvent->id);
    }

    //Cria changes a partir de mudanças percebidas
    private function synchronize() {
        foreach ($this->parser->getData()['domains'] as $domainName => $domainData) {
            $domain = Domain::findByName($domainName)->one();

            if ($domain == null) {
                $change = $this->buildChange();
                $change->domain = $domainName;
                $change->item_type = TopologyChange::ITEM_TYPE_DOMAIN;
                $change->data = json_encode([''=>'']);
                $change->type = TopologyChange::TYPE_CREATE;
                $change->save();

                $invalidDevices = false;

            } else {
                //VERIFICA DEVICES
                if ($this->parser instanceof NSIParser) {
                    $invalidDevices = Device::find()->where(['domain_id'=>$domain->id]);
                } else {
                    $invalidDevices = false;
                }
                ///////////
            }
            
            if ($this->parser instanceof NSIParser && isset($domainData['nsa'])) {
                foreach ($domainData['nsa'] as $nsaId => $nsaData) {
                    $provider = Provider::find()->where(['nsa'=>$nsaId])->one();

                    if (!$provider) {
                        $change = $this->buildChange();
                        $change->type = TopologyChange::TYPE_CREATE;
                        $change->domain = $domainName;
                        $change->item_type = TopologyChange::ITEM_TYPE_PROVIDER;
                        $change->data = json_encode(['name'=>$nsaData['name'],'type'=>$nsaData['type'],
                            'lat'=>$nsaData["lat"],
                            'lng'=>$nsaData["lng"],
                            'nsa'=>$nsaId]);

                        $change->save();
                    } elseif ($nsaData['lat'] && $nsaData['lng'] && 
                            (intval($provider->latitude) != intval($nsaData['lat'])) || 
                            (intval($provider->longitude) != intval($nsaData['lng']))) {
                        $change = $this->buildChange();
                        $change->type = TopologyChange::TYPE_UPDATE;
                        $change->item_id = $provider->id;
                        $change->domain = $domainName;
                        $change->item_type = TopologyChange::ITEM_TYPE_PROVIDER;
                        $change->data = json_encode(['name'=>$nsaData['name'],'type'=>$nsaData['type'],
                            'lat'=>$nsaData["lat"],
                            'lng'=>$nsaData["lng"],
                            'nsa'=>$nsaId]);

                        $change->save();
                    }

                    if ($provider) $oldServices = $provider->getServices();
                    $newServices = [];

                    foreach ($nsaData['services'] as $serviceUrl => $serviceType) {
                        $service = Service::findOneByUrl($serviceUrl);
                        if (!$service) {
                            $change = $this->buildChange();
                            $change->type = TopologyChange::TYPE_CREATE;
                            $change->domain = $domainName;
                            $change->item_type = TopologyChange::ITEM_TYPE_SERVICE;
                            $change->data = json_encode([
                                'provName'=>$nsaData['name'],
                                'provNsa'=>$nsaId,
                                'type'=>$serviceType,
                                'url'=>$serviceUrl]);

                            $change->save();
                        } else {
                            $newServices[] = $service->id;
                        }
                    }

                    if($provider) {
                        $oldServices = $oldServices->andWhere(['not in', 'id', $newServices])
                            ->select(['id'])
                            ->asArray()
                            ->all();

                        foreach ($oldServices as $invalidService) {
                            $change = $this->buildChange();
                            $change->type = TopologyChange::TYPE_DELETE;
                            $change->domain = $domainName;
                            $change->item_id = $invalidService['id'];
                            $change->item_type = TopologyChange::ITEM_TYPE_SERVICE;
                            $change->data = json_encode([''=>'']);

                            $change->save();
                        }
                    }
                }
            }
    
            //PERFSONAR
            if ($this->parser instanceof NMWGParser) {
                if (isset($domainData['devices'])) {
                    $this->importDevices($domainData["devices"], $domainName, $invalidDevices);
                }
            //NSI
            } else {
                if (isset($domainData['nets'])) {
                    $this->importNetworks($domainData["nets"], $domainName, $invalidDevices);

                    if ($invalidDevices) {
                        $invalidDevices = $invalidDevices->select(['id','node'])->asArray()->all();

                        foreach ($invalidDevices as $device) {
                            $change = $this->buildChange();
                            $change->type = TopologyChange::TYPE_DELETE;
                            $change->domain = $domainName;
                            $change->item_type = TopologyChange::ITEM_TYPE_DEVICE;
                            $change->item_id = $device['id'];
                            $change->data = json_encode(["node"=>$device['node']]);

                            $change->save();
                        } 
                    }
                }
            }
        }
    }

    private function importNetworks($netsArray, $domainName, $invalidDevices) {
        foreach ($netsArray as $netUrn => $netData) {
            $network = Network::findByUrn($netUrn)->one();
            if (!$network) {
                $change = $this->buildChange();
                $change->type = TopologyChange::TYPE_CREATE;
                $change->domain = $domainName;
                $change->item_type = TopologyChange::ITEM_TYPE_NETWORK;
                $change->data = json_encode(['name'=>$netData['name'],'urn'=>$netUrn,
                    'lat'=>isset($netData["lat"]) ? $netData["lat"] : null,
                    'lng'=>isset($netData["lng"]) ? $netData["lng"] : null]);

                $change->save();

            } elseif(isset($netData["lat"]) && isset($netData["lng"])) {
                if (intval($network->latitude) != intval($netData['lat']) || 
                    intval($network->longitude) != intval($netData['lng'])) {
                    $change = $this->buildChange();
                    $change->type = TopologyChange::TYPE_UPDATE;
                    $change->item_id = $network->id;
                    $change->domain = $domainName;
                    $change->item_type = TopologyChange::ITEM_TYPE_NETWORK;
                    $change->data = json_encode(['name'=>$netData['name'],'urn'=>$netUrn,
                        'lat'=>$netData["lat"],
                        'lng'=>$netData["lng"]]);

                    $change->save();
                }
            }

            if (isset($netData['devices'])) {
                $this->importDevices($netData["devices"], $domainName, $invalidDevices, $netUrn);
            }
        }
    }
    
    private function importDevices($devicesArray, $domainName, $invalidDevices, $netUrn=null) {
        $validDevices = [];

        //VERIFICA PORTAS
        $validBiPorts = [];
        if ($this->parser instanceof NMWGParser) {
            $type = Port::TYPE_NMWG;
            //$invalidBiPorts = Port::find()->where(['type'=>$type]);
            $invalidBiPorts = false;

        } else {
            $type = Port::TYPE_NSI;
            $net = Network::findByUrn($netUrn)->one();
            if ($net) {
                $invalidBiPorts = Port::find()->where(['type'=>$type, 'directionality'=> Port::DIR_BI, 'network_id'=>$net->id]);
            } else {
                $invalidBiPorts = false;
            }
        }

        foreach ($devicesArray as $deviceNode => $deviceData) {
            $device = Device::findOneByDomainAndNode($domainName, $deviceNode);

            if(!$device) {
                $change = $this->buildChange();
                $change->type = TopologyChange::TYPE_CREATE;
                $change->domain = $domainName;
                $change->item_type = TopologyChange::ITEM_TYPE_DEVICE;
                $change->data = json_encode(['node'=>$deviceNode,
                    'lat'=>isset($deviceData["lat"]) ? $deviceData['lat'] : null,
                    'lng'=>isset($deviceData["lng"]) ? $deviceData['lng'] : null,
                    'address'=>isset($deviceData["address"]) ? $deviceData['address'] : null]);
    
                $change->save();

            } else {
                $validDevices[] = $device->id;

                if (isset($deviceData['lat']) && isset($deviceData['lng']) && 
                    (intval($device->latitude) != intval($deviceData['lat']) ||
                        intval($device->longitude) != intval($deviceData['lng']))) {
                    $change = $this->buildChange();
                    $change->type = TopologyChange::TYPE_UPDATE;
                    $change->domain = $domainName;
                    $change->item_type = TopologyChange::ITEM_TYPE_DEVICE;
                    $change->item_id = $device->id;

                    $change->data = json_encode(['node'=>$deviceNode,
                        'lat'=>$deviceData['lat'],
                        'lng'=>$deviceData['lng'],
                        'address'=>$deviceData['address']]);

                    $change->save();
                }
            }

            foreach ($deviceData["biports"] as $urnString => $portData) {
                $port = Port::findByUrn($urnString)->one();

                if(!$port) {
                    $change = $this->buildChange();
                    $change->type = TopologyChange::TYPE_CREATE;
                    $change->domain = $domainName;
                    $change->item_type = TopologyChange::ITEM_TYPE_BIPORT;

                    $change->data = json_encode([
                        'netUrn' => $netUrn,
                        'node'=>$deviceNode,
                        'urn'=>$urnString,
                        'type'=>$type,
                        'name' =>$portData["port"],
                        'cap_max' =>isset($portData["capMax"]) ? $portData["capMax"] : null,
                        'cap_min'=>isset($portData["capMin"]) ? $portData["capMin"] : null,
                        'granu' =>isset($portData["granu"]) ? $portData["granu"] : null,
                        'vlan'=> isset($portData["vlan"]) ? $portData["vlan"] : null,
                    ]);
    
                    $change->save();

                } else {
                    $validBiPorts[] = $port->id; 
                }

                if ($this->parser instanceof NMWGParser) {
                    //PERFSONAR
                    $srcPort = Port::findByUrn($urnString)->one();

                    if (isset($portData['aliasUrn'])) {
                        $dstPort = Port::findByUrn($portData['aliasUrn'])->one();

                        if ((!$dstPort || !$srcPort) || ($srcPort->alias_id != $dstPort->id)) {
                            $change = $this->buildChange();
                            $change->domain = $domainName;
                            $change->type = $srcPort ? $srcPort->alias_id ? TopologyChange::TYPE_UPDATE : TopologyChange::TYPE_CREATE : TopologyChange::TYPE_CREATE;
                            $change->item_type = TopologyChange::ITEM_TYPE_LINK;

                            $change->data = json_encode([
                                'node'=>$deviceNode,
                                'port'=>$portData["port"],
                                'urn'=> $urnString,
                                'dst_urn' =>$portData['aliasUrn'],
                            ]);

                            $change->save();
                        }
                    } elseif($srcPort && $srcPort->alias_id) {
                        $change = $this->buildChange();
                        $change->type = TopologyChange::TYPE_DELETE;
                        $change->domain = $domainName;
                        $change->item_type = TopologyChange::ITEM_TYPE_LINK;
                        $change->item_id = $srcPort->id;
                        $change->data = json_encode([
                            '' =>'',
                        ]);

                        $change->save();
                    }

                } else {
                    foreach ($portData["uniports"] as $uniPortUrn => $uniPortData) {
                        $uniport = Port::findByUrn($uniPortUrn)->one();
                        if (!$uniport) {
                            $change = $this->buildChange();
                            $change->type = TopologyChange::TYPE_CREATE;
                            $change->domain = $domainName;
                            $change->item_type = TopologyChange::ITEM_TYPE_UNIPORT;

                            $change->data = json_encode([
                                'netUrn' => $netUrn,
                                'node'=>$deviceNode,
                                'type'=> Port::TYPE_NSI,
                                'dir' =>  $uniPortData['type'],
                                'urn'=>$uniPortUrn,
                                'biPortUrn' => $urnString,
                                'biPort'=> $portData["port"],
                                'name' =>$uniPortData["port"],
                                'cap_max' =>isset($uniPortData["capMax"]) ? $uniPortData["capMax"] : null,
                                'cap_min'=>isset($uniPortData["capMin"]) ? $uniPortData["capMin"] : null,
                                'granu' =>isset($uniPortData["granu"]) ? $uniPortData["granu"] : null,
                                'vlan'=> isset($uniPortData["vlan"]) ? $uniPortData["vlan"] : null,
                            ]);
            
                            $change->save();
                        }

                        if (isset($uniPortData['aliasUrn'])) {
                            $dstPort = Port::findByUrn($uniPortData['aliasUrn'])->one();
                            if ((!$dstPort || !$uniport) || ($uniport->alias_id != $dstPort->id)) {
                                $change = $this->buildChange();
                                $change->type = $uniport ? $uniport->alias_id ? TopologyChange::TYPE_UPDATE : TopologyChange::TYPE_CREATE : TopologyChange::TYPE_CREATE;
                                $change->domain = $domainName;
                                $change->item_type = TopologyChange::ITEM_TYPE_LINK;

                                $change->data = json_encode([
                                    'node'=>$deviceNode,
                                    'port'=>$uniPortData["port"],
                                    'urn'=> $uniPortUrn,
                                    'dst_urn' =>$uniPortData['aliasUrn'],
                                ]);

                                $change->save();
                            }
                        } elseif($uniport && $uniport->alias_id) {
                            $change = $this->buildChange();
                            $change->type = TopologyChange::TYPE_DELETE;
                            $change->domain = $domainName;
                            $change->item_type = TopologyChange::ITEM_TYPE_LINK;
                            $change->item_id = $uniport->id;
                            $change->data = json_encode([
                                '' =>'',
                            ]);

                            $change->save();
                        }
                    }
                }
            }
        }

        if ($invalidBiPorts) {
            $invalidBiPorts = $invalidBiPorts->andWhere(['not in', 'id', $validBiPorts])->all();

            foreach ($invalidBiPorts as $port) {
                $change = $this->buildChange();
                $change->type = TopologyChange::TYPE_DELETE;
                $change->domain = $domainName;
                $change->item_type = TopologyChange::ITEM_TYPE_BIPORT;
                $change->item_id = $port->id;
                $change->data = json_encode(["node"=>$port]);

                $change->save();
            } 
        }

        if ($invalidDevices) {
            $invalidDevices->andWhere(['not in', 'id', $validDevices]);
        }
    }
}
