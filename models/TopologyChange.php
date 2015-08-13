<?php

namespace app\models;

use Yii;

use yii\data\ActiveDataProvider;
use app\components\DateUtils;

/**
 * This is the model class for table "{{%topo_change}}".
 *
 * @property integer $id
 * @property integer $sync_event_id
 * @property string $domain
 * @property string $status
 * @property string $type
 * @property string $item_type
 * @property integer $item_id
 * @property string $data
 * @property string $applied_at
 * @property string $error
 *
 * @property TopoSynchronizer $sync
 */
class TopologyChange extends \yii\db\ActiveRecord
{
    const ITEM_TYPE_DOMAIN = 'DOMAIN';
    const ITEM_TYPE_PROVIDER = 'PROVIDER';
    const ITEM_TYPE_SERVICE = 'SERVICE';
    const ITEM_TYPE_NETWORK = 'NETWORK';
    const ITEM_TYPE_DEVICE = 'DEVICE';
    const ITEM_TYPE_BIPORT = 'BIPORT';
    const ITEM_TYPE_UNIPORT = 'UNIPORT';
    const ITEM_TYPE_LINK = 'LINK';

    const TYPE_CREATE = "CREATE";
    const TYPE_UPDATE = "UPDATE";
    const TYPE_DELETE = "DELETE";

    const STATUS_PENDING = 'PENDING';
    const STATUS_FAILED = "FAILED";
    const STATUS_APPLIED = 'APPLIED';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topo_change}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sync_event_id', 'domain', 'status', 'type', 'item_type', 'data'], 'required'],
            [['sync_event_id', 'item_id'], 'integer'],
            [['status', 'type', 'item_type', 'data', 'error'], 'string'],
            [['applied_at'], 'safe'],
            [['domain'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('circuits', 'ID'),
            'sync_event_id' => Yii::t('circuits', 'Sync ID'),
            'domain' => Yii::t('circuits', 'Domain'),
            'status' => Yii::t('circuits', 'Status'),
            'type' => Yii::t('circuits', 'Type'),
            'item_type' => Yii::t('circuits', 'Element'),
            'item_id' => Yii::t('circuits', 'Item ID'),
            'data' => Yii::t('circuits', 'Element details'),
            'applied_at' => Yii::t('circuits', 'Applied At'),
            'error' => Yii::t('circuits', 'Error'),
        ];
    }

    public function searchPending($params, $syncId = null) {
        $query = self::find();

        // load the search form data and validate
        $this->load($params);

        // adjust the query by adding the filters
        $query->andFilterWhere(['status'=>TopologyChange::STATUS_PENDING]);
        $query->andFilterWhere(['domain' => $this->domain]);
        $query->andFilterWhere(['item_type' => $this->item_type]);
        $query->andFilterWhere(['type' => $this->type]);

        if ($syncEventId) $query->andFilterWhere(['sync_event_id' => $syncEventId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $dataProvider;
    }

    public function searchApplied($params) {
        $query = self::find();

        // load the search form data and validate
        $this->load($params);

        // adjust the query by adding the filters
        $query->andFilterWhere(['status'=>TopologyChange::STATUS_APPLIED]);
        $query->andFilterWhere(['domain' => $this->domain]);
        $query->andFilterWhere(['item_type' => $this->item_type]);
        $query->andFilterWhere(['type' => $this->type]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['applied_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $dataProvider;
    }

    public function apply() {
        $data = json_decode($this->data);
        switch ($this->item_type) {
            case self::ITEM_TYPE_DOMAIN:
                $dom = new Domain;
                $dom->name = $this->domain;
                $dom->default_policy = "ACCEPT_ALL";

                if($dom->save()) {
                    $this->setApplied();
                    return $this->save();
                }

                $this->error = json_encode($dom->getErrors());

                break;
            case self::ITEM_TYPE_PROVIDER:
                $provData = $data;
                $dom = Domain::findOneByName($this->domain);
                if ($this->item_id) {
                    $prov = Provider::findOne($this->item_id);
                } else {
                    $prov = new Provider;
                    $prov->nsa = $provData->nsa;
                }
                $prov->name = $provData->name;
                $prov->type = $provData->type;
                $prov->latitude = $provData->lat;
                $prov->longitude = $provData->lng;
                $prov->domain_id = $dom->id;

                if($prov->save()) {
                    $this->setApplied();
                    return $this->save();
                }

                $this->error = json_encode($prov->getErrors());
                break;

            case self::ITEM_TYPE_SERVICE:
                $serviceData = $data;
                
                switch ($this->type) {
                    case self::TYPE_DELETE:
                        $service = Service::findOne($this->item_id);
                        if($service && $service->delete()) {
                            $this->setApplied();
                            return $this->save();
                        } else {
                            $this->error = "Service not found";
                        }
                        break;
                    case self::TYPE_CREATE:
                        $prov = Provider::findOneByNsa($serviceData->provNsa);
                        if ($prov) {
                            $service = new Service;
                            $service->provider_id = $prov->id;
                            $service->type = $serviceData->type;
                            $service->url = $serviceData->url;

                            if($service->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($service->getErrors());
                            }
                        } else {
                            $this->error = "Provider not found";
                        }
                        break;
                    default:
                }
                
                break;

            case self::ITEM_TYPE_NETWORK: 
                $netData = $data;
                if ($this->item_id) {
                    $net = Network::findOne($this->item_id);
                    $net->latitude = $netData->lat;
                    $net->longitude = $netData->lng;
                    $net->name = $netData->name;
                } else {
                    $net = new Network;
                    $net->domain_id = Domain::findByName($this->domain)->one()->id;
                    $net->latitude = $netData->lat;
                    $net->longitude = $netData->lng;
                    $net->name = $netData->name;
                    $net->urn = $netData->urn;
                }
                if($net->save()) {
                    $this->setApplied();
                    return $this->save();
                }

                $this->error = json_encode($net->getErrors());
                break;

            case self::ITEM_TYPE_DEVICE: 
                $devData = $data;
                if ($this->item_id) {
                    $dev = Device::findOne($this->item_id);
                    $dev->latitude = $devData->lat;
                    $dev->longitude = $devData->lng;
                    $dev->address = $devData->address;
                } else {
                    //NECESSARIO pq o parser nao analisa esse caso
                    $dev = Device::findOneByDomainAndNode($this->domain, $devData->node);
                    if ($dev) {
                        $dev->latitude = $devData->lat;
                        $dev->longitude = $devData->lng;
                        $dev->address = $devData->address;
                    } else {
                        $dev = new Device;
                        $dev->domain_id = Domain::findByName($this->domain)->one()->id;
                        $dev->latitude = $devData->lat;
                        $dev->longitude = $devData->lng;
                        $dev->node = $devData->node;
                        $dev->name = $dev->node;
                        $dev->address = $devData->address;
                    }
                }
                if($dev->save()) {
                    $this->setApplied();
                    return $this->save();
                }

                $this->error = json_encode($dev->getErrors());

                break;
            case self::ITEM_TYPE_BIPORT:
                $portData = $data;
                switch ($this->type) {
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port && $port->delete()) {
                            $this->setApplied();
                            return $this->save();
                        }
                    default:
                        if ($this->item_id) {
                            $port = Port::findOne($this->data);
                        } else {
                            $port = new Port;
                            $dev = Device::findOneByDomainAndNode($this->domain, $portData->node);
                            if ($dev) {
                                $port->type = $portData->type;
                                $port->directionality = Port::DIR_BI;
                                $port->urn = $portData->urn;
                                $port->name = $portData->name;
                                $port->max_capacity = $portData->cap_max;
                                $port->min_capacity = $portData->cap_min;
                                $port->granularity = $portData->granu;
                                $port->vlan_range = $portData->vlan;

                                $net = Network::findByUrn($portData->netUrn)->one();
                                if ($net) $port->network_id = $net->id;

                                $port->device_id = $dev->id;
                            } else {
                                break;
                            }
                            
                        }
                        if($port->save()) {
                            $this->setApplied();
                            return $this->save();
                        } 
                        
                        $this->error = json_encode($port->getErrors());
                }

                break;

            case self::ITEM_TYPE_UNIPORT:
                $portData = $data;
                if ($this->item_id) {
                    $port = Port::findOne($this->data);
                } else {
                    $port = new Port;
                    $dev = Device::findOneByDomainAndNode($this->domain, $portData->node);
                    $biPortUrn = Port::findOne(['urn'=> $portData->biPortUrn]);
                    if ($dev && $biPortUrn) {
                        $port->type = Port::TYPE_NSI;
                        if ($portData->dir == "IN") {
                            $port->directionality = Port::DIR_UNI_IN;
                        } else {
                            $port->directionality = Port::DIR_UNI_OUT;
                        }

                        $port->urn = $portData->urn;
                        $port->name = $portData->name;
                        $port->max_capacity = $portData->cap_max;
                        $port->min_capacity = $portData->cap_min;
                        $port->granularity = $portData->granu;
                        $port->biport_id = $biPortUrn->id;
                        $port->vlan_range = $portData->vlan;

                        $net = Network::findByUrn($portData->netUrn)->one();
                        if ($net) $port->network_id = $net->id;

                        $port->device_id = $dev->id;

                    } else {
                        break;
                    }
                }

                if($port->save()) {
                    $this->setApplied();
                    return $this->save();
                } 

                $this->error = json_encode($port->getErrors());

                break;

            case self::ITEM_TYPE_LINK:
                $linkData = $data;
                if ($this->item_id) {
                } else {
                    $port = Port::findByUrn($linkData->urn)->one();
                    if ($port) {
                        $dstPort = Port::findByUrn($linkData->dst_urn)->one();
                        if ($dstPort) {
                            $port->setAlias($dstPort);
                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            }

                            $this->error = json_encode($port->getErrors());
                        } else {
                            $this->error = "The destination port does not exist.";
                        }
                    } else {
                        $this->error = "The source port does not exist.";
                    }
                }
                break;
            default:
                break;
        }

        $this->status = self::STATUS_FAILED;
        $this->save();
        return false;
    }

    private function setApplied() {
        $this->applied_at = DateUtils::now();
        $this->status = self::STATUS_APPLIED;
        $this->error = null;
    }

    public function getItemType() {
        switch ($this->item_type) {
            case self::ITEM_TYPE_DOMAIN: return Yii::t('topology', 'Domain');
            case self::ITEM_TYPE_PROVIDER: return Yii::t('topology', 'Provider');
            case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 'Service');
            case self::ITEM_TYPE_NETWORK: return Yii::t('topology', 'Network');
            case self::ITEM_TYPE_DEVICE: return Yii::t('topology', 'Device');
            case self::ITEM_TYPE_BIPORT: return Yii::t('topology', 'Bidirectional Port');
            case self::ITEM_TYPE_UNIPORT: return Yii::t('topology', 'Unidirectional Port');
            case self::ITEM_TYPE_LINK: return Yii::t('topology', 'Link');
            default: return Yii::t('topology', 'Unknown');
        }
    }

    public function getType() {
        switch ($this->type) {
            case self::TYPE_CREATE: return Yii::t('topology', 'Create');
            case self::TYPE_UPDATE: return Yii::t('topology', 'Update');
            case self::TYPE_DELETE: return Yii::t('topology', 'Delete');
            default: return Yii::t('topology', 'Unknown');
        }
    }

    static function getTypes() {
        return [
            ['id' => self::TYPE_CREATE, 'name' => Yii::t('topology', 'Create')],
            ['id' => self::TYPE_UPDATE, 'name' => Yii::t('topology', 'Update')],
            ['id' => self::TYPE_DELETE, 'name' => Yii::t('topology', 'Delete')]];
    }

    public function getParentInfo() {
        $data = json_decode($this->data);
        switch ($this->type) {
            case self::TYPE_CREATE:
            case self::TYPE_UPDATE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return "";
                    case self::ITEM_TYPE_PROVIDER: return "";
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 
                            '<b>Provider</b>: {provName}', 
                            ['provName'=> $data->provName]);
                    case self::ITEM_TYPE_NETWORK: return "";
                    case self::ITEM_TYPE_DEVICE: return "";
                    case self::ITEM_TYPE_BIPORT: return Yii::t('topology', 
                            '<b>Device</b>: {node}', 
                            ['node'=> $data->node == "" ? "default" : $data->node]);
                    case self::ITEM_TYPE_UNIPORT: return Yii::t('topology', 
                            '<b>Port</b>: {biPort} on <b>Device</b>: {node}', 
                            ['node'=> $data->node == "" ? "default" : $data->node, 'biPort'=>$data->biPort]);
                    case self::ITEM_TYPE_LINK: return Yii::t('topology', 
                            '<b>Port</b>: {port} on <b>Device</b>: {node}', 
                            ['node'=> $data->node == "" ? "default" : $data->node, 'port'=>$data->port]);
                    default: return Yii::t('topology', 'Error');
                } 
            case self::TYPE_DELETE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return "";
                    case self::ITEM_TYPE_PROVIDER: return "";
                    case self::ITEM_TYPE_SERVICE: return "";
                    case self::ITEM_TYPE_NETWORK: return "";
                    case self::ITEM_TYPE_DEVICE: return "";
                    case self::ITEM_TYPE_BIPORT: return "";
                    case self::ITEM_TYPE_UNIPORT: return Yii::t('topology', 
                            '<b>Port</b>: {biPort} on <b>Device</b>: {node}', 
                            ['node'=> $data->node == "" ? "default" : $data->node, 'biPort'=>$data->biPort]);
                    case self::ITEM_TYPE_LINK: return Yii::t('topology', 
                            '<b>Port</b>: {port} on <b>Device</b>: {node}', 
                            ['node'=> $data->node == "" ? "default" : $data->node, 'port'=>$data->port]);
                    default: return Yii::t('topology', 'Error');
                }
        }
    }

    static function getItemTypes() {
        return [
            ['id' => self::ITEM_TYPE_DOMAIN, 'name' => Yii::t('topology', 'Domain')],
            ['id' => self::ITEM_TYPE_PROVIDER, 'name' => Yii::t('topology', 'Provider')],
            ['id' => self::ITEM_TYPE_SERVICE, 'name' => Yii::t('topology', 'Service')],
            ['id' => self::ITEM_TYPE_NETWORK, 'name' => Yii::t('topology', 'Network')],
            ['id' => self::ITEM_TYPE_DEVICE, 'name' => Yii::t('topology', 'Device')],
            ['id' => self::ITEM_TYPE_BIPORT, 'name' => Yii::t('topology', 'Bidirectional Port')],
            ['id' => self::ITEM_TYPE_UNIPORT, 'name' => Yii::t('topology', 'Unidirectional Port')],
            ['id' => self::ITEM_TYPE_LINK, 'name' => Yii::t('topology', 'Link')]];
    }

    public function getDetails() {
        $data = json_decode($this->data);
        switch ($this->type) {
            case self::TYPE_DELETE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_PROVIDER: 
                            return Yii::t('topology', '<b>Provider</b>: {name}, <b>Type</b>: {type}', 
                            ['name' => $data->name, 'type'=>$data->type]);
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_NETWORK: return Yii::t('topology', 'Network');
                    case self::ITEM_TYPE_DEVICE: return Yii::t('topology', 'Device');
                    case self::ITEM_TYPE_BIPORT: return Yii::t('topology', 'Port');
                    case self::ITEM_TYPE_UNIPORT: return Yii::t('topology', 'Port');
                    default: return Yii::t('topology', 'Error');
                }
            case self::TYPE_UPDATE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_PROVIDER: 
                            $prov = Provider::findOne($this->item_id);
                            return Yii::t('topology', 'From: <b>Provider</b>: {name}, <b>Type</b>: {type}, <b>Latitude</b>: {lat}'.
                                ', <b>Longitude</b>: {lng}<br>', 
                            ['name' => $prov->name, 'type'=>$prov->type, 'lat'=> $prov->latitude, 'lng'=>$prov->longitude]).
                            Yii::t('topology', 'To: <b>Provider</b>: {name}, <b>Type</b>: {type}, <b>Latitude</b>: {lat}'.
                                ', <b>Longitude</b>: {lng}', 
                            ['name' => $data->name, 'type'=>$data->type, 'lat'=> $data->lat, 'lng'=>$data->lng]);
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_NETWORK: return Yii::t('topology', 'Network');
                    case self::ITEM_TYPE_DEVICE: 
                        $dev = Device::findOne($this->item_id);
                        return Yii::t('topology', 'From: <b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            [
                            'lat'=> $dev->latitude ? $dev->latitude : Yii::t('topology', 'undefined'), 
                            'lng'=> $dev->longitude ? $dev->longitude : Yii::t('topology', 'undefined')]).'<br>'.
                            Yii::t('topology', 'To: <b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            ['lat'=> $data->lat, 'lng'=>$data->lng]);
                    case self::ITEM_TYPE_BIPORT: return Yii::t('topology', 'Port');
                    case self::ITEM_TYPE_UNIPORT: return Yii::t('topology', 'Port');
                    default: return Yii::t('topology', 'Error');
                }
            case self::TYPE_CREATE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return "";
                    case self::ITEM_TYPE_PROVIDER: return Yii::t('topology', '<b>Provider</b>: {name}, <b>Type</b>: {type}, <b>Latitude</b>: {lat}'.
                                ', <b>Longitude</b>: {lng}', 
                            ['name' => $data->name, 'type'=>Provider::getTypeLabels()[$data->type], 'lat'=> $data->lat, 'lng'=>$data->lng]);
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', '<b>Service</b>: {type}, <b>URL</b>: {url}', 
                            ['url' => $data->url, 'type'=>Service::getTypeLabels()[$data->type]]);
                    case self::ITEM_TYPE_NETWORK: return Yii::t('topology', '<b>Network</b>: {name} - <b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            ['name' => $data->name , 
                            'lat'=> $data->lat, 
                            'lng'=> $data->lng]);
                    case self::ITEM_TYPE_DEVICE: 
                        $location = $data->lat ? Yii::t('topology',' - <b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            ['lat'=> $data->lat, 
                            'lng'=> $data->lng]) : "";
                        return Yii::t('topology', '<b>Device</b>: {node} - <b>Address</b>: {address}', 
                            ['node' => $data->node == "" ? "default" : $data->node, 'address'=>$data->address ? $data->address : "undefined"]).$location;
                    case self::ITEM_TYPE_BIPORT: 
                        $caps = $data->cap_max ? Yii::t('topology',' - <b>Capacity</b> (Mbps): Max: {max}, Min: {min}', 
                            ['max'=> $data->cap_max, 'min'=>$data->cap_min, 'granu'=> $data->granu]) : "";
                        $vlan = $data->vlan ? Yii::t('topology',' - <b>VLAN Range</b>: {vlan}', 
                            ['vlan'=> $data->vlan]) : "";
                        return Yii::t('topology', '<b>Bidirectional Port</b>: {name}',['name'=>$data->name]).$caps.$vlan;
                    case self::ITEM_TYPE_UNIPORT: 
                        return Yii::t('topology', '<b>Unidirectional Port</b>: {name}',['name'=>$data->name]);
                    case self::ITEM_TYPE_LINK: 
                        return Yii::t('topology', '<b>Link to Port</b>: {dst_urn}', 
                            ['dst_urn'=> $data->dst_urn]);
                    default: return Yii::t('topology', 'Error');
                }
        }
    }
}
