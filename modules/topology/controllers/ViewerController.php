<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use Yii;

use meican\aaa\RbacController;
use meican\topology\models\Domain;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\topology\models\Peering;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ViewerController extends RbacController {

    public function actionIndex() {
        return $this->render('index');
    }

    //REST functions

    public function actionSavePreference() {
        
    }

    public function actionSaveGraphPositions() {
        Yii::trace($_POST);
        if($_POST['nodes']) {
            switch ($_POST['mode']) {
                case 'dom':
                    foreach ($_POST['nodes'] as $node) {
                        $dom = Domain::findOne(str_replace("dom", "", $node['id']));
                        $dom->graph_x = $node['x'];
                        $dom->graph_y = $node['y'];
                        $dom->save();
                    }
                    break;
                case 'dev':
                    foreach ($_POST['nodes'] as $node) {
                        $dev = Device::findOne(str_replace("dev", "", $node['id']));
                        $dev->graph_x = $node['x'];
                        $dev->graph_y = $node['y'];
                        $dev->save();
                    }
                    break;
                case 'net':
                    break;
                
                default:
                    break;
            }
            
        }
        return "";
    }
    
    public function actionGetNetworkLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])
            ->andWhere(['not', ['network_id'=> null]])
            ->select(['id','directionality','network_id','alias_id'])->all();
        $links = [];
        foreach ($portsWithAlias as $port) {
            $netId1 = $port['network_id'];
            $netId2 = $port->getAlias()->select(['network_id'])->asArray()->one()['network_id'];

            if($netId1 != $netId2) {
                isset($links[$netId1]) ? null : $links[$netId1] = [];
                isset($links[$netId2]) ? null : $links[$netId2] = [];

                switch ($port->directionality) {
                    case Port::DIR_UNI_OUT:
                        if(!in_array($netId2, $links[$netId1]))
                            $links[$netId1][] = $netId2; break;
                    case Port::DIR_UNI_IN:
                        if(!in_array($netId1, $links[$netId2]))
                            $links[$netId2][] = $netId1; break;
                    case Port::DIR_BI:
                        if(!in_array($netId2, $links[$netId1]))
                            $links[$netId1][] = $netId2; break;
                        if(!in_array($netId1, $links[$netId2]))
                            $links[$netId2][] = $netId1; break;
                    default:
                        break;
                }
            }
        }
        Yii::trace($links);
        return json_encode($links);
    }

    public function actionGetDevicePorts($dom=null, $type=null) {
        if($dom != null) {
            $validDevs = Device::find()->where(['domain_id'=>$dom])->asArray()->select('id')->all();
            $devs =[];
            foreach ($validDevs as $value) {
                $devs[] = $value['id'];
            }
            $portsWithAlias = Port::find()
                ->where(['in', 'device_id', $devs])
                ->andWhere(['type'=>$type])
                ->select(['id','name','directionality','max_capacity','device_id','alias_id'])
                ->all();
        } else {
            $portsWithAlias = Port::find()
                ->select(['id','name','directionality','max_capacity','device_id','alias_id'])->all();
        }
        
        $ports = [];
        foreach ($portsWithAlias as $port) {
            $devId1 = $port->device_id;
            $aliasPort = $port->getAlias()->select(['id','device_id'])->asArray()->one();
            $devId2 = $aliasPort['device_id'];

            isset($ports[$devId1]) ? null : $ports[$devId1] = [];
            $devId2 ? (isset($ports[$devId2]) ? null : $ports[$devId2] = []) : null;

            if(!in_array($port->id, $ports[$devId1]))
                $ports[$devId1][$port->id] = [
                    'dir'=>$port->directionality,
                    'name' => $port->name,
                    'cap' => $port->max_capacity,
                    'link' => [
                        'dev' => $devId2,
                        'port' => $aliasPort['id']
                    ],
                ]; 
        }
        Yii::trace($ports);
        return json_encode($ports);
    }

    public function actionGetDeviceLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])
            ->select(['id','directionality','device_id','alias_id'])->all();
        $links = [];
        foreach ($portsWithAlias as $port) {
            $devId1 = $port->device_id;
            $devId2 = $port->getAlias()->select(['id','device_id'])->asArray()->one()['device_id'];

            if($devId1 != $devId2) {
                isset($links[$devId1]) ? null : $links[$devId1] = [];
                isset($links[$devId2]) ? null : $links[$devId2] = [];

                switch ($port->directionality) {
                    case Port::DIR_UNI_OUT:
                        if(!in_array($devId2, $links[$devId1]))
                            $links[$devId1][] = $devId2; break;
                    case Port::DIR_UNI_IN:
                        if(!in_array($devId1, $links[$devId2]))
                            $links[$devId2][] = $devId1; break;
                    case Port::DIR_BI:
                        if(!in_array($devId2, $links[$devId1]))
                            $links[$devId1][] = $devId2; break;
                        if(!in_array($devId1, $links[$devId2]))
                            $links[$devId2][] = $devId1; break;
                    default:
                        break;
                }
            }
        }
        Yii::trace($links);
        return json_encode($links);
    }

    public function actionGetDomainLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])
            ->select(['id','directionality','network_id','alias_id'])->all();
        $links = [];
        $passedPorts = [];

        foreach ($portsWithAlias as $port) {
            $domId1 = $port->getNetwork()->select(['domain_id'])->asArray()->one()['domain_id'];
            $dstPort = $port->getAlias()->select(['id','network_id'])->one();
            $domId2 = $dstPort->getNetwork()->select(['domain_id'])->asArray()->one()['domain_id'];

            if($domId1 != $domId2) {
                isset($links[$domId1]) ? null : $links[$domId1] = [];
                isset($links[$domId2]) ? null : $links[$domId2] = [];

                switch ($port->directionality) {
                    case Port::DIR_UNI_OUT:
                        if(!in_array($domId2, $links[$domId1]))
                            $links[$domId1][] = $domId2; break;
                    case Port::DIR_UNI_IN:
                        if(!in_array($domId1, $links[$domId2]))
                            $links[$domId2][] = $domId1; break;
                    case Port::DIR_BI:
                        if(!in_array($domId2, $links[$domId1]))
                            $links[$domId1][] = $domId2; break;
                        if(!in_array($domId1, $links[$domId2]))
                            $links[$domId2][] = $domId1; break;
                    default:
                        break;
                }
            }
        }

        return json_encode($links);
    }

    public function actionGetCapLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])
            ->select(['id','directionality','network_id','max_capacity', 'alias_id'])->all();
        $links = [];
        $capacity = [];
        $passedPorts = [];

        foreach ($portsWithAlias as $port) {
            $domId1 = $port->getNetwork()->select(['domain_id'])->asArray()->one()['domain_id'];
            $dstPort = $port->getAlias()->select(['id','network_id', 'max_capacity'])->one();
            $domId2 = $dstPort->getNetwork()->select(['domain_id'])->asArray()->one()['domain_id'];

            if($domId1 != $domId2) {
                isset($links[$domId1]) ? null : $links[$domId1] = [];
                isset($links[$domId2]) ? null : $links[$domId2] = [];

                $cap = ($dstPort->max_capacity and $port->max_capacity)? min($dstPort->max_capacity, $port->max_capacity) : ($port->max_capacity) ? $port->max_capacity : $dstPort->max_capacity; 

                switch ($port->directionality) {
                    case Port::DIR_UNI_OUT:
                        if(!in_array($domId2, $links[$domId1])) {
                            $links[$domId1][] = $domId2; 
                            $capacity[$domId1][] = ($cap)? ['port' => $domId2, 'max_capacity' => $cap] : ['port' => $domId2];
                        }
                        break;
                    case Port::DIR_UNI_IN:
                        if(!in_array($domId1, $links[$domId2])) {
                            $links[$domId2][] = $domId1;
                            $capacity[$domId2][] = ($cap)? ['port' => $domId1, 'max_capacity' => $cap] : ['port' => $domId1]; 
                        }
                        break;
                    case Port::DIR_BI:
                        if(!in_array($domId2, $links[$domId1]))
                            $links[$domId1][] = $domId2; break;
                        if(!in_array($domId1, $links[$domId2]))
                            $links[$domId2][] = $domId1; break;
                    default:
                        break;
                }
            }
        }

        return json_encode($capacity);
    }

    public function actionGetPortLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])
            ->select(['id', 'directionality','alias_id','biport_id'])->asArray()->all();
        $links = [];
        $passedPorts = [];

        foreach ($portsWithAlias as $port) {
            $srcBiPortId = $port['biport_id'] ? $port['biport_id'] : $port['id'];
            $dstPort = Port::find()->where(['id'=> $port['alias_id']])->select(['id','biport_id'])->asArray()->one();
            $dstBiPortId = $dstPort['biport_id'] ? $dstPort['biport_id'] : $dstPort['id'];

            isset($links[$srcBiPortId]) ? null : $links[$srcBiPortId] = [];
            isset($links[$dstBiPortId]) ? null : $links[$dstBiPortId] = [];

            switch ($port['directionality']) {
                case Port::DIR_UNI_OUT:
                    if(!in_array($dstBiPortId, $links[$srcBiPortId]))
                        $links[$srcBiPortId][] = $dstBiPortId; break;
                case Port::DIR_UNI_IN:
                    if(!in_array($srcBiPortId, $links[$dstBiPortId]))
                        $links[$dstBiPortId][] = $srcBiPortId; break;
                case Port::DIR_BI:
                    if(!in_array($dstBiPortId, $links[$srcBiPortId]))
                        $links[$srcBiPortId][] = $dstBiPortId; break;
                    if(!in_array($srcBiPortId, $links[$dstBiPortId]))
                        $links[$dstBiPortId][] = $srcBiPortId; break;
                default:
                    break;
            }
        }

        return json_encode($links);
    }

    public function actionGetPortCapLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])
            ->select(['id', 'directionality','alias_id','biport_id', 'max_capacity'])->asArray()->all();
        $links = [];
        $link_capacity = [];
        $passedPorts = [];

        foreach ($portsWithAlias as $port) {
            $srcBiPortId = $port['biport_id'] ? $port['biport_id'] : $port['id'];
            $dstPort = Port::find()->where(['id'=> $port['alias_id']])->select(['id','biport_id', 'max_capacity'])->asArray()->one();
            $dstBiPortId = $dstPort['biport_id'] ? $dstPort['biport_id'] : $dstPort['id'];

            isset($links[$srcBiPortId]) ? null : $links[$srcBiPortId] = [];
            isset($link_capacity[$srcBiPortId]) ? null : $link_capacity[$srcBiPortId] = [];

            isset($links[$dstBiPortId]) ? null : $links[$dstBiPortId] = [];
            isset($link_capacity[$dstBiPortId]) ? null : $link_capacity[$dstBiPortId] = [];


            $cap = ($port['max_capacity'] and $dstPort['max_capacity']) ? min($port['max_capacity'], $dstPort['max_capacity']) : ($port['max_capacity'])? $port['max_capacity'] : $dstPort['max_capacity'];


            switch ($port['directionality']) {
                case Port::DIR_UNI_OUT:
                    if(!in_array($dstBiPortId, $links[$srcBiPortId]))   {
                        $links[$srcBiPortId][] = $dstBiPortId; 
                        $link_capacity[$srcBiPortId][] = ($cap) ? ['port' => $dstBiPortId, 'max_capacity' => $cap] : ['port' => $dstBiPortId];
                    }
                    break;
                case Port::DIR_UNI_IN:
                    if(!in_array($srcBiPortId, $links[$dstBiPortId]))   {
                        $links[$dstBiPortId][] = $srcBiPortId; 
                        $link_capacity[$dstBiPortId][] = ($cap) ? ['port' => $srcBiPortId, 'max_capacity' => $cap] : ['port' => $srcBiPortId];
                    }
                    break;
                case Port::DIR_BI:
                    if(!in_array($dstBiPortId, $links[$srcBiPortId]))   {
                        $links[$srcBiPortId][] = $dstBiPortId; 
                        $link_capacity[$srcBiPortId][] = ($cap) ? ['port' => $dstBiPortId, 'max_capacity' => $cap] : ['port' => $dstBiPortId];
                    }
                    break;
                    if(!in_array($srcBiPortId, $links[$dstBiPortId]))   {
                        $links[$dstBiPortId][] = $srcBiPortId; 
                        $link_capacity[$dstBiPortId][] = ($cap) ? ['port' => $srcBiPortId, 'max_capacity' => $cap] : ['port' => $srcBiPortId];
                    }    
                    break;
                default:
                    break;
            }
        }

        return json_encode($link_capacity);
    }

    public function actionGetPeerings() {
        $peerings = Peering::find()->asArray()->all();
        $links = [];

        foreach ($peerings as $peering) {
            isset($links[$peering['src_id']]) ? null : $links[$peering['src_id']] = [];

            $links[$peering['src_id']][] = $peering['dst_id'];
        }

        Yii::trace($links);
        return json_encode($links);
    }

    public function actionSearch($term) {
        $term = str_replace("urn:ogf:network:", "", $term);
        $ports = Port::findBySql(
            "SELECT `name`, `device_id`, `network_id` 
            FROM `meican_port` 
            WHERE ((`urn` COLLATE UTF8_GENERAL_CI LIKE :term) 
            OR (`name` LIKE :term)) AND `directionality` = 'BI' AND `type` = 'NSI'
            LIMIT 5")->addParams([':term'=>'%'.$term.'%'])->asArray()->all();
        Yii::trace($ports);
        return json_encode($ports);
    }
}
