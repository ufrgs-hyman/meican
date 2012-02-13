<?php

defined('__MEICAN') or die("Invalid access.");

include_once 'libs/controller.php';
include_once 'libs/auth.php';
include_once 'apps/aaa/models/user_info.php';

class login extends Controller {

    public function login() {
        $this->app = 'init';
        $this->controller = 'login';
        $this->defaultAction = 'show';
    }

    public function show($message = NULL) {
        if ($message){
            $this->setArgsToBody($message);
        }
        $this->layout = 'empty';
        
        $this->render();
    }

    public function logout() {
        AuthSystem::userLogout();
        header('HTTP/1.1 401 Logout');
        //header('HTTP/1.1 404 Not Found');
    }

    public function doLogin() {
        $user = new user_info();
        $user->usr_login = Common::POST('login');
        $user->usr_password = md5(Common::POST('password'));
       
        $result = $user->login();
        if ($result) {
            $user = $result[0];
            AuthSystem::setAuthUser($user);
            header('Location: '.Dispatcher::getInstance()->url(array('app' => 'init', 'controller' => 'gui')));
        }

        $this->show(_("Failed on authentication"));
    }

    //    public function expired() {
    //        $message = "Usuário não autenticado";
    //        $this->show($message);
    //    }
}

?>
