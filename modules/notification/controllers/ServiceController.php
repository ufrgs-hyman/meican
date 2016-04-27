<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notification\controllers;

use Yii;
use yii\console\Controller;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use meican\notification\services\NotificationServer;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ServiceController extends Controller {
    
    public function actionStart() {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new NotificationServer()
                )
            ),
            8080
        );

        $server->run();
    }
}

?>