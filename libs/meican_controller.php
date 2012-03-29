<?php

include_once LIBS.'controller.php';

class MeicanController extends Controller {
    protected function makeIndex(){
        $model = new $this->modelClass();
        $ret = $model->fetch(FALSE);
        if (empty($ret)){
            if (method_exists($this, 'setEmptyDb'))
                $this->setEmptyDb();
            $this->render('empty_db', array('app' => 'init', 'controller' => false));
        }
        return $ret;
    }
    
    protected function renderEmpty($title, $message){
        $this->autoRender = false;
    }
}
