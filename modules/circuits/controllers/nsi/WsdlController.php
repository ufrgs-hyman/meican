<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers\nsi;

use yii\helpers\Url;
use Yii;
use yii\web\Controller;

use meican\circuits\models\CircuitsPreference;

/**
 * Controlador que retorna o WSDL on the fly.
 * Necessario para o suporte de provedores do tipo OpenNSA que nao oferecem WSDL.
 *
 * @author Maurício Quatrin Guerreiro
 */
class WsdlController extends Controller {

    public $layout = "@meican/base/views/layouts/blank";
    public $enableCsrfValidation = false;

    public function actionBuild($file) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->getHeaders()->set('Content-Type', 'text/xml');

        if ($file == 'ogf_nsi_connection_provider_v2_0.wsdl') {
            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->load(Url::to("@web/wsdl/$file", true));
            $soapns = 'http://schemas.xmlsoap.org/wsdl/soap/';
            foreach ($dom->getElementsByTagNameNS($soapns, 'address') as $el) {
                $el->setAttribute('location', CircuitsPreference::findOneValue(
                    CircuitsPreference::CIRCUITS_DEFAULT_CS_URL));
            }

            return $dom->saveXML();
        }

        return file_get_contents(Url::to("@web/wsdl/$file", true));
    }
}

?>