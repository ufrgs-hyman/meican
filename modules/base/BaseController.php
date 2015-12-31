<?php

namespace meican\base;

use yii\web\Controller;
use Yii;

abstract class BaseController extends Controller {

    public $layout = "@meican/base/views/layouts/main";
    
    public function init() {
        parent::init();
        
        Yii::$app->language = Yii::$app->user->isGuest ? "en-US" : Yii::$app->user->getIdentity()->language;
        Yii::$app->formatter->datetimeFormat = Yii::$app->user->isGuest ? "dd/MM/yyyy HH:mm" : Yii::$app->user->getIdentity()->date_format." ".Yii::$app->user->getIdentity()->time_format;
        Yii::$app->formatter->timeZone = Yii::$app->user->isGuest ? 'America/Sao_Paulo' : Yii::$app->user->getIdentity()->time_zone;
    } 
    
    static function asyncActionBegin() {
        Yii::$app->session->close();
    }
}
