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
    private $perTyArray = array('read','create','update','delete');


    public function AclLoader() {

        //fazer verificação da necessidade do reload
        $acl_ses = Common::getSessionVariable('acl');
        $last_update_server = Common::getLastUpdate();
        $last_update_client = Common::getSessionVariable('last_update');

        if ($last_update_server && $last_update_client) {
            //Framework::debug('last udpate server',$last_update_server);
            //Framework::debug('last udpate client',$last_update_client);
            if ($last_update_server >= $last_update_client)
                $reload = TRUE;
            else $reload = FALSE;
        } else $reload = TRUE;

        if (!$acl_ses || $reload) {
            $this->acl = $this->reloadACL();
            Common::setSessionVariable('acl', $this->acl);

            Framework::debug("acl tree",$this->acl);

        }
        else $this->acl = $acl_ses;
    }

    public function reloadACL($options = array()) {
        Framework::debug('reloading ACL...');
        
        Framework::$debugMode = 0;

        $time = mktime();

        //procura pelos aro_id do user (pode ser mais de um)
        $aro = new aros();
        $aro->obj_id = AuthSystem::getUserId();
        $aro->model = 'user_info';
        $result = $aro->fetch(FALSE);

        //array com os aros já analisados
        $old = array();

        //preenche array listaros com os aros que devem ser analisados (todos possuem mesma prioridade)
        foreach ($result as $r) {
            $listaros[] = $r->aro_id;
        }

        //array listaros possui nodos de mesma prioridade
        while (isset($listaros)) {
            unset($parentlist);

            //preenche array parentList possui os nodos pai da listaros
            foreach ($listaros as $la) {
                unset($aro);
                $aro = new aros();
                $aro->aro_id = $la;
                $result = $aro->fetch(FALSE);
                if ($result[0]->parent_id) {
                    $parentlist[] = $result[0]->parent_id;
                }
            }

            //listaros é a lista de aro_id que serão checados na tabela aros_acos, todos possuem mesma prioridade
            $old = $this->analisaPrioridadeIgual($listaros, $old);

            //passa para listaros os pais e assim sucessivamente até chegar a raiz
            if (isset($parentlist)) {
                $listaros = $parentlist;
            } else unset($listaros);
        }

        //passa do vetor old para um vetor intermediario para ser passado as variaveis de sessao
        unset($acl);
        foreach ($this->perTyArray as $perTy) {
            ${$perTy} = array_keys($old[$perTy],'allow');
            $acl->{$perTy} = ${$perTy};
        }

        Common::setSessionVariable('last_update', $time);
        Framework::$debugMode = 1;
        return $acl;
    }

    /**
     *
     * @param <array Aros> $listaros aros de mesma prioridade
     * @param <array[perTy][aco_id]> $old array multidimensional de string ('deny','allow')
     * @return <array> novo array old com as permissões de listaros atualizadas
     */
    function analisaPrioridadeIgual($listaros, $old) {
        Framework::debug("analise prioridade igual aros: ",$listaros);

        $new = array();
        //procura na tabela aros_acos os respectivos aros
        foreach ($listaros as $a) {
            $aros_acos = new aros_acos();
            $aros_acos->aro_id = $a;
            $lines_aros_acos = $aros_acos->fetch(FALSE);
            unset($restrModel);

            //analisa
            foreach ($this->perTyArray as $perTy) {
                unset($toSearch);
                $level = 0;
                Framework::debug("***************** perty ", $perTy);

                //avalia privilegios de mesmo nível, constroi um array new com as permissoes
                foreach ($lines_aros_acos as $perm) {
                    Framework::debug("analisando perm ",$perm->perm_id);
                    //ira restringir quais acos serao afetados, afetando apenas aqueles do model estipulado restrModel
                    $aco_id = $perm->aco_id;
                    if ($perm->model) {
                        $aco_t = new Acos();
                        $aco_t->aco_id = $aco_id;
                        $aco_t->model = $perm->model;

                        //se encontrar tal nodo, é do modelo estipulado, vai atribuir permissão
                        if ($result_aco = $aco_t->fetch(FALSE)) {

                            if (!array_key_exists($aco_id, $new[$perTy])) {

                                if ($perm->{$perTy} == 'deny')
                                    $new[$perTy][$aco_id]->value = 'deny';
                                elseif ($perm->{$perTy} == 'allow')
                                    $new[$perTy][$aco_id]->value = 'allow';

                                $new[$perTy][$aco_id]->model = $perm->model;


                            } else { //caso esse aco_id já tenha sido analisado nessa interação
                                //se a permissao seja deny e aquela que estava era allow
                                if ($perm->{$perTy} == 'deny' && $new[$perTy][$aco_id]->value == 'allow')
                                //subscreve a permissao allow e deixa a deny (já que aqui possuem a mesma prioridade)
                                    $new[$perTy][$aco_id]->value = 'deny';
                            }
                        } else {  //model nao conferiu, ira procurar descendo pela arvore, mas nao adiciona no vetor new
                            //tosearch precisa ser em ordem a avaliacao

                            if (isset($toSearch[$perTy][$level][$aco_id])) {
                                $level++;
                                Framework::debug("vai incrementar a porquera",$level);
                            }
                            $toSearch[$perTy][$level][$aco_id]->value = $perm->{$perTy};
                            $toSearch[$perTy][$level][$aco_id]->model = $perm->model;
                            Framework::debug("models diferentes ira analisar filhos", $toSearch);

                        }

                    } else { //nao tem model definido

                        if (!array_key_exists($aco_id, $new[$perTy])) {

                            if ($perm->{$perTy} == 'deny')
                                $new[$perTy][$aco_id]->value = 'deny';
                            elseif ($perm->{$perTy} == 'allow')
                                $new[$perTy][$aco_id]->value = 'allow';

                        } else { //caso esse aco_id já tenha sido analisado nessa interação
                            //se a permissao seja deny e aquela que estava era allow
                            if ($perm->{$perTy} == 'deny' && $new[$perTy][$aco_id]->value == 'allow')
                            //subscreve a permissao allow e deixa a deny (já que aqui possuem a mesma prioridade)
                                $new[$perTy][$aco_id]->value = 'deny';
                        }
                    }

                }  //foreach lines_aros_acos

                //Framework::debug("array new ",$new);
                //colapsar array velho old com novo new, só vai atribuir se o novo nao estiver no velho
                foreach ($new[$perTy] as $aco_id => $perm) {

                    //caso o aco_id não exista no array velho
                    if (!array_key_exists($aco_id, $old[$perTy])) {
                        Framework::debug("vai atribuir ao aco ",$aco_id);
                        Framework::debug("permissao ",$perm->value);
                        $old[$perTy][$aco_id] = $perm->value;

                        if (isset($toSearch[$perTy][$level][$aco_id])) {
                            $level++;
                            Framework::debug("vai incrementar a porquera",$level);
                        }
                        $toSearch[$perTy][$level][$aco_id]->value = $perm->value;
                        //array com os nodos que forma atribuídos permissoes nessa interação
                        //e a permissao que deve ser atribuída a tais nodos
                        if ($perm->model)
                            $toSearch[$perTy][$level][$aco_id]->model = $perm->model;
                    }
                }

                $level++;

                //vai atribuir aos proximos nodos filhos

                for ($ind=0; $ind <= $level; $ind++) {
                    unset($acos);
                    $new = array();

                    //retorna os aco_id dos nodos a serem analisados
                    Framework::debug("tosearch",$toSearch);
                    $children = $this->getacosmesmonivel($toSearch[$perTy][$ind]);
                    Framework::debug("children",$children);
                    if (!$children)
                        break;

                    foreach ($children as $c) {
                        Framework::debug("analisando filhos do aco_id ", $c->parent_id);

                        if (!array_key_exists($c->aco_id, $new[$perTy])) {

                            if ((!$toSearch[$perTy][$ind][$c->parent_id]->model) || ($toSearch[$perTy][$ind][$c->parent_id]->model == $c->model)) {

                                if ($toSearch[$perTy][$ind][$c->parent_id]->value == 'deny')
                                    $new[$perTy][$c->aco_id]->value = 'deny';
                                elseif ($toSearch[$perTy][$ind][$c->parent_id]->value == 'allow')
                                    $new[$perTy][$c->aco_id]->value = 'allow';
                            }
                            $toSearch[$perTy][$level][$c->aco_id]->value = $toSearch[$perTy][$ind][$c->parent_id]->value;

                            if ($toSearch[$perTy][$ind][$c->parent_id]->model)
                                $toSearch[$perTy][$level][$c->aco_id]->model = $toSearch[$perTy][$ind][$c->parent_id]->model;



                        } else {
                            if ($toSearch[$perTy][$ind][$c->parent_id]->value == 'deny' && $new[$perTy][$c->aco_id]->value == 'allow') {
                                $new[$perTy][$c->aco_id]->value = 'deny';
                            }
                        }
                    }


                    foreach ($new[$perTy] as $aco_id => $perm) {
                        if (!array_key_exists($aco_id, $old[$perTy])) {
                            $old[$perTy][$aco_id] = $perm->value;
                            Framework::debug("vai atribuir ao aco ",$aco_id);
                            Framework::debug("permissao ",$perm->value);


//                            $toSearch[$perTy][$level][$aco_id]->value= $perm->value;
//
//                            if ($toSearch[$perTy][$level][$c->parent_id]->model)
//                                $toSearch[$perTy][$level][$aco_id]->model = $toSearch[$perTy][$level][$c->parent_id]->model;

                            //limpa vetor do level já analisado

                        }
                    }
                    unset($toSearch[$perTy][$ind]);
                    $level++;

                } //for level
            } //foreach perty
        } //foreach listaros

        return $old;
    }

    /**
     * Retorna os aco_id imediatamente abaixo do aco em questão
     * @param <array int> $parentlist $toSearch[perTy][aco_id] dos nodos que devem ser procurados/cascateados
     * @return <array> StdObject aco_id e parent_id
     */
    function getacosmesmonivel($parentlist) {
        $new = array();
        $children = array();
        $ind = 0;

        foreach ($parentlist as $aco_id => $val) {
            unset($aco);

            $aco = new Acos();
            $aco->aco_id = $aco_id;


            $result = $aco->getImmediateNodes();

            foreach ($result as $val) {
                $children[$ind]->aco_id = $val->aco_id;
                $children[$ind]->model = $val->model;
                $children[$ind]->parent_id = $val->parent_id;
                $ind++;
            }
        }

        if ($ind > 0)
            return $children;
        else return FALSE;
    }

    function getAllowedPKey($right, $model) {
        //vetor de acos do modelo que o usuario tem acesso
        $acos = $this->acl->{$right};


        if ($acos) {
            $strAcos = implode(',', $acos);

            $acos = new acos();
            //retorna os objetos (obj_id) que o usuário possui acesso
            $result = $acos->getAcoGroupByModel($strAcos, $model);

            $obj = array(); //array com os ids do modelo que o usuário
            foreach ($result as $r) {
                $obj[] = $r->obj_id;
            }

            //$strObj = implode(',', $obj);

            return $obj;
        } else
            return FALSE;
    }


    public function checkACL($right, $model, $id = NULL) {

        $acos = $this->acl->{$right};
        //Framework::debug('acos',$acos);
        if ($acos) {

            $restr = $this->getAllowedPKey($right, $model);

            if ($restr) {
                if ($id) { //se tem o id especificado, deve estar especificado
                    if (array_search($id, $restr) !== FALSE)
                        return TRUE;
                } else { //possui acesso a algum nodo!!! nao precisa necessariamente ser o VOID
                //pode ser qualquer UM. função getvoid parece inútil
                //
                    //nao especificou id, procura pelo void
                    //entes voids: obj_id = NULL, a presença de um ente void
                    //em determinado modelo discrimina os direitos default do usuário
                    //sobre aquele modelo (sem especificar o id)
                    //$aco = new Acos();
                    //$strAcos = implode(',', $acos);
                    //$void = $aco->getVoid($strAcos, $model);
                    //if ($void) //possui acesso ao ente void, logo possui right sobre aquele model
                        return TRUE;
                }
            }
        }
        return FALSE;
    }
} // da classe

?>
