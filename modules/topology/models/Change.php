<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;

use yii\data\ActiveDataProvider;
use meican\base\utils\DateUtils;
use meican\base\utils\ColorUtils;

/**
 * Esta classe representa uma Alteração ou diferença
 * da topologia local comparada a topologia do provedor
 * consultado. Changes são geradas durante uma consulta
 * realizada pelo DiscoveryService.
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
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Change extends \yii\db\ActiveRecord
{
    const ITEM_TYPE_DOMAIN = 'DOMAIN';
    const ITEM_TYPE_PROVIDER = 'PROVIDER';
    const ITEM_TYPE_PEERING = 'PEERING';
    const ITEM_TYPE_SERVICE = 'SERVICE';
    const ITEM_TYPE_NETWORK = 'NETWORK';
    const ITEM_TYPE_BIPORT = 'BIPORT';
    const ITEM_TYPE_UNIPORT = 'UNIPORT';
    const ITEM_TYPE_LINK = 'LINK';

    const TYPE_CREATE = "CREATE";
    const TYPE_UPDATE = "UPDATE";
    const TYPE_DELETE = "DELETE";

    const STATUS_PENDING = 'PENDING';
    const STATUS_FAILED = "FAILED";
    const STATUS_APPLIED = 'APPLIED';

    public $count;

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
            'id' => Yii::t('topology', 'ID'),
            'sync_event_id' => Yii::t('topology', 'Sync ID'),
            'domain' => Yii::t('topology', 'Domain'),
            'status' => Yii::t('topology', 'Status'),
            'type' => Yii::t('topology', 'Type'),
            'item_type' => Yii::t('topology', 'Element'),
            'item_id' => Yii::t('topology', 'Item ID'),
            'data' => Yii::t('topology', 'Element details'),
            'applied_at' => Yii::t('topology', 'Applied at'),
            'error' => Yii::t('topology', 'Error'),
        ];
    }

    public function getTask()
    {
        return $this->hasOne(DiscoveryTask::className(), ['id' => 'sync_event_id']);
    }

    public function searchPending($params, $eventId = null) {
        $query = self::find();

        // load the search form data and validate
        $this->load($params);

        // adjust the query by adding the filters
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
        $query->andFilterWhere(['status'=>Change::STATUS_APPLIED]);
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

    private function updateLocation($name, $lat, $lng, $domain_id)  {
        $location_id = null;

        $locationQuery = Location::findByName($name)->one();
        if($locationQuery)
            $location_id = $locationQuery->id;
        else    {
            $location = new Location;
            $location->lat = $lat;
            $location->lng = $lng;
            $location->name = $name;
            $location->domain_id = $domain_id;

            if($location->save()) {
                // $location_id = $location->id;
                $location_id = $location->getPrimaryKey();
            }
            else {
                $this->error = "Unknown";
            }
        }

        return $location_id;
    }

    public function apply() {
        $data = json_decode($this->data);

        switch ($this->item_type) {
            case self::ITEM_TYPE_DOMAIN:
                $dom = new Domain;
                $dom->name = $this->domain;
                $dom->color = ColorUtils::generate();
                $dom->default_policy = Domain::ACCEPT_ALL;

                if($dom->save()) {
                    $this->setApplied();
                    return $this->save();
                } else {
                    $this->error = "Unknown";
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
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Domain does not exist";
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
                                    $this->error = "Unknown";
                                }
                            } else {
                                $this->error = "Provider does not exist";
                            }
                        } else {
                            $this->error = "Domain does not exist";
                        }
                        
                        break;
                    case self::TYPE_DELETE:
                        $this->error = 'Invalid action';
                        break;
                }
                
                break;
            case self::ITEM_TYPE_PEERING:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $prov = Provider::findOneByNsa($data->srcNsaId);
                        if ($prov) {
                            $dstProv = Provider::findOneByNsa($data->dstNsaId);
                            if ($dstProv) {
                                $peering = new Peering;
                                $peering->src_id = $prov->id;
                                $peering->dst_id = $dstProv->id;
                                if($peering->save()) {
                                    $this->setApplied();
                                    return $this->save();
                                } else {
                                    $this->error = "Unknown";
                                }
                                
                            } else {
                                $this->error = "Destination not found";
                            }
                        } else {
                            $this->error = "Source not found";
                        }
                        break;
                    case self::TYPE_UPDATE:
                        $port = Port::findOneByUrn($data->urn);
                        if ($port) {
                            $dstPort = Port::findOneByUrn($data->dst_urn);
                            if ($dstPort) {
                                $port->setAlias($dstPort);
                                if($port->save()) {
                                    $this->setApplied();
                                    return $this->save();
                                } else {
                                    $this->error = "Unknown";
                                }
                                
                            } else {
                                $this->error = "Destination not found";
                            }
                        } else {
                            $this->error = "Source not found";
                        }

                        break;
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            $port->alias_id = null;
                            if ($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Source not found";
                        }
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
                                $this->error = "Unknown";
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
                            $net->version = DateUtils::toUTCfromGMT($data->version);
                            $net->domain_id = $dom->id;

                            if($net->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Domain not found";
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
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Network not found";
                        }
                        
                        break;

                    case self::TYPE_DELETE:
                        $this->error = "In development";
                        break;    
                }

                break;
            case self::ITEM_TYPE_BIPORT:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $port = new Port;
                        $port->type = $data->type;
                        $port->directionality = Port::DIR_BI;
                        $port->urn = $data->urn;
                        $port->name = $data->name;
                        $port->max_capacity = $data->cap_max;
                        $port->capacity = $data->capacity;
                        $port->min_capacity = $data->cap_min;
                        $port->granularity = $data->granu;
                        $port->vlan_range = $data->vlan;

                        if($data->locationName) {
                            $dom = Domain::findOneByName($this->domain);

                            if($dom)    {
                                $location_id = $this->updateLocation($data->locationName, $data->lat, $data->lng, $dom->id);

                                if($location_id)
                                    $port->location_id = $location_id;
                            }
                        }

                        if ($data->netUrn) {
                            $net = Network::findByUrn($data->netUrn)->one();
                            if ($net) $port->network_id = $net->id;
                        }

                        if($port->save()) {
                            $this->setApplied();
                            return $this->save();
                        } else {
                            Yii::trace($port->getErrors());
                            $this->error = "Unknown";
                        }
                        break;
                    case self::TYPE_UPDATE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            // $port->name = $data->name;
                            // $port->max_capacity = $data->cap_max;
                            // $port->min_capacity = $data->cap_min;
                            // $port->granularity = $data->granu;
                            $port->vlan_range = $data->vlan;

                            // if($data->locationName) {
                            //     $dom = Domain::findOneByName($this->domain);

                            //     if($dom)    {
                            //         $location_id = $this->updateLocation($data->locationName, $data->lat, $data->lng, $dom->id);

                            //        if($location_id)
                            //             $port->location_id = $location_id;
                            //     }
                            // }

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                Yii::trace($port->getErrors());
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Port not found";
                        }
                        
                        break;
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            if($port->delete()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = 'Error deleting';
                            }
                        } else {
                            $this->error = 'Port not found';
                        }
                }

                break;
            case self::ITEM_TYPE_UNIPORT:
                switch ($this->type) {
                    case self::TYPE_CREATE:
                        $biPortUrn = Port::findOne(['urn'=> $data->biPortUrn]);
                        if ($biPortUrn) {
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

                            if ($data->netUrn) {
                                $net = Network::findByUrn($data->netUrn)->one();
                                if ($net) $port->network_id = $net->id;
                            }

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = "Unknown";
                            }
                        } 
                        break;
                    case self::TYPE_UPDATE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            $port->name = isset($data->name) ? $data->name : $port->name;
                            $port->max_capacity = isset($data->cap_max) ? $data->cap_max : null;
                            $port->min_capacity = isset($data->cap_min) ? $data->cap_min : null;
                            $port->granularity = isset($data->granu) ? $data->granu : null;
                            $port->vlan_range = isset($data->vlan) ? $data->vlan : null;

                            if($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Port not found";
                        }
                        
                        break;
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            if($port->delete()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = 'Error deleting port';
                            }
                        } else {
                            $this->error = 'Port not found';
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
                                    $this->error = "Unknown";
                                }
                                
                            } else {
                                $this->error = 'Destination port not found';
                            }
                        } else {
                            $this->error = "Source port not found";
                        }
                        break;
                    case self::TYPE_UPDATE:
                        $port = Port::findOneByUrn($data->urn);
                        if ($port) {
                            $dstPort = Port::findOneByUrn($data->dst_urn);
                            if ($dstPort) {
                                $port->setAlias($dstPort);
                                if($port->save()) {
                                    $this->setApplied();
                                    return $this->save();
                                } else {
                                    $this->error = "Unknown";
                                }
                                
                            } else {
                                $this->error = 'Destination port not found';
                            }
                        } else {
                            $this->error = "Source port not found";
                        }

                        break;
                    case self::TYPE_DELETE:
                        $port = Port::findOne($this->item_id);
                        if ($port) {
                            $port->alias_id = null;
                            if ($port->save()) {
                                $this->setApplied();
                                return $this->save();
                            } else {
                                $this->error = "Unknown";
                            }
                        } else {
                            $this->error = "Source port not found";
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
            case self::ITEM_TYPE_PEERING: return Yii::t('topology', 'Peering');
            case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 'Service');
            case self::ITEM_TYPE_NETWORK: return Yii::t('topology', 'Network');
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

    static function getItemTypes() {
        return [
            ['id' => self::ITEM_TYPE_DOMAIN, 'name' => Yii::t('topology', 'Domain')],
            ['id' => self::ITEM_TYPE_PROVIDER, 'name' => Yii::t('topology', 'Provider')],
            ['id' => self::ITEM_TYPE_PEERING, 'name' => Yii::t('topology', 'Peering')],
            ['id' => self::ITEM_TYPE_SERVICE, 'name' => Yii::t('topology', 'Service')],
            ['id' => self::ITEM_TYPE_NETWORK, 'name' => Yii::t('topology', 'Network')],
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
                    case self::ITEM_TYPE_PEERING: return "";
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_NETWORK: return Yii::t('topology', 'Network');
                    case self::ITEM_TYPE_BIPORT: 
                        $port = Port::findOne($this->item_id);
                        return $port ? $port->name : '';
                    case self::ITEM_TYPE_UNIPORT: return Yii::t('topology', 'Port');
                    case self::ITEM_TYPE_LINK: return '';
                    default: return Yii::t('topology', 'Error');
                }
            case self::TYPE_UPDATE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_PROVIDER: 
                            $prov = Provider::findOne($this->item_id);
                            return Yii::t('topology', 'To: <b>Provider</b>: {name}, <b>Type</b>: {type}, <b>Latitude</b>: {lat}'.
                                ', <b>Longitude</b>: {lng}', 
                            ['name' => $data->name, 'type'=>$data->type, 'lat'=> $data->lat, 'lng'=>$data->lng]);
                    case self::ITEM_TYPE_PEERING: return "";
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', 'Domain');
                    case self::ITEM_TYPE_NETWORK: return Yii::t('topology', 'URN');
                    case self::ITEM_TYPE_BIPORT: return Yii::t('topology', 'Port');
                    case self::ITEM_TYPE_UNIPORT: 
                        $port = Port::findOneArraySelect($this->item_id, ['urn']);
                        $vlan = $data->vlan ? Yii::t('topology',' - <b>VLAN Range</b>: {vlan}', 
                            ['vlan'=> $data->vlan]) : "";
                        return Yii::t('topology', '<b>Unidirectional Port</b>: {urn}',['urn'=>$port['urn']]).$vlan;
                    case self::ITEM_TYPE_LINK: 
                        return Yii::t('topology', '<b>Link to Port</b>: {dst_urn}', 
                            ['dst_urn'=> $data->dst_urn]);
                    default: return Yii::t('topology', 'Error');
                }
            case self::TYPE_CREATE:
                switch ($this->item_type) {
                    case self::ITEM_TYPE_DOMAIN: return "";
                    case self::ITEM_TYPE_PROVIDER: return Yii::t('topology', '<b>Provider</b>: {name}<br><b>Type</b>: {type}<br><b>Latitude</b>: {lat}'.
                                ', <b>Longitude</b>: {lng}', 
                            ['name' => $data->name, 'type'=>Provider::getTypeLabels()[$data->type], 'lat'=> $data->lat, 'lng'=>$data->lng]);
                    case self::ITEM_TYPE_PEERING: return Yii::t('topology', '<b>Provider</b>: {nsa}<br><b>Peering with</b>: {dstNsaId}', 
                            ['nsa' => $data->srcNsaId,'dstNsaId' => $data->dstNsaId]);
                    case self::ITEM_TYPE_SERVICE: return Yii::t('topology', '<b>Provider</b>: {nsa}<br><b>Service</b>: {type}<br><b>URL</b>: {url}', 
                            ['nsa'=>$data->provNsa,'url' => $data->url, 'type'=>Service::getTypeLabels()[$data->type]]);
                    case self::ITEM_TYPE_NETWORK: 
                        $location = $data->lat ? Yii::t('topology','<br><b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            ['lat'=> $data->lat, 
                            'lng'=> $data->lng]) : "";
                        return Yii::t('topology', '<b>Name</b>: {name}<br><b>URN</b>: {urn}<br><b>Version</b>: {version}',
                            ['name'=>$data->name,'urn' => $data->urn,'version'=>$data->version]).
                            $location;
                    case self::ITEM_TYPE_BIPORT:     
                        $location = $data->lat ? Yii::t('topology',
                            '<br><b>Location</b>: {lname}, <b>Latitude</b>: {lat}, <b>Longitude</b>: {lng}', 
                            ['lname' => $data->locationName,
                            'lat'=> $data->lat, 
                            'lng'=> $data->lng,]) : "";
                        $vlan = $data->vlan ? Yii::t('topology','<br><b>VLAN Range</b>: {vlan}', 
                            ['vlan'=> $data->vlan]) : "";
                        return Yii::t('topology', '<b>Name</b>: {name}<br><b>URN</b>: {urn}',
                            ['urn'=>$data->urn, 'name'=>$data->name]).$vlan.$location;
                    case self::ITEM_TYPE_UNIPORT: 
                        return Yii::t('topology', '<b>Name</b>: {name}<br><b>URN</b>: {urn}<br><b>Parent Port URN</b>: {biPortUrn}',
                            ['urn'=>$data->urn, 'biPortUrn'=>$data->biPortUrn, 'name'=>$data->name]);
                    case self::ITEM_TYPE_LINK: 
                        return Yii::t('topology', '<b>From</b>: {src}<br><b>To</b>: {dst}', 
                            ['dst'=> $data->dst_urn, 'src'=> $data->urn]);
                    default: return Yii::t('topology', 'Error');
                }
        }
    }
}
