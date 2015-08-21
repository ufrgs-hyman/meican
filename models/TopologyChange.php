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

    public function searchPending($params, $eventId = null) {
        $query = self::find();

        // load the search form data and validate
        $this->load($params);

        // adjust the query by adding the filters
        $query->andFilterWhere(['in','status',[TopologyChange::STATUS_PENDING, TopologyChange::STATUS_FAILED]]);
        $query->andFilterWhere(['domain' => $this->domain]);
        $query->andFilterWhere(['item_type' => $this->item_type]);
        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere(['sync_event_id' => $eventId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $dataProvider;
    }

    public function searchApplied($params, $eventId = null) {
        $query = self::find();

        // load the search form data and validate
        $this->load($params);

        // adjust the query by adding the filters
        $query->andFilterWhere(['status'=>TopologyChange::STATUS_APPLIED]);
        $query->andFilterWhere(['sync_event_id' => $eventId]);
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
                $dom->default_policy = Domain::ACCEPT_ALL;

                if($dom->save()) {
                    $this->setApplied();
                    return $this->save();
                } else {
                    $this->error = json_encode($dom->getErrors());
                }

                break;
            case self::ITEM_TYPE_PROVIDER:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $dom = Domain::findOneByName($this->domain);
                        if ($dom) {
                            $prov = new Provider;
                            $prov->nsa = $data->nsa;
                            $prov->name = $data->name;
                            $prov->type = $data->type;
                            $prov->latitude = $data->lat;
                            $prov->longitude = $data->lng;
                            $prov->domain_id = $dom->id;

                            if($prov->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($prov->getErrors());
                            }
                        } else {
                            $this->error = "domain does not exist";
                        }

                        break;
                    case self::TYPE_UPDATE:
                        $dom = Domain::findOneByName($this->domain);
                        if ($dom) {
                            $prov = Provider::findOne($this->item_id);
                            if($prov) {
                                $prov->name = $data->name;
                                $prov->type = $data->type;
                                $prov->latitude = $data->lat;
                                $prov->longitude = $data->lng;
                                $prov->domain_id = $dom->id;

                                if($prov->save()) {
                                    $this->setApplied();
                                    return $this->save();
                                } else {
                                    $this->error = json_encode($prov->getErrors());
                                }
                            } else {
                                $this->error = "provider does not exist";
                            }
                        } else {
                            $this->error = "domain does not exist";
                        }
                        
                        break;
                    case self::TYPE_DELETE:
                        $this->error = "invalid action";
                        break;
                }
                
                break;
            case self::ITEM_TYPE_SERVICE:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $prov = Provider::findOneByNsa($data->provNsa);
                        if ($prov) {
                            $service = new Service;
                            $service->provider_id = $prov->id;
                            $service->type = $data->type;
                            $service->url = $data->url;

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

                    case self::TYPE_UPDATE:
                        $this->error = "Invalid action";
                        break;

                    case self::TYPE_DELETE:
                        $service = Service::findOne($this->item_id);
                        if($service) {
                            if ($service->delete()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = "Error deleting";
                            }
                        } else {
                            $this->error = "Service not found";
                        }
                        break;    
                }
                
                break;
            case self::ITEM_TYPE_NETWORK: 
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $dom = Domain::findOneByName($this->domain);
                        if($dom) {
                            $net = new Network;
                            $net->latitude = $data->lat;
                            $net->longitude = $data->lng;
                            $net->name = $data->name;
                            $net->urn = $data->urn;
                            $net->domain_id = $dom->id;

                            if($net->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($net->getErrors());
                            }
                        } else {
                            $this->error = "domain not found";
                        }

                        break;

                    case self::TYPE_UPDATE:
                        $net = Network::findOne($this->item_id);
                        if ($net) {
                            $net->latitude = $data->lat;
                            $net->longitude = $data->lng;
                            $net->name = $data->name;

                            if($net->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($net->getErrors());
                            }
                        } else {
                            $this->error = "network not found";
                        }
                        
                        break;

                    case self::TYPE_DELETE:
                        $this->error = "invalid action";
                        break;    
                }

                break;
            case self::ITEM_TYPE_DEVICE: 
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        //NECESSARIO pq o parser nao analisa esse caso VERIFICAR
                       /* $dev = Device::findOneByDomainAndNode($this->domain, $data->node);
                        if ($dev) {
                            $dev->latitude = $data->lat;
                            $dev->longitude = $data->lng;
                            $dev->address = $data->address;
                        } else {*/
                             //}

                        $dom = Domain::findOneByName($this->domain);

                        if ($dom) {
                            $dev = new Device;
                            $dev->domain_id = $dom->id;
                            $dev->latitude = $data->lat;
                            $dev->longitude = $data->lng;
                            $dev->node = $data->node;
                            $dev->name = $dev->node;
                            $dev->address = $data->address;

                            if($dev->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($dev->getErrors());
                            }
                        } else {
                            $this->error = 'domain not found';
                        }

                        break;

                    case self::TYPE_UPDATE:
                        $dev = Device::findOne($this->item_id);

                        if ($dev) {
                            $dev->latitude = $data->lat;
                            $dev->longitude = $data->lng;
                            $dev->address = $data->address;
                            if($dev->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($dev->getErrors());
                            }

                        } else {
                            $this->error = 'device not found';
                        }

                        break;
                    case self::TYPE_DELETE:
                        $dev = Device::findOne($this->item_id);
                        if($dev) {
                            if ($dev->delete()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = 'error deleting';
                            }
                        } else {
                            $this->error = 'device not found';
                        }
                }

                break;
            case self::ITEM_TYPE_BIPORT:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $dev = Device::findOneByDomainAndNode($this->domain, $data->node);
                        if ($dev) {
                            $port = new Port;
                            $port->type = $data->type;
                            $port->directionality = Port::DIR_BI;
                            $port->urn = $data->urn;
                            $port->name = $data->name;
                            $port->max_capacity = $data->cap_max;
                            $port->min_capacity = $data->cap_min;
                            $port->granularity = $data->granu;
                            $port->vlan_range = $data->vlan;
                            $port->device_id = $dev->id;

                            if ($data->netUrn) {
                                $net = Network::findByUrn($data->netUrn)->one();
                                if ($net) $port->network_id = $net->id;
                            }

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($port->getErrors());
                            }
                        } 
                        break;
                    case self::TYPE_UPDATE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            $port->name = $data->name;
                            $port->max_capacity = $data->cap_max;
                            $port->min_capacity = $data->cap_min;
                            $port->granularity = $data->granu;
                            $port->vlan_range = $data->vlan;

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($port->getErrors());
                            }
                        } else {
                            $this->error = "port not found";
                        }
                        
                        break;
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            if($port->delete()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = 'error deleting';
                            }
                        } else {
                            $this->error = 'port not found';
                        }
                }

                break;
            case self::ITEM_TYPE_UNIPORT:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $dev = Device::findOneByDomainAndNode($this->domain, $data->node);
                        $biPortUrn = Port::findOne(['urn'=> $data->biPortUrn]);
                        if ($dev && $biPortUrn) {
                            $port = new Port;
                            $port->type = Port::TYPE_NSI;
                            if ($data->dir == "IN") {
                                $port->directionality = Port::DIR_UNI_IN;
                            } else {
                                $port->directionality = Port::DIR_UNI_OUT;
                            }

                            $port->urn = $data->urn;
                            $port->name = $data->name;
                            $port->max_capacity = $data->cap_max;
                            $port->min_capacity = $data->cap_min;
                            $port->granularity = $data->granu;
                            $port->biport_id = $biPortUrn->id;
                            $port->vlan_range = $data->vlan;
                            $port->device_id = $dev->id;

                            if ($data->netUrn) {
                                $net = Network::findByUrn($data->netUrn)->one();
                                if ($net) $port->network_id = $net->id;
                            }

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($port->getErrors());
                            }
                        } 
                        break;
                    case self::TYPE_UPDATE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            $port->name = $data->name;
                            $port->max_capacity = $data->cap_max;
                            $port->min_capacity = $data->cap_min;
                            $port->granularity = $data->granu;
                            $port->vlan_range = $data->vlan;

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = json_encode($port->getErrors());
                            }
                        } else {
                            $this->error = "port not found";
                        }
                        
                        break;
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            if($port->delete()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = 'error deleting';
                            }
                        } else {
                            $this->error = 'port not found';
                        }
                }

                break;
            case self::ITEM_TYPE_LINK:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $port = Port::findOneByUrn($data->urn);
                        if ($port) {
                            $dstPort = Port::findOneByUrn($data->dst_urn);
                            if ($dstPort) {
                                $port->setAlias($dstPort);
                                if($port->save()) {
                                    $this->setApplied();
                                    return $this->save();
                                } else {
                                    $this->error = json_encode($port->getErrors());
                                }
                                
                            } else {
                                $this->error = "destin port not found";
                            }
                        } else {
                            $this->error = "source port not found";
                        }
                        break;
                    case self::TYPE_UPDATE:
                        break;
                    case self::TYPE_DELETE:
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
                    case self::ITEM_TYPE_DEVICE: return $data->node;
                    case self::ITEM_TYPE_BIPORT: 
                        $port = Port::findOne($this->item_id);
                        return $port ? $port->name : '';
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
                        return Yii::t('topology', '<b>Device</b>: {node}  - <b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            [ 'node'=> $dev ? $dev->node : Yii::t('topology', 'undefined'),
                            'lat'=> $dev ? $dev->latitude : Yii::t('topology', 'undefined'), 
                            'lng'=> $dev ? $dev->longitude : Yii::t('topology', 'undefined')]).'<br>'.
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
