<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\monitoring\controllers;

use Yii;

use meican\aaa\RbacController;
use meican\base\utils\DateBuilder;
use meican\topology\models\Device;
use meican\topology\models\Port;

/**
 * @author Maurício Quatrin Guerreiro
 */
class TrafficController extends RbacController {
    
    public function actionIndex() {
        return $this->render('index');
    }

    //history by vlan
    public function actionGetVlanHistory(/*$port, $vlan, $dir, $begin, $end*/) {
        return json_encode([
                //'dev'=> $dev['id'], 
                //'port'=> $portId, 
                'traffic' => json_decode('http://monitora.cipo.rnp.br/esmond/v2/device/MXRJ/interface/xe-3@2F0@2F0.1705/out?begin=1462545617&end=1462632017&format=json'
                    )->data
            ]);
        return 
        
        self::beginAsyncAction();

        $portId = $port;
        $data = Yii::$app->cache->get('monitoring.traffic.history.port.'.$portId.'.vlan.'.$vlan.$begin);

        if ($data === false) {

            $port = Port::find()
                ->where(['id'=>$port])
                ->select(['id', 'device_id', 'name', 'max_capacity'])
                ->one();
            $dev = $port->getDevice()->select(['id', 'node'])->asArray()->one();
            $portName = str_replace('/', '@2F', $port->name);

            $ch = curl_init();
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,

                CURLOPT_USERAGENT => 'Meican',
                CURLOPT_URL => 'http://monitora.cipo.rnp.br/esmond/v2/device/'.$dev['node'].'/interface/'.$portName.'/'.$dir.
                    '?format=json&begin='.$begin//DateTime::now('-60 seconds')->getTimestamp()
            );
            curl_setopt_array($ch , $options);
            $output = curl_exec($ch);
            curl_close($ch);

            Yii::trace($output);

            $output = json_decode($output);

            $data = json_encode([
                'dev'=> $dev['id'], 
                'port'=> $portId, 
                'traffic' => isset($output->data) ? $output->data : 0
            ]);

            // store $data in cache so that it can be retrieved next time
            Yii::$app->cache->set('monitoring.traffic.history.port.'.$portId.'.vlan.'.$vlan.$begin, $data);
        }

        return $data;
    }

    //instant bandwidth
    public function actionGet($dev = null, $port = null, $dir) {
        self::beginAsyncAction();
        $portId = $port;
        $data = Yii::$app->cache->get('monitoring.traffic.port.'.$portId);

        if ($data === false) {

            $port = Port::find()
                ->where(['id'=>$port])
                ->select(['id', 'device_id', 'name', 'max_capacity'])
                ->one();
            $dev = $port->getDevice()->select(['id', 'node'])->asArray()->one();
            $portName = str_replace('/', '@2F', $port->name);

            $ch = curl_init();
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,

                CURLOPT_USERAGENT => 'Meican',
                CURLOPT_URL => 'http://monitora.cipo.rnp.br/esmond/v2/device/'.$dev['node'].'/interface/'.$portName.'/'.$dir.
                    '?format=json&begin='.strtotime('-90 seconds')//DateTime::now('-60 seconds')->getTimestamp()
            );
            curl_setopt_array($ch , $options);
            $output = curl_exec($ch);
            curl_close($ch);

            Yii::trace($output);

            $output = json_decode($output);

            $data = json_encode([
                'dev'=> $dev['id'], 
                'port'=> $portId, 
                'traffic' => isset($output->data[0]) ? ( ( $output->data[0]->val / 1000000 ) / $port->max_capacity ) : 0
            ]);

            // store $data in cache so that it can be retrieved next time
            Yii::$app->cache->set('monitoring.traffic.port.'.$portId, $data);
        }

        return $data;
    }

    public function actionGetHistory($dev, $port, $begin, $end) {
        return '{"url":"http://monitora.cipo.rnp.br/esmond/v2/device/MXAM/interface/ge-2@2F2@2F0/in",
        "data":[{"ts":1462211610,"val":49361282.96666667},{"ts":1462211640,"val":52226996.53333333},
        {"ts":1462211670,"val":51947669.36666667},{"ts":1462211700,"val":53848493.4},{"ts":1462211730,"val":54824730.56666667},
        {"ts":1462211760,"val":51451934.266666666},{"ts":1462211790,"val":53180906.5},{"ts":1462211820,"val":52362476.36666667},
        {"ts":1462211850,"val":49297988.7},{"ts":1462211880,"val":53154153.63333333},{"ts":1462211910,"val":55469100.03333333},
        {"ts":1462211940,"val":58973615.3},{"ts":1462211970,"val":59570313.0},{"ts":1462212000,"val":56210387.56666667},
        {"ts":1462212030,"val":56778564.8},{"ts":1462212060,"val":57761282.43333333},{"ts":1462212090,"val":58766763.733333334},
        {"ts":1462212120,"val":59962150.233333334},{"ts":1462212150,"val":59496986.6},{"ts":1462212180,"val":59699040.8},
        {"ts":1462212210,"val":60363615.733333334},{"ts":1462212240,"val":56867070.833333336},{"ts":1462212270,"val":57738748.03333333},
        {"ts":1462212300,"val":60846507.766666666},{"ts":1462212330,"val":56505367.56666667},{"ts":1462212360,"val":48083415.8},
        {"ts":1462212390,"val":45653701.1},{"ts":1462212420,"val":47457348.833333336},{"ts":1462212450,"val":50436376.833333336},
        {"ts":1462212480,"val":49619391.166666664},{"ts":1462212510,"val":51955495.9},{"ts":1462212540,"val":53725479.733333334},
        {"ts":1462212570,"val":59168671.333333336},{"ts":1462212600,"val":58640175.733333334},{"ts":1462212630,"val":53804185.833333336},
        ts":1462212660,"val":53992898.6},{"ts":1462212690,"val":50700966.333333336},{"ts":1462212720,"val":55148001.166666664},
        {"ts":1462212750,"val":60445635.333333336},{"ts":1462212780,"val":54480671.333333336},{"ts":1462212810,"val":53826474.4},
        {"ts":1462212840,"val":52759975.63333333},{"ts":1462212870,"val":61017306.46666667},{"ts":1462212900,"val":54258981.166666664},
        {"ts":1462212930,"val":57261926.833333336},{"ts":1462212960,"val":54907551.666666664},{"ts":1462212990,"val":54354293.8},
        {"ts":1462213020,"val":54099059.93333333},{"ts":1462213050,"val":53594246.9},{"ts":1462213080,"val":55157339.06666667},
        {"ts":1462213110,"val":50820191.166666664},{"ts":1462213140,"val":32057371.6},{"ts":1462213170,"val":60763359.86666667},
        {"ts":1462213200,"val":55546088.46666667},{"ts":1462213230,"val":43556556.46666667},{"ts":1462213260,"val":56354102.333333336},
        {"ts":1462213290,"val":59108119.233333334},{"ts":1462213320,"val":58144823.43333333},{"ts":1462213350,"val":60795806.833333336},
        {"ts":1462213380,"val":66109413.56666667},{"ts":1462213410,"val":56524518.8},{"ts":1462213440,"val":56551271.833333336},
        {"ts":1462213470,"val":40128950.46666667},{"ts":1462213500,"val":55226607.06666667},{"ts":1462213530,"val":56676670.03333333},
        {"ts":1462213560,"val":57976390.1},{"ts":1462213590,"val":41776719.96666667},{"ts":1462213620,"val":59093487.43333333},
        {"ts":1462213650,"val":60377113.3},{"ts":1462213680,"val":63969331.96666667},{"ts":1462213710,"val":63235331.46666667},
        {"ts":1462213740,"val":55226125.266666666},{"ts":1462213770,"val":61447880.166666664},{"ts":1462213800,"val":55373105.166666664},
        {"ts":1462213830,"val":58817547.4},{"ts":1462213860,"val":58207987.3},{"ts":1462213890,"val":56448201.56666667},
        {"ts":1462213920,"val":51981098.266666666},{"ts":1462213950,"val":53104126.03333333},{"ts":1462213980,"val":58933610.166666664},
        {"ts":1462214010,"val":62872843.166666664},{"ts":1462214040,"val":53252438.4},{"ts":1462214070,"val":53174685.43333333},
        {"ts":1462214100,"val":56654849.06666667},{"ts":1462214130,"val":54599442.86666667},{"ts":1462214160,"val":50814306.53333333},
        {"ts":1462214190,"val":51572032.5},{"ts":1462214220,"val":53268005.2},{"ts":1462214250,"val":59924693.03333333},
        {"ts":1462214280,"val":53344016.13333333},{"ts":1462214310,"val":55187420.666666664},{"ts":1462214340,"val":59330325.5},
        {"ts":1462214370,"val":58063390.233333334},{"ts":1462214400,"val":59479394.833333336},{"ts":1462214430,"val":51749907.9},
        {"ts":1462214460,"val":55329187.86666667},{"ts":1462214490,"val":55614856.266666666},{"ts":1462214520,"val":58160874.6},
        {"ts":1462214550,"val":62732273.833333336},{"ts":1462214580,"val":71747142.4},{"ts":1462214610,"val":65667854.3},
        {"ts":1462214640,"val":63825787.86666667},{"ts":1462214670,"val":60217431.13333333},{"ts":1462214700,"val":74601430.13333334},
        {"ts":1462214730,"val":63962607.13333333},{"ts":1462214760,"val":62963580.43333333},{"ts":1462214790,"val":68786187.06666666},
        {"ts":1462214820,"val":70938920.4},{"ts":1462214850,"val":61398824.7},{"ts":1462214880,"val":57971649.666666664},
        {"ts":1462214910,"val":55409738.733333334},{"ts":1462214940,"val":58329318.46666667},{"ts":1462214970,"val":57381254.3},
        {"ts":1462215000,"val":57656963.733333334},{"ts":1462215030,"val":55086019.8},{"ts":1462215060,"val":54980804.266666666},
        {"ts":1462215090,"val":51715350.13333333},{"ts":1462215120,"val":49570724.06666667},{"ts":1462215150,"val":55720147.93333333},
        {"ts":1462215180,"val":21751224.4}],"begin_time":1462211595,"end_time":1462215195,"agg":"30",
        "cf":"average","resource_uri":"/esmond/v2/device/MXAM/interface/ge-2@2F2@2F0/in"}';
        //return $this->redirect("http://monitora.cipo.rnp.br/esmond/v2/device/".$dev."/interface/".str_replace('_', "@2F", $port)."/in?format=json");
    }
}

?>