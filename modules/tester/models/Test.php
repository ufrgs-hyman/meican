<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\tester\models;

use Yii;

use meican\scheduler\utils\SchedulableTask;
use meican\scheduler\models\ScheduledTask;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Test extends Reservation implements SchedulableTask {

    const AT_PREFIX = "MeicanAT";

    public function attributeLabels() {

        return array_merge(parent::attributeLabels(),[
            'last_run_at' => Yii::t('circuits', 'Last execution'),
        ]);
    }

    function getConnectionStatus() {
        $conn = Connection::find()->where([
                'id'=> Connection::find()->where([
                        'reservation_id'=>$this->id])->max("id")])->select(['status'])->one();
        if ($conn) {
            switch ($conn->status) {
                case Connection::STATUS_PENDING:
                case Connection::STATUS_CREATED :
                case Connection::STATUS_CONFIRMED :         return Yii::t("circuits","Testing"); 
                case Connection::STATUS_SUBMITTED :         return Yii::t("circuits","Passed");
                case Connection::STATUS_FAILED_CREATE:      
                case Connection::STATUS_FAILED_CONFIRM :    
                case Connection::STATUS_FAILED_SUBMIT :     return Yii::t("circuits","Failed");
            }
        } else {
            return "Never tested";
        }
    }
    
    public function execute($data) {
        $test = self::findOne($data);
        if($test) {
            $date = new \DateTime();
            $date->modify('+10 minutes');
            $this->start = $date->format('Y-m-d H:i:s');
            $date->modify('+10 minutes');
            $this->finish =  $date->format('Y-m-d H:i:s');
            $this->save();
        
            $this->createConnections();
                
            $this->confirm();
        }
    }

    function getSourceDomain() {
        return $this->getSourceDevice()->select(['domain_id'])->one()->getDomain();
    }

    function getDestinationDomain() {
        return $this->getDestinationDevice()->select(['domain_id'])->one()->getDomain();
    }
    
    function getSourceDevice() {
        return $this->getSourcePort()->select(["device_id"])->one()->getDevice();
    }
    
    function getDestinationDevice() {
        return $this->getDestinationPort()->select(["device_id"])->one()->getDevice();
    }

    function getSourcePort() {
        return $this->getFirstPath()->one()->getPort();
    }

    function getDestinationPort() {
        return $this->getLastPath()->one()->getPort();
    }

    function getSourceVlanValue() {
        return $this->getFirstPath()->one()->vlan;
    }

    function getDestinationVlanValue() {
        return $this->getLastPath()->one()->vlan;
    }

    function getScheduledTask() {
        return ScheduledTask::findOne([
            'obj_class'=> 'meican\tester\models\Test',
            'obj_data'=> $this->id]);
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $task = $this->getScheduledTask();
            if($task) return $task->delete();
        }
        return false;
    }
}
