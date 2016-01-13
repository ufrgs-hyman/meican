<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base;

use yii\web\Controller;
use Yii;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
abstract class BaseController extends Controller {

    public $layout = "@meican/base/views/layouts/main";
    
    public function init() {
        parent::init();
        
        Yii::$app->language = Yii::$app->user->isGuest ? "en-US" : Yii::$app->user->getIdentity()->language;
        Yii::$app->formatter->datetimeFormat = Yii::$app->user->isGuest ? "dd/MM/yyyy HH:mm" : Yii::$app->user->getIdentity()->date_format." ".Yii::$app->user->getIdentity()->time_format;
        Yii::$app->formatter->timeZone = Yii::$app->user->isGuest ? 'America/Sao_Paulo' : Yii::$app->user->getIdentity()->time_zone;
    } 
    
    /**
     * Indica o início de uma função com lógica independente ao
     * usuário associado a sessão.
     *
     * Determina que a partir desse momento variáveis da sessão
     * não serão mais acessadas. Necessário no caso de funções 
     * com acesso externo, como chamadas SOAP. Sem esta função,
     * uma chamada Ajax pode bloquear o usuário em toda a aplicação,
     * pois apenas um script PHP pode acessar informações da sessão
     * a cada vez.
     *
     * A partir dessa função, N scripts podem ser executados ao mesmo
     * tempo, sempre que apenas um desses N tenha acesso à dados da
     * sessão.
     *
     * Atenção, a partir dessa funcão não será possível usar o RBAC.
     **/
    static function asyncActionBegin() {
        Yii::$app->session->close();
    }
}
