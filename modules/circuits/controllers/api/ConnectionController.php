<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers\api;

use yii\rest\Controller;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use Yii;

use meican\circuits\models\Connection;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ConnectionController extends Controller {

    public function actionIndex($format = 'json', $status = null, $type = null) {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return new ActiveDataProvider([
            'query' => Connection::find(),
        ]);
    }
}