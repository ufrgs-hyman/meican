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
        $conn = Connection::find()
            ->where([
                'id'=> Connection::find()
                    ->where(['reservation_id'=>$this->id])
                    ->max("id")])
            ->select(['status'])
            ->one();
        Yii::trace($conn);
        if ($conn) {
            switch ($conn->status) {
                case Connection::STATUS_PENDING:
                case Connection::STATUS_CREATED :
                case Connection::STATUS_CONFIRMED :
                case Connection::STATUS_SUBMITTED:         return Yii::t("circuits","Testing"); 
                case Connection::STATUS_PROVISIONED :         return Yii::t("circuits","Passed");
                case Connection::STATUS_FAILED_CREATE:      
                case Connection::STATUS_FAILED_CONFIRM :    
                case Connection::STATUS_FAILED_PROVISION:
                case Connection::STATUS_FAILED_SUBMIT :     return Yii::t("circuits","Failed");
                default: return Yii::t("circuits","Unknown");
            }
        } else {
            return Yii::t("circuits","Never tested");
        }
    }
    
    public function execute($data) {
        $test = self::findOne($data);
        if($test) {
            $date = new \DateTime();
            $date->modify('+10 minutes');
            $events = ['start' => [$date->format('Y-m-d\TH:i:s.000-00:00')]];
            $test->start = $date->format('Y-m-d H:i:s');
            $date->modify('+10 minutes');
            $events['finish'] = [$date->format('Y-m-d\TH:i:s.000-00:00')];
            $test->finish =  $date->format('Y-m-d H:i:s');
            $test->save();

            $test->createConnections($events);
                
            $test->confirm();
        }
    }

    function getSourceDomain() {
        return $this->getSourcePort()->one()->getNetwork()->one()->getDomain();
    }

    function getDestinationDomain() {
        return $this->getDestinationPort()->one()->getNetwork()->one()->getDomain();
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
