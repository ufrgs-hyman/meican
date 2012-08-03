<?php

include_once LIBS.'controller.php';

class MeicanController extends Controller {
    protected function makeIndex($conditions = array()){
        $model = new $this->modelClass();
        if (array_key_exists('useACL', $conditions)) {
            $useACL = $conditions['useACL'];
            unset($conditions['useACL']);
        } else
            $useACL = true;
        foreach($conditions as $key => $value)
            $model->{$key} = $value;
        $ret = $model->fetch($useACL);
        if (empty($ret)){
            $this->set('link', array('app' => $this->app, 'controller' => $this->controller, 'action' => 'add_form'));
            $this->renderEmpty();
        }
        return $ret;
    }
    
    protected function renderEmpty(){
        $this->render('empty_db', array('app' => 'init', 'controller' => false));
    }
}
