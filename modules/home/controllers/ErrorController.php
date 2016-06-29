<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\home\controllers;

use Yii;
use yii\web\Controller;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ErrorController extends Controller {

    public function actionError() {
        $exception = Yii::$app->errorHandler->exception;

        if(Yii::$app->user->isGuest) {
            $this->layout = "@meican/base/views/layouts/error";
            return $this->render('error', ['exception' => $exception]);
        } else {
            $this->layout = "@meican/base/views/layouts/main";
            return $this->render('error',['exception' => $exception]);
        }
    }
}

?>