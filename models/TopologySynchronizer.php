<?php

namespace app\models;

use Yii;

use app\components\DateUtils;

use app\modules\topology\models\NSIParser;
use app\modules\topology\models\NMWGParser;

/**
 * This is the model class for table "{{%topo_synchronizer}}".
 *
 * @property integer $id
 * @property string $sync_date
 * @property string $type
 * @property integer $enabled
 * @property string $name
 * @property string $url
 */
class TopologySynchronizer extends \yii\db\ActiveRecord
{
    public $parser;
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
            [['type', 'enabled', 'auto_apply', 'name', 'url'], 'required'],
            [['url'], 'unique'],
            [['sync_date'], 'safe'],
            [['type'], 'string'],
            [['auto_apply' ,'enabled'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['url'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'sync_date' => Yii::t('topology', 'Last Sync'),
            'auto_apply' => Yii::t('topology', 'Auto Apply Changes'),
            'type' => Yii::t('topology', 'Type'),
            'enabled' => Yii::t('topology', 'Auto Sync'),
            'name' => Yii::t('topology', 'Name'),
            'url' => Yii::t('topology', 'URL'),
        ];
    }

    public function buildChange() {
        $change = new TopologyChange;
        $change->sync_id = $this->id;
        $change->status = TopologyChange::STATUS_PENDING;
        return $change;
    }

    public function deletePendingChanges() {
        return TopologyChange::deleteAll(['status'=> TopologyChange::STATUS_PENDING, 'sync_id'=>$this->id]);
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
            ['id'=> Service::TYPE_NSI_DS_1_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NSI_DS_1_0]],
            ['id'=> Service::TYPE_NSI_TD_2_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NSI_TD_2_0]],
            ['id'=> Service::TYPE_NMWG_TD_1_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NMWG_TD_1_0]],
            ['id'=> Service::TYPE_PERFSONAR_TS_1_0, 'name'=> Service::getTypeLabels()[Service::TYPE_PERFSONAR_TS_1_0]],
        ];
    }

    static function findOneByNotification($xml) {
        $parser = new NSIParser; 
        $parser->loadXml($xml);
        $sync = TopologySynchronizer::findOne(1);
        $sync->parser = $parser;
        return $sync;
    }

    public function execute() {
        $this->sync_date = DateUtils::now();
        $this->save();

        if (!$this->parser) {

            switch ($this->type) {
                case Service::TYPE_NSI_DS_1_0: 
                case Service::TYPE_NSI_TD_2_0: 
                    $this->parser = new NSIParser; 
                    $this->parser->loadFile($this->url);
                    //Yii::trace($topo->getData());
                    break;

                case Service::TYPE_NMWG_TD_1_0: 
                case Service::TYPE_PERFSONAR_TS_1_0: 
                    $this->parser = new NMWGParser($this->url);
                    $this->parser->loadFile();
                    //Yii::trace($topo->getData());
                    break;
            }
        }

        $this->deletePendingChanges();
        $this->synchronize();

        if ($this->auto_apply) {
            $this->applyChanges();
        }
    }

    public function applyChanges() {
        //NECESSARIO POR UM BUG NO CONTROLE DE MEMORIA DO YII
        //ELE NAO LIBERA A MEMORIA USADA NO LOG DE CADA APPLYCHANGE E ACABA EM FATAL ERROR
        $log = Yii::$app->log;
        foreach ($log->targets as $logTarget) { 
            $logTarget->enabled = false;
        }
        
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_DOMAIN);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_PROVIDER);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_SERVICE);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_NETWORK);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_DEVICE);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_BIPORT);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_UNIPORT);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_LINK);

        $log = Yii::$app->log;
        foreach ($log->targets as $logTarget) { 
            $logTarget->enabled = true;
        }
    }

    private function applyChangesByType($type) {
        $changes = TopologyChange::find()->where(['sync_id'=>$this->id, 'status'=>TopologyChange::STATUS_PENDING,'item_type'=>$type])->all();
        foreach ($changes as $change) {
            $change->apply();
        }
    }

    //Cria changes a partir de mudanças percebidas pelo Sync
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
            } else {
                //comparar?
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

                    foreach ($nsaData['services'] as $serviceUrl => $serviceType) {
                        if ($provider) {
                            $service = $provider->getServices()->andWhere(['url'=>$serviceUrl])->one();
                            if ($service) continue;
                        }

                        $change = $this->buildChange();
                        $change->type = TopologyChange::TYPE_CREATE;
                        $change->domain = $domainName;
                        $change->item_type = TopologyChange::ITEM_TYPE_SERVICE;
                        $change->data = json_encode(['provName'=>$nsaData['name'],'provNsa'=>$nsaId,
                            'type'=>$serviceType,
                            'url'=>$serviceUrl]);

                        $change->save();
                    }
                }
            }
    
            //PERFSONAR
            if ($this->parser instanceof NMWGParser) {
                if (isset($domainData['devices'])) {
                    $this->importDevices($domainData["devices"], $domainName);
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
                $this->importDevices($netData["devices"], $domainName, $netUrn);
            }
        }
    }
    
    private function importDevices($devicesArray, $domainName, $netUrn=null) {
        $validBiPorts = [];
        if ($this->parser instanceof NMWGParser) {
                $type = Port::TYPE_NMWG;
                $invalidBiPorts = Port::find()->where(['type'=>$type]);
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
                    if (isset($portData['aliasUrn'])) {
                        $srcPort = Port::findByUrn($urnString)->one();
                        $dstPort = Port::findByUrn($portData['aliasUrn'])->one();
                        if ((!$dstPort || !$srcPort) || ($dstPort && $srcPort && $srcPort->alias_id != $dstPort->id)) {
                            $change = $this->buildChange();
                            $change->domain = $domainName;
                            $change->type = TopologyChange::TYPE_CREATE;
                            $change->item_type = TopologyChange::ITEM_TYPE_LINK;

                            $aliasUrn = explode(":", $portData['aliasUrn']);
                            $dst_domain = explode("=", $aliasUrn[0])[1];

                            $change->data = json_encode([
                                'node'=>$deviceNode,
                                'port'=>$portData["port"],
                                'urn'=> $urnString,
                                'dst_dom' => $dst_domain,
                                'dst_urn' =>$portData['aliasUrn'],
                            ]);

                            $change->save();
                        }
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

                                $dstUrn = explode(":", $uniPortData['aliasUrn']);
                                //         0   1     2         3        4    5
                                //        urn:ogf:network:cipo.rnp.br:2014::POA

                                //$dstDom = $dstUrn[3];

                                $change->data = json_encode([
                                    'node'=>$deviceNode,
                                    'port'=>$uniPortData["port"],
                                    'urn'=> $uniPortUrn,
                                    //'dst_dom' => $dstDom,
                                    'dst_urn' =>$uniPortData['aliasUrn'],
                                ]);

                                $change->save();
                            }
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
    }
}
