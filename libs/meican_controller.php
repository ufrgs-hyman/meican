<?php

include_once LIBS.'controller.php';

class MeicanController extends Controller {
    protected function makeIndex($conditions = array()){
        $model = new $this->modelClass();
        foreach($conditions as $key => $value)
            $model->{$key} = $value;
        $ret = $model->fetch(FALSE);
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
