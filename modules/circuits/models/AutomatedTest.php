<?php

namespace meican\circuits\models;

use Yii;

use meican\base\models\Cron;

class AutomatedTest extends Reservation {

    const AT_PREFIX = "MeicanAT";

    public function attributeLabels() {

        return array_merge(parent::attributeLabels(),[
            'last_run_at' => Yii::t('circuits', 'Última execução'),
        ]);
    }

    function getCron() {
        return Cron::findTestTask($this->id);
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
            return "";
        }
    }
    
    function getStatus() {
        switch ($this->getCron()->one()->status) {
            case Cron::STATUS_ENABLED: return Yii::t("circuits","Enabled");
            case Cron::STATUS_DISABLED: return Yii::t("circuits","Disabled");
            case Cron::STATUS_PROCESSING: return Yii::t("circuits","Processing"); 
        }
    }
    
    function execute() {
        $date = new \DateTime();
        $date->modify('+10 minutes');
        $this->start = $date->format('Y-m-d H:i:s');
        $date->modify('+10 minutes');
        $this->finish =  $date->format('Y-m-d H:i:s');
        $this->save();
    
        $this->createConnections();
            
        $this->confirm();
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

    function getCronValue() {
        return $this->getCron()->one()->freq;
    }
}
