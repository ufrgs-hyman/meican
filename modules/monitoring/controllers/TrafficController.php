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
    
    /**
     * Obtem o historico de trafego de determinada VLAN em determinada Porta e dada direção.
     * 
     * @param port
     *      Port ID, inteiro.
     * @param vlan
     *      VLAN, inteiro.
     * @param dir
     *      Direção do trafego ('in' ou 'out').
     * @param interval
     *      Define o intervalo relativo ao historico solicitado. Apenas são aceitos: 
     *      última hora ou 3600s (dados a cada 30s agregados = padrao esmond), 
     *      ultimo dia ou 86400s (dados a cada 10 min ou 600s agregados),
     *      ultima semana ou 604800s (dados a cada 1h10m ou 4200s agregados),
     *      ultimo mes ou 2592000s (dados a cada 5 horas ou 18000s agregados).
     */
    public function actionGetVlanHistory($dom, $dev, $port, $vlan, $dir, $interval) {
        self::beginAsyncAction();

        if ($dom != 'cipo.rnp.br') 
            throw new \yii\web\HttpException(501, 'Currently only cipo.rnp.br ports are supported.');     
        //urn:ogf:network:cipo.rnp.br:2013::MXRJ:xe-3_0_0:+

        // NSI converte / para _
        $port = str_replace('/', '@2F', $port);
        // NSI insere :+ no final das URNs

        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_USERAGENT => 'Meican',
            CURLOPT_URL => 'http://monitora.cipo.rnp.br/esmond/v2/device/'.$dev.'/interface/'.$port.'.'.$vlan.'/'.$dir.
                '?format=json&begin='.strtotime('-3600 seconds')
        );
        Yii::trace($options);
        curl_setopt_array($ch , $options);
        $output = curl_exec($ch);
        curl_close($ch);

        $output = json_decode($output);
        //if(!isset($output->data))
         //   throw new \yii\web\HttpException(500, 'Monitoring service has been returned a error for your request.');

        $data = json_encode([
            'dev'=> $dev, 
            'port'=> $port, 
            'traffic' => isset($output->data) ? $output->data : 0
        ]);
        
        return $data;
    }

    //instant bandwidth
    public function actionGetOld($port = null, $dir) {
        self::beginAsyncAction();
        
        $portId = $port;
        $data = Yii::$app->cache->get('monitoring.traffic.port.'.$portId);

        if ($data === false) {

            $port = Port::find()
                ->where(['id'=>$port])
                ->select(['id', 'device_id', 'name'])
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
                'traffic' => isset($output->data[0]) ? $output->data[0]->val : 0
            ]);

            // store $data in cache so that it can be retrieved next time
            Yii::$app->cache->set('monitoring.traffic.port.'.$portId, $data);
        }

        return $data;
    }

    //instant bandwidth
    public function actionGet($dev, $port, $vlan, $dir) {
        self::beginAsyncAction();

        $data = Yii::$app->cache->get('monitoring.status.dev.'.$dev.'.port.'.$port.'.vlan.'.$vlan.'.dir.'.$dir);

        if ($data === false) {
        
            $port = str_replace('/', '@2F', $port);

            $ch = curl_init();
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,

                CURLOPT_USERAGENT => 'Meican',
                CURLOPT_URL => 'http://monitora.cipo.rnp.br/esmond/v2/device/'.$dev.'/interface/'.$port.'.'.$vlan.'/'.$dir.
                    '?format=json&begin='.strtotime('-90 seconds')
            );
            curl_setopt_array($ch , $options);
            $output = curl_exec($ch);
            curl_close($ch);

            Yii::trace($output);

            $output = json_decode($output);

            $port = str_replace('@2F', '/', $port);

            $data = json_encode([
                'dev' => $dev,
                'port' => $port,
                'vlan' => $vlan,
                'traffic' => isset($output->data[0]) && $output->data[0]->val ? $output->data[0]->val : 0
            ]);

            Yii::trace($data);

            // store $data in cache so that it can be retrieved next time
            Yii::$app->cache->set('monitoring.status.dev.'.$dev.'.port.'.$port.'.vlan.'.$vlan.'.dir.'.$dir, $data, 5);
        }

        return $data;
    }

    private function buildDummyTrafficHistory($dev, $port) {
        return '{"dev":"MXSC","port":"ge-2@2F3@2F4","traffic":[{"ts":1465923120,"val":35.933333333333},{"ts":1465923150,"val":32.7},{"ts":1465923180,"val":29.766666666667},{"ts":1465923210,"val":36.233333333333},{"ts":1465923240,"val":34.066666666667},{"ts":1465923270,"val":35.066666666667},{"ts":1465923300,"val":33.8},{"ts":1465923330,"val":28},{"ts":1465923360,"val":37.9},{"ts":1465923390,"val":32.966666666667},{"ts":1465923420,"val":36.2},{"ts":1465923450,"val":34},{"ts":1465923480,"val":30.7},{"ts":1465923510,"val":36.1},{"ts":1465923540,"val":32},{"ts":1465923570,"val":34.866666666667},{"ts":1465923600,"val":34.066666666667},{"ts":1465923630,"val":36.1},{"ts":1465923660,"val":32.066666666667},{"ts":1465923690,"val":32.8},{"ts":1465923720,"val":33.9},{"ts":1465923750,"val":36.033333333333},{"ts":1465923780,"val":31.466666666667},{"ts":1465923810,"val":29.966666666667},{"ts":1465923840,"val":36.7},{"ts":1465923870,"val":33.733333333333},{"ts":1465923900,"val":35.733333333333},{"ts":1465923930,"val":34.366666666667},{"ts":1465923960,"val":32.1},{"ts":1465923990,"val":32.733333333333},{"ts":1465924020,"val":35.566666666667},{"ts":1465924050,"val":34.466666666667},{"ts":1465924080,"val":33.133333333333},{"ts":1465924110,"val":33.733333333333},{"ts":1465924140,"val":34},{"ts":1465924170,"val":29.8},{"ts":1465924200,"val":35.866666666667},{"ts":1465924230,"val":36},{"ts":1465924260,"val":34.333333333333},{"ts":1465924290,"val":32.4},{"ts":1465924320,"val":32.566666666667},{"ts":1465924350,"val":35.133333333333},{"ts":1465924380,"val":34.766666666667},{"ts":1465924410,"val":34},{"ts":1465924440,"val":31.8},{"ts":1465924470,"val":34.233333333333},{"ts":1465924500,"val":34.833333333333},{"ts":1465924530,"val":33.3},{"ts":1465924560,"val":33.566666666667},{"ts":1465924590,"val":34},{"ts":1465924620,"val":33.333333333333},{"ts":1465924650,"val":31.266666666667},{"ts":1465924680,"val":34},{"ts":1465924710,"val":36.266666666667},{"ts":1465924740,"val":34.633333333333},{"ts":1465924770,"val":33.3},{"ts":1465924800,"val":32.933333333333},{"ts":1465924830,"val":33.4},{"ts":1465924860,"val":33.466666666667},{"ts":1465924890,"val":35.066666666667},{"ts":1465924920,"val":34.066666666667},{"ts":1465924950,"val":32.333333333333},{"ts":1465924980,"val":33.4},{"ts":1465925010,"val":35.066666666667},{"ts":1465925040,"val":34.7},{"ts":1465925070,"val":32.4},{"ts":1465925100,"val":32.7},{"ts":1465925130,"val":34.466666666667},{"ts":1465925160,"val":34.666666666667},{"ts":1465925190,"val":34},{"ts":1465925220,"val":34.433333333333},{"ts":1465925250,"val":33.866666666667},{"ts":1465925280,"val":32.566666666667},{"ts":1465925310,"val":33.6},{"ts":1465925340,"val":33.266666666667},{"ts":1465925370,"val":34.766666666667},{"ts":1465925400,"val":34.733333333333},{"ts":1465925430,"val":32.133333333333},{"ts":1465925460,"val":33.233333333333},{"ts":1465925490,"val":34.666666666667},{"ts":1465925520,"val":35.266666666667},{"ts":1465925550,"val":32.6},{"ts":1465925580,"val":32.333333333333},{"ts":1465925610,"val":34.3},{"ts":1465925640,"val":34.333333333333},{"ts":1465925670,"val":32.633333333333},{"ts":1465925700,"val":35.866666666667},{"ts":1465925730,"val":33.733333333333},{"ts":1465925760,"val":31.866666666667},{"ts":1465925790,"val":29.433333333333},{"ts":1465925820,"val":39.6},{"ts":1465925850,"val":33.466666666667},{"ts":1465925880,"val":35.533333333333},{"ts":1465925910,"val":31.933333333333},{"ts":1465925940,"val":33.033333333333},{"ts":1465925970,"val":34},{"ts":1465926000,"val":34.066666666667},{"ts":1465926030,"val":34.833333333333},{"ts":1465926060,"val":31.766666666667},{"ts":1465926090,"val":32.066666666667},{"ts":1465926120,"val":37.133333333333},{"ts":1465926150,"val":31.866666666667},{"ts":1465926180,"val":36.1},{"ts":1465926210,"val":31.833333333333},{"ts":1465926240,"val":33},{"ts":1465926270,"val":36.2},{"ts":1465926300,"val":33.966666666667},{"ts":1465926330,"val":32.9},{"ts":1465926360,"val":34},{"ts":1465926390,"val":34},{"ts":1465926420,"val":34},{"ts":1465926450,"val":32.9},{"ts":1465926480,"val":33.966666666667},{"ts":1465926510,"val":36.2},{"ts":1465926540,"val":31.866666666667},{"ts":1465926570,"val":32.866666666667},{"ts":1465926600,"val":33.933333333333},{"ts":1465926630,"val":32.033333333333},{"ts":1465926660,"val":36.733333333333},{"ts":1465926690,"val":3.7666666666667}]}';

        //return $this->redirect("http://monitora.cipo.rnp.br/esmond/v2/device/".$dev."/interface/".str_replace('_', "@2F", $port)."/in?format=json");
    }
}

?>