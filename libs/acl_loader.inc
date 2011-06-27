<?php
include_once 'libs/tree_model.inc';
include_once 'includes/auth.inc';
include_once 'apps/aaa/models/aros.inc';
include_once 'apps/aaa/models/acos.inc';
include_once 'apps/aaa/models/aros_acos.inc';
include_once 'includes/common.inc';

class AclLoader extends Tree_Model {

    public $model;
    public $acl;
    private $perTyArray = array('create','read','update','delete');


    public function AclLoader() {

        //fazer verificação da necessidade do reload
        $acl_ses = Common::getSessionVariable('acl');
        $last_update_server = Common::getLastUpdate();
        $last_update_client = Common::getSessionVariable('last_update');

        if ($last_update_server && $last_update_client){
            //Framework::debug('last udpate server',$last_update_server);
            //Framework::debug('last udpate client',$last_update_client);
            if ($last_update_server >= $last_update_client)
                $reload = TRUE;
            else $reload = FALSE;
        } else $reload = TRUE;

        if (!$acl_ses || $reload) {
            $this->acl = $this->reloadACL();
            Common::setSessionVariable('acl', $this->acl);
            
        }
        else $this->acl = $acl_ses;
    }

    public function reloadACL($options = array()) {

        $time = mktime();
        Framework::debug('reloading ACL...');
        //procurar na tabela de aros, aonde se encontra o usuário logado
        //unset($aros); //aros na ordem que devem ser examinados
        $aro = new aros();
        $aro->obj_id = AuthSystem::getUserId();
        $aro->model = 'user_info';
        $result = $aro->fetch(FALSE); //retorna os aros que o usuário faz parte
        //$perTy = array ('create','read','update','delete');
        $old = array();


        foreach ($result as $r) {
            $listaros[] = $r->aro_id;
        }

        while (isset($listaros)) {
            unset($parentlist);
            foreach ($listaros as $la) {
                unset($aro);
                $aro = new aros();
                $aro->aro_id = $la;
                $result = $aro->fetch(FALSE);
                if ($result[0]->parent_id) {
                    $parentlist[] = $result[0]->parent_id;
                }

            }
            $old = $this->analisaPrioridadeIgual($listaros, $old);
            if (isset($parentlist)) {
                $listaros = $parentlist;
            } else unset($listaros);
        }

        unset($acl);
        foreach ($this->perTyArray as $perTy) {
            ${$perTy} = array_keys($old[$perTy],'allow');
            $acl->{$perTy} = ${$perTy};
        }

        Common::setSessionVariable('last_update', $time);
        return $acl;


    }

    //function getPriorityNodes
    function analisaPrioridadeIgual($listaros, $old) {
        $new = array();


        foreach ($listaros as $a) {
            $aros_acos = new aros_acos();
            $aros_acos->aro_id = $a;
            $lines_aros_acos = $aros_acos->fetch(FALSE);
            unset($restrModel);
            foreach ($this->perTyArray as $perTy) {
                foreach ($lines_aros_acos as $perm) { //avalia privilegios de mesmo nível
                    //Framework::debug('perm',$perm->perm_id);

                    if ($perm->model){
                        $restrModel = $perm->model;
                    }

                    $aco_id = $perm->aco_id;
                    if (!array_key_exists($aco_id, $new[$perTy])) {
                        if ($perm->{$perTy} == 'deny')
                            $new[$perTy][$aco_id] = 'deny';
                        elseif ($perm->{$perTy} == 'allow')
                            $new[$perTy][$aco_id] = 'allow';
                    } else {
                        if ($perm->{$perTy} == 'deny' && $new[$perTy][$aco_id] == 'allow')
                            $new[$perTy][$aco_id] = 'deny';
                    }
                }
                //colapsar velho com novo
                foreach ($new[$perTy] as $aco_id => $perm) {
                    if (!array_key_exists($aco_id, $old[$perTy])) {

                        if ($restrModel){
                            $aco_t = new Acos();
                            $aco_t->aco_id = $aco_id;
                            $aco_t->model = $restrModel;
                            if ($result_aco = $aco_t->fetch(FALSE)){
                                $old[$perTy][$aco_id] = $perm;
                            }
                        } else $old[$perTy][$aco_id] = $perm;
                        
                        //Framework::debug('atribuindo para aco',$aco_id);
                        $toSearch[$perTy][$aco_id] = $perm;
                    }
                }
                //pegar os filhos
                while ($toSearch[$perTy]) {
                    unset($acos);
                    $acos = array_keys($toSearch[$perTy]);

                    $children = $this->getacosmesmonivel($acos);
                    //Framework::debug('children ',$children);
                    //Framework::debug('nivel');
                    if (!$children) {
                        unset($toSearch);
                        break;
                    }
                    $new = array();
                    foreach ($children as $c) {
                        // if ($new[$perTy])
                        if (!array_key_exists($c->aco_id, $new[$perTy])) {
                            if ($toSearch[$perTy][$c->parent_id] == 'deny')
                                $new[$perTy][$c->aco_id] = 'deny';
                            elseif ($toSearch[$perTy][$c->parent_id] == 'allow')
                                $new[$perTy][$c->aco_id] = 'allow';
                        } else {
                            if ($toSearch[$perTy][$c->parent_id] == 'deny' && $new[$perTy][$c->aco_id] == 'allow')
                                $new[$perTy][$c->aco_id] = 'deny';
                        }
                    }
                    unset($toSearch);
                    foreach ($new[$perTy] as $aco_id => $perm) {
                        if (!array_key_exists($aco_id, $old[$perTy])) {

                             if ($restrModel){
                                  $aco_t = new Acos();
                                  $aco_t->aco_id = $aco_id;
                                  $aco_t->model = $restrModel;
                             if ($result_aco = $aco_t->fetch(FALSE)){
                                  $old[$perTy][$aco_id] = $perm;
                            }
                        } else $old[$perTy][$aco_id] = $perm;
                            //Framework::debug('atribuindo para aco while',$aco_id);
                            $toSearch[$perTy][$aco_id] = $perm;
                        }
                    }
                }
            }
        }
        return $old;
    }

    function getacosmesmonivel($parentlist) {
        $new = array();
        $children = array();
        $ind = 0;
        
        foreach ($parentlist as $p) {
            unset($aco);
            $aco = new Acos();
            $aco->aco_id = $p;
            $result = $aco->getImmediateNodes();

            foreach ($result as $val) {
                $children[$ind]->aco_id = $val->aco_id;
                $children[$ind]->parent_id = $p;
                $ind++;
            }
            //Framework::debug('child step',$children);
        }

        if (count($children) > 0)
            return $children;
        else return FALSE;


    }
    function getAllowedPKey($right, $model) {

        $acos = $this->acl->{$right};
       

        if ($acos) {
            $strAcos = implode(',', $acos);

            $acos = new acos();
            $result = $acos->getAcoGroupByModel($strAcos, $model);

            unset($obj); //array com os ids do modelo que o usuário
            foreach ($result as $r) {
                $obj[] = $r->obj_id;
            }

            //$strObj = implode(',', $obj);

            return $obj;
        } else
            return FALSE;
    }


    public function checkACL($right, $model, $id = NULL) {

        $acos = $this->acl->$right;

        $recurso = new $model;
        $pk = $recurso->getPrimaryKey();


        $restr = $this->getAllowedPKey($right, $model);

        //debug($restr);

        if ($restr) { //se não retornar nada, não possui acesso a nada

            if ($id) {
                if (array_search($id, $restr) === FALSE)
                    return FALSE;
                else return TRUE;

            } else return TRUE;
        } else return FALSE;
    } // da checkACL

} // da classe

?>
