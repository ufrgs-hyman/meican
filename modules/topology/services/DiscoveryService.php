<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\services;

use Yii;

use meican\base\utils\DateUtils;
use meican\nsi\NSIParser;
use meican\nmwg\NMWGParser;
use meican\topology\models\TopologyNotification;
use meican\topology\models\DiscoveryTask;
use meican\topology\models\DiscoveryRule;
use meican\topology\models\Domain;
use meican\topology\models\Network;
use meican\topology\models\Port;
use meican\topology\models\Provider;
use meican\topology\models\Service;
use meican\topology\models\Peering;
use meican\topology\models\Change;

/**
 * This is the MEICAN Network Topology Discovery Service.
 *
 * Based on a Discovery Rule, this object query the network topology provider and
 * get the network topology description, generally a XML file. 
 * After that step, this service compare the current MEICAN topology and the
 * recently downloaded network topology. As result, changes are discovered and
 * applied on local MEICAN topology. Go to Change class for more information about
 * apply methods.
 *
 * @author Maurício Quatrin Guerreiro
 */
class DiscoveryService {
    
    public $parser;
    public $task;
    public $detectedChanges = false;

    private function buildChange() {
        if(!$this->detectedChanges) $this->detectedChanges = true;
        $change = new Change;
        $change->sync_event_id = $this->task->id;
        $change->status = Change::STATUS_PENDING;
        return $change;
    }

    private function taskFailed() {
        $this->task = DiscoveryTask::STATUS_FAILED;
        $this->task->save();
    }

    public function execute($task, $rule) {
        $this->task = $task;
        $this->task->started_at = DateUtils::now();
        $this->task->progress = 0;
        $this->task->sync_id = $rule->id;
        $this->task->status = DiscoveryTask::STATUS_INPROGRESS;
        $this->task->save();

        if (!$this->parser) {

            switch ($rule->type) {
                case DiscoveryRule::DESC_TYPE_NSI: 
                    $this->parser = new NSIParser; 
                    if(!$this->parser->loadFile($rule->url) || !$this->parser->isTD()) 
                        return $this->taskFailed();

                    $this->parser->parseTopology();
                    //Yii::trace($this->parser->getData());
                    break;

                case DiscoveryRule::DESC_TYPE_NMWG: 
                    $this->parser = new NMWGParser;
                    if(!$this->parser->loadFile($rule->url) || !$this->parser->isTD()) 
                        return $this->taskFailed();

                    $this->parser->parseTopology();
                    Yii::trace($this->parser->getData());
                    break;
            }
        }

        $this->search();
        $this->task->status = DiscoveryTask::STATUS_SUCCESS;
        $this->task->save();

        if ($rule->auto_apply) {
            $this->task->applyChanges();
        }

        //if($this->detectedChanges) TopologyNotification::create(1, $this->task->id);
    }

    //Cria changes a partir de mudanças percebidas
    private function search() {
        foreach ($this->parser->getData()['domains'] as $domainName => $domainData) {
            $domain = Domain::findByName($domainName)->one();

            if ($domain == null) {
                $change = $this->buildChange();
                $change->domain = $domainName;
                $change->item_type = Change::ITEM_TYPE_DOMAIN;
                $change->data = json_encode([''=>'']);
                $change->type = Change::TYPE_CREATE;
                $change->save();
            } 
            
            if ($this->parser instanceof NSIParser && isset($domainData['nsa'])) {
                foreach ($domainData['nsa'] as $nsaId => $nsaData) {
                    $provider = Provider::find()->where(['nsa'=>$nsaId])->one();

                    if (!$provider) {
                        $change = $this->buildChange();
                        $change->type = Change::TYPE_CREATE;
                        $change->domain = $domainName;
                        $change->item_type = Change::ITEM_TYPE_PROVIDER;
                        $change->data = json_encode(['name'=>$nsaData['name'],'type'=>$nsaData['type'],
                            'lat'=>$nsaData["lat"],
                            'lng'=>$nsaData["lng"],
                            'nsa'=>$nsaId]);

                        $change->save();
                    } elseif ($nsaData['lat'] && $nsaData['lng'] && 
                            (intval($provider->latitude) != intval($nsaData['lat'])) || 
                            (intval($provider->longitude) != intval($nsaData['lng']))) {
                        $change = $this->buildChange();
                        $change->type = Change::TYPE_UPDATE;
                        $change->item_id = $provider->id;
                        $change->domain = $domainName;
                        $change->item_type = Change::ITEM_TYPE_PROVIDER;
                        $change->data = json_encode(['name'=>$nsaData['name'],'type'=>$nsaData['type'],
                            'lat'=>$nsaData["lat"],
                            'lng'=>$nsaData["lng"],
                            'nsa'=>$nsaId]);

                        $change->save();
                    }                    

                    if ($provider) {
                        $oldPeerings = $provider->getPeerings();
                        $oldServices = $provider->getServices();
                    }
                    $newPeerings = [];
                    $newServices = [];

                    if(isset($nsaData['peerings'])) {
                        foreach ($nsaData['peerings'] as $dstNsaId) {
                            if ($provider) {
                                $dstProv = Provider::findOneByNsa($dstNsaId);
                                if ($dstProv) {
                                    $peering = Peering::findOne(['src_id'=> $provider->id, 'dst_id'=>$dstProv->id]);
                                    if ($peering) {
                                        //$newPeerings[] = $peering->id;
                                        continue;
                                    }
                                }
                            } 

                            $change = $this->buildChange();
                            $change->type = Change::TYPE_CREATE;
                            $change->domain = $domainName;
                            $change->item_type = Change::ITEM_TYPE_PEERING;
                            $change->data = json_encode([
                                'srcNsaId'=>$nsaId,
                                'dstNsaId'=>$dstNsaId]);

                            $change->save();
                        }
                    }

                    foreach ($nsaData['services'] as $serviceUrl => $serviceType) {
                        $service = Service::findOneByUrl($serviceUrl);
                        if (!$service) {
                            $change = $this->buildChange();
                            $change->type = Change::TYPE_CREATE;
                            $change->domain = $domainName;
                            $change->item_type = Change::ITEM_TYPE_SERVICE;
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
                            $change->type = Change::TYPE_DELETE;
                            $change->domain = $domainName;
                            $change->item_id = $invalidService['id'];
                            $change->item_type = Change::ITEM_TYPE_SERVICE;
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
                    $this->importNetworks($domainData["nets"], $domainName);
                }
            }
        }
    }

    private function importNetworks($netsArray, $domainName) {
        foreach ($netsArray as $netUrn => $netData) {
            $network = Network::findByUrn($netUrn)->one();
            if (!$network) {
                $change = $this->buildChange();
                $change->type = Change::TYPE_CREATE;
                $change->domain = $domainName;
                $change->item_type = Change::ITEM_TYPE_NETWORK;
                $change->data = json_encode([
                    'name'=>$netData['name'],
                    'urn'=>$netUrn,
                    'version'=>$netData['version'],
                    'lat'=>isset($netData["lat"]) ? $netData["lat"] : null,
                    'lng'=>isset($netData["lng"]) ? $netData["lng"] : null]);

                $change->save();

            } elseif(isset($netData["lat"]) && isset($netData["lng"])) {
                if (intval($network->latitude) != intval($netData['lat']) || 
                    intval($network->longitude) != intval($netData['lng'])) {
                    $change = $this->buildChange();
                    $change->type = Change::TYPE_UPDATE;
                    $change->item_id = $network->id;
                    $change->domain = $domainName;
                    $change->item_type = Change::ITEM_TYPE_NETWORK;
                    $change->data = json_encode([
                        'name'=>$netData['name'],
                        'urn'=>$netUrn,
                        'version'=>$netData['version'],
                        'lat'=>$netData["lat"],
                        'lng'=>$netData["lng"]]);

                    $change->save();
                }
            }

            if (isset($netData['biports'])) {
                $this->searchPorts($netData["biports"], $domainName, $netUrn);
            }
        }
    }
    
    private function searchPorts($biports, $domainName, $netUrn=null) {
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

        foreach ($biports as $urnString => $portData) {
            $port = Port::findByUrn($urnString)->one();

            if(!$port) {
                $change = $this->buildChange();
                $change->type = Change::TYPE_CREATE;
                $change->domain = $domainName;
                $change->item_type = Change::ITEM_TYPE_BIPORT;

                $change->data = json_encode([
                    'netUrn' => $netUrn,
                    'urn'=>$urnString,
                    'type'=>$type,
                    'name' =>$portData["name"],
                    'lat'=>isset($portData["lat"]) ? $portData["lat"] : null,
                    'lng'=>isset($portData["lng"]) ? $portData["lng"] : null,
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
                        $change->type = $srcPort ? $srcPort->alias_id ? Change::TYPE_UPDATE : Change::TYPE_CREATE : Change::TYPE_CREATE;
                        $change->item_type = Change::ITEM_TYPE_LINK;

                        $change->data = json_encode([
                            'urn'=> $urnString,
                            'dst_urn' =>$portData['aliasUrn'],
                        ]);

                        $change->save();
                    }
                } elseif($srcPort && $srcPort->alias_id) {
                    $change = $this->buildChange();
                    $change->type = Change::TYPE_DELETE;
                    $change->domain = $domainName;
                    $change->item_type = Change::ITEM_TYPE_LINK;
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
                        $change->type = Change::TYPE_CREATE;
                        $change->domain = $domainName;
                        $change->item_type = Change::ITEM_TYPE_UNIPORT;

                        $change->data = json_encode([
                            'netUrn' => $netUrn,
                            'type'=> Port::TYPE_NSI,
                            'dir' =>  $uniPortData['type'],
                            'urn'=>$uniPortUrn,
                            'biPortUrn' => $urnString,
                            'biPort'=> $portData["name"],
                            'name' =>$uniPortData["name"],
                            'cap_max' =>isset($uniPortData["capMax"]) ? $uniPortData["capMax"] : null,
                            'cap_min'=>isset($uniPortData["capMin"]) ? $uniPortData["capMin"] : null,
                            'capacity'=>isset($uniPortData["capacity"]) ? $uniPortData["capacity"] : null,
                            'granu' =>isset($uniPortData["granu"]) ? $uniPortData["granu"] : null,
                            'vlan'=> isset($uniPortData["vlan"]) ? $uniPortData["vlan"] : null,
                        ]);
        
                        $change->save();
                    } else {
                        //update port
                        $uptatedProperties = [
                            'capMax' => [
                                'key' =>'cap_max',
                                'value' => $uniport->max_capacity
                            ],
                            'capMin' => [
                                'key' =>'cap_min',
                                'value' => $uniport->min_capacity
                            ],
                            'granu' => [
                                'key' => 'granu',
                                'value' => $uniport->granularity
                            ],
                            'vlan' => [
                                'key' => 'vlan',
                                'value' => $uniport->vlan_range
                            ]
                        ];

                        $dataChange = [];
                        foreach($uptatedProperties as $key => $property)   {
                            if(isset($uniPortData[$key]) && $uniPortData[$key] != $property['value'])    {
                                $dataChange[$property['key']] = (isset($uniPortData[$key])) ? $uniPortData[$key] : null;
                            }
                        }

                        if(!empty($dataChange)) {
                            $change = $this->buildChange();
                            $change->type = Change::TYPE_UPDATE;
                            $change->domain = $domainName;
                            $change->item_type = Change::ITEM_TYPE_UNIPORT;
                            $change->item_id = $uniport->id;

                            if(!isset($dataChange['vlan']))  {
                                $dataChange['vlan'] = $uniport->vlan_range;
                            }

                            $change->data = json_encode($dataChange);
                            $change->save();
                        }
                    }

                    if (isset($uniPortData['aliasUrn'])) {
                        $dstPort = Port::findByUrn($uniPortData['aliasUrn'])->one();
                        if ((!$dstPort || !$uniport) || ($uniport->alias_id != $dstPort->id)) {
                            $change = $this->buildChange();
                            $change->type = $uniport ? $uniport->alias_id ? Change::TYPE_UPDATE : Change::TYPE_CREATE : Change::TYPE_CREATE;
                            $change->domain = $domainName;
                            $change->item_type = Change::ITEM_TYPE_LINK;

                            $change->data = json_encode([
                                'urn'=> $uniPortUrn,
                                'dst_urn' =>$uniPortData['aliasUrn'],
                            ]);

                            $change->save();
                        }

                    } elseif($uniport && $uniport->alias_id) {
                        $change = $this->buildChange();
                        $change->type = Change::TYPE_DELETE;
                        $change->domain = $domainName;
                        $change->item_type = Change::ITEM_TYPE_LINK;
                        $change->item_id = $uniport->id;
                        $change->data = json_encode([
                            '' =>'',
                        ]);

                        $change->save();
                    }
                }
            }
        }

        if ($invalidBiPorts) {
            $invalidBiPorts = $invalidBiPorts->andWhere(['not in', 'id', $validBiPorts])->all();

            foreach ($invalidBiPorts as $port) {
                $change = $this->buildChange();
                $change->type = Change::TYPE_DELETE;
                $change->domain = $domainName;
                $change->item_type = Change::ITEM_TYPE_BIPORT;
                $change->item_id = $port->id;
                $change->data = json_encode(["node"=>$port]);

                $change->save();
            } 
        }
    }
}
