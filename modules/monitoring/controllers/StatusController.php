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
class StatusController extends RbacController {
    
    //instant port status
    public function actionGetByPort($dev, $port) {
        self::beginAsyncAction();

        $data = Yii::$app->cache->get('monitoring.status.dev.'.$dev.'.port.'.$port);

        if ($data === false) {
        
            $port = str_replace('/', '@2F', $port);

            $ch = curl_init();
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,

                CURLOPT_USERAGENT => 'Meican',
                CURLOPT_URL => 'http://monitora.cipo.rnp.br/esmond/v2/device/'.$dev.'/interface/'.$port.'/?format=json'
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
                'status' => 1,
            ]);

            Yii::trace($data);

            // store $data in cache so that it can be retrieved next time
            Yii::$app->cache->set('monitoring.status.dev.'.$dev.'.port.'.$port, $data, 120000);
        }

        return $data;
    }
}

?>