<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/meican_controller.php';

include_once 'libs/auth.php';

include_once 'apps/aaa/models/group_info.php';
include_once 'apps/aaa/models/user_info.php';
include_once 'apps/aaa/models/aros.php';
include_once 'libs/acl_loader.php';

class groups extends MeicanController {

    public $app = 'aaa';
    public $modelClass = 'group_info';
    
    public function beforeFilter(){
        $this->addScriptForLayout(array('select'));
    }

    protected function renderEmpty(){
        $this->set(array(
            'title' => _("User Groups"),
            'message' => _("You can't see any group, click the button below to add one")
            ));
        parent::renderEmpty();
    }

    public function show() {
        if ($allGroups = $this->makeIndex()) {
            $groups = array();
            $acl = AclLoader::getInstance();
            
            foreach ($allGroups as $grp) {
                $group = new stdClass();
                $group->descr = $grp->grp_descr;
                $group->id = $grp->grp_id;
                //$groups[$ind]->deletable = !(AuthSystem::selfUserTest($usr->usr_id));
                $group->deletable = $acl->checkACL('delete', 'group_info', $grp->grp_id);
                $group->editable = $acl->checkACL('update', 'group_info', $grp->grp_id);
                
                $grp_parents = array();
                $grp_aro = new Aros($grp->grp_id, 'group_info');
                if ($parents = $grp_aro->getParentNodes()) {
                    foreach ($parents as $p) {
                        if ($p->model == "group_info") {
                            $grp_tmp = new group_info();
                            $grp_tmp->grp_id = $p->obj_id;
                            if ($ret = $grp_tmp->fetch(FALSE))
                                $grp_parents[] = $ret[0]->grp_descr;
                        }
                    }
                }
                
                $group->parents = ($grp_parents) ? implode('<br>', $grp_parents) : _("Group is root");
                
                $groups[] = $group;
            }
            $this->setArgsToBody($groups);
            $this->render('show');
        }
    }

    public function add_form() {

        $user = new user_info();
        $allUsers = $user->fetch();

        $usersLeftArray = array();

        if ($allUsers) {
            $acl = AclLoader::getInstance();

            foreach ($allUsers as $usr) {
                $user = new stdClass();
                $user->id = $usr->usr_id;
                $user->name = $usr->usr_name;
                $user->editable = $acl->checkACL('update', 'user_info', $usr->usr_id);

                $usersLeftArray[] = $user;
            }
        }

        $group = new group_info();
        $groupsMged = $group->fetch();

        $args = new stdClass();

        $args->groups = $groupsMged;

        $args->users->title = _('Users');
        $args->users->left = $usersLeftArray;
        $args->users->right = array();

        $this->setArgsToBody($args);
        $this->render('add');
    } // addForm

    public function add() {
        $new_group = Common::POST('new_group');
        $parents = Common::POST('parents');

        if ($new_group && $parents) {
            $group_info = new group_info();
            $group_info->grp_descr = $new_group;

            if (!$group_info->fetch(FALSE)) { // verifica se o nome do grupo está disponível
                if ($group_info->insert($parents)) {

                    $resultUsers = $group_info->updateUsers(Common::POST('usedArray'));

                    if ($resultUsers) {
                        $this->setFlash(_("Group") . " '$group_info->grp_descr' " . _("added"), "success");
                        $this->show();
                        return;
                    } else
                        $this->setFlash(_("Fail to add users in group") . " '$group_info->grp_descr'", "error");
                }
                else
                    $this->setFlash(_("Fail to create group"), "error");
            } else
                $this->setFlash(_("Group") . " '$new_group' " . _('already exists'), "error");
        } else
            $this->setFlash(_("Missing argument"), "error");


        $this->add_form();
    } // add

    public function edit($grp_id_array) {
        $groupId = NULL;
        if (array_key_exists('grp_id', $grp_id_array)) {
            $groupId = $grp_id_array['grp_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        // instancia objeto do modelo
        $new_group = new group_info();
        $new_group->grp_id = $groupId;
        $result = $new_group->fetch();

        $group = NULL;
        if ($result === FALSE) {
            $this->setFlash(_("Group not found"), "fatal");
            $this->show();
            return;
        } else {
            $group = $result[0];
        }
        
        $acl = AclLoader::getInstance();

        // busca usuários que não pertencem ao grupo
        $allUsers = $group->fetchUsers(FALSE);
        $leftArray = array();
        
        if ($allUsers)
            foreach ($allUsers as $u) {
                $tmp = new user_info();
                $tmp->usr_id = $u->usr_id;
                $result = $tmp->fetch();

                if ($result) {
                    $user = new stdClass();
                    $user->id = $u->usr_id;
                    $user->name = $u->usr_name;
                    $user->editable = $acl->checkACL('update', 'user_info', $u->usr_id);

                    $leftArray[] = $user;
                }
            }

        // busca usuários que pertencem ao grupo
        $allUsers = $group->fetchUsers();
        $rightArray = array();
        
        if ($allUsers)
            foreach ($allUsers as $u) {
                $tmp = new user_info();
                $tmp->usr_id = $u->usr_id;
                $result = $tmp->fetch();

                if ($result) {
                    $user = new stdClass();
                    $user->id = $u->usr_id;
                    $user->name = $u->usr_name;
                    $user->editable = $acl->checkACL('update', 'user_info', $u->usr_id);

                    $rightArray[] = $user;
                }
            }

        $args = new stdClass();
        $args->title = _('Users');
        $args->left = $leftArray;
        $args->right = $rightArray;

        $args->group = $group;

        $this->setArgsToBody($args);

        $this->render('edit');
    } // edit

    public function update($grp_id_array) {
        $groupId = NULL;
        if (array_key_exists('grp_id', $grp_id_array)) {
            $groupId = $grp_id_array['grp_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $group = new group_info();
        $group->grp_id = $groupId;

        $group_descr = Common::POST('group');

        $usedArray = Common::POST('usedArray');

        if ($usedArray) {
            $resultUser = $group->updateUsers($usedArray);
        } else
            $resultUser = $group->updateUsers(); //caso post venha vazio = grupo sem users

        if ($resultUser) {
            $this->setFlash(_("Group users updated"), "success");
        } else {
            $this->setFlash(_("No changes in group users"), "warning");
        }

        $result = $group->fetch();
        $tmp = $result[0];

        if ($tmp->grp_descr != $group_descr) { //modificou a descr do grupo
            $tmp = new group_info();
            $tmp->grp_descr = $group_descr;

            if ($result = $tmp->fetch()) {
                if ($result[0]->grp_id != $groupId) {
                    $this->setFlash(_("Group") . " '$group_descr' " . _('already exists'), "error");
                    $this->edit($grp_id_array);
                    return;
                }
            }
        }

        $group->grp_descr = $group_descr;

        if ($group->update()) {
            $this->setFlash(_("Group information updated"), "success");
            $this->show();
        } else {
            $this->setFlash(_("No change in group information"), "warning");
            $this->edit($grp_id_array);
        }
    } //update

    public function delete() {
        $del_groups = Common::POST('del_checkbox');

        if ($del_groups) {
            foreach ($del_groups as $grp_id) {
                $group = new group_info();
                $group->grp_id = $grp_id;
                $tmp = $group->fetch();
                $result = $tmp[0];
                if ($group->delete())
                    $this->setFlash(_("Group") . " '$result->grp_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}

?>
