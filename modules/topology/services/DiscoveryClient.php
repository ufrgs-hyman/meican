<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\services;

use Yii;
use yii\helpers\Url;

use meican\topology\models\TopologySynchronizer;
use meican\topology\components\NSIParser;
use meican\base\models\Preference;

/**
 * Classe que implementa o módulo Cliente do protocolo NSI Document Distribution Service (DDS), 
 * também conhecido como Discovery Service.
 *
 * Envia mensagens para provedores NSI para criar, alterar ou remover subscrições para notificações.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryClient {
    
    static function subscribe($url) {
        $ch = curl_init();

        $message = '<?xml version="1.0" encoding="UTF-8"?><tns:subscriptionRequest '.
                    'xmlns:tns="http://schemas.ogf.org/nsi/2014/02/discovery/types">'.
                '<requesterId>urn:ogf:network:'.Preference::findOneValue(Preference::MEICAN_NSA).'</requesterId>'.
                '<callback>'.Url::toRoute("/topology/discovery/notification", "http").'</callback>'.
                '<filter>'.
                    '<include>'.
                        '<event>All</event>'.
                    '</include>'.
                '</filter>'.
                '</tns:subscriptionRequest>';
         
        $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST            => 1,
                CURLOPT_POSTFIELDS  => $message,
                CURLOPT_HTTPHEADER => array(
                        'Accept-encoding: application/xml;charset=utf-8',
                        'Content-Type: application/xml;charset=utf-8'),
                CURLOPT_USERAGENT => 'Meican',
                CURLOPT_URL => $url.'/subscriptions',
        );
         
        curl_setopt_array($ch , $options);

        Yii::trace($message);
        $output = curl_exec($ch);
        Yii::trace($output);

        curl_close($ch);

        $parser = new NSIParser;
        $parser->loadXml($output);
        $parser->parseSubscriptions();

        foreach ($parser->getData()['subs'] as $subId => $sub) {
            return (string) $subId;
        }
    }

    static function unsubscribe($url, $subId) {
        $ch = curl_init();
         
        $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => array(
                        'Accept-encoding: application/xml;charset=utf-8',
                        'Content-Type: application/xml;charset=utf-8'),
                CURLOPT_USERAGENT => 'Meican',
                CURLOPT_URL => $url.'/subscriptions/'.$subId,
        );
         
        curl_setopt_array($ch , $options);
         
        $output = curl_exec($ch);
        Yii::trace($output);

        curl_close($ch);
    }
}
