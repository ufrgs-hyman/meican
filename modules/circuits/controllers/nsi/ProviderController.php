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
use meican\nsi\WSDLBuilder;

/**
 * Controlador que retorna o WSDL do provedor atualmente configurado.
 * Necessario para o suporte de provedores do tipo OpenNSA
 *
 * @author Maurício Quatrin Guerreiro
 */
class ProviderController extends Controller {

    public $layout = "@meican/base/views/layouts/blank";
    public $enableCsrfValidation = false;

    public function actionIndex() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->getHeaders()->set('Content-Type', 'text/xml');
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->load(Url::to('@web/wsdl/ogf_nsi_connection_provider_v2_0.wsdl', true));
        foreach ($dom->getElementsByTagNameNS('http://schemas.xmlsoap.org/wsdl/soap/', 'address') as $el) {
            $el->setAttribute('location', CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_CS_URL));
        }

        return $dom->saveXML();
    }
}

?>