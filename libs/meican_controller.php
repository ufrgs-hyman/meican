<?php

include_once LIBS.'controller.php';

class MeicanController extends Controller {
    protected function makeIndex(){
        $model = new $this->modelClass();
        $ret = $model->fetch(FALSE);
        $ret = false;
        if (empty($ret)){
            $this->set('link', array('controller' => $this->controller, 'action' => 'add_form'));
            $this->renderEmpty();
        }
        return $ret;
    }
    
    protected function renderEmpty(){
        $this->render('empty_db', array('app' => 'init', 'controller' => false));
    }
}
