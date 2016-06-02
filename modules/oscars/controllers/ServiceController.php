<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\oscars\controllers;

use yii\console\Controller;
use yii\web\ForbiddenHttpException;
use Yii;

use meican\oscars\services\OscarsService;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ServiceController extends Controller {

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app instanceof \yii\web\Application) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
        return true;
    }

    /**
     * Contact configured OSCARS instance, get (active and scheduled) circuits
     * and save in MEICAN database.
     */
    public function actionLoadCircuits() {
        return OscarsService::loadCircuits('');
    }
}
