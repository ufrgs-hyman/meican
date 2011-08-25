<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'includes/auth.inc';
include_once 'includes/language.inc';
include_once 'apps/aaa/models/user_info.inc';
include_once 'apps/aaa/models/group_info.inc';
include_once 'apps/aaa/models/aros.inc';
include_once 'apps/aaa/models/acos.inc';
include_once 'libs/acl_loader.inc';

class users extends Controller {

    public function users() {
        $this->app = 'aaa';
        $this->controller = 'users';
        $this->defaultAction = 'show';
    }

    public function show() {
        /** @todo
         *  transformar essas consultas em uma função
         */

        $usr_info = new user_info();
        $allUsers = $usr_info->fetch();

        if ($allUsers) {
            $users = array();
            $acl = new AclLoader();
            
            foreach ($allUsers as $usr) {
                $user = new stdClass();
                $user->name = $usr->usr_name;
                $user->id = $usr->usr_id;
                $user->login = $usr->usr_login;
                
                $user->deletable = $acl->checkACL('delete', 'user_info', $usr->usr_id);
                $user->editable = $acl->checkACL('update', 'user_info', $usr->usr_id);

                $userGrps = $usr->fetchGroups();
                foreach ($userGrps as $ug) {
                    $user->grps[] = $ug->grp_descr;
                }
                
                $users[] = $user;
            }
            $this->setAction('show');
            
            $this->setArgsToBody($users);
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("Users");
            $args->message = _("You can't see any user, click the button below to add one");
            $this->setArgsToBody($args);
        }

        $this->render();
    }

    public function add_form() {
        $grp_info = new group_info();
        $allGroups = $grp_info->fetch();

        if ($allGroups) {
            $leftArray = array();
            $acl = new AclLoader();

            foreach ($allGroups as $grp) {
                $group = new stdClass();
                $group->id = $grp->grp_id;
                $group->name = $grp->grp_descr;
                $group->editable = $acl->checkACL('update', 'group_info', $grp->grp_id);

                $leftArray[] = $group;
            }
            $this->setAction('add');

            $args = new stdClass();
            $args->title = _('Groups');
            $args->left = $leftArray;
            $args->right = array();

            $this->setArgsToBody($args);

            $this->addScript('select');
            $this->render();
        } else {
            $this->setFlash(_("You don't have permission to view groups, so you can't add users"), "warning");
            $this->show();
        }
    }

    public function add() {

        $usr_login = Common::POST('usr_login');
        $usr_name = Common::POST('usr_name');
        $usr_password = Common::POST('usr_password');
        $usr_repassword = Common::POST('retype_password');
        $usedArray = Common::POST('usedArray');

        if ($usr_login && $usr_name && $usr_password && $usr_repassword && $usedArray) {

            if ($usr_password == $usr_repassword) {
                $user_info = new user_info();
                $user_info->usr_login = $usr_login;

                if (!$user_info->fetch(FALSE)) { // verifica se o login está disponível
                    $user_info->usr_name = $usr_name;
                    $user_info->usr_password = $usr_password;
                    $user_info->usr_email = Common::POST("usr_email");

                    $result = $user_info->insert($usedArray);

                    if ($result) {
                        $this->setFlash(_("User") . " '$user_info->usr_login' " . _("added"), "success");
                        $this->show();
                        return;
                    } else
                        $this->setFlash(_("Fail to create user"), "error");
                } else
                    $this->setFlash(_("User") . " '$usr_login' " . _('already exists'), "error");
            } else
                $this->setFlash(_("Passwords mismatch"), "error");
        } else
            $this->setFlash(_("Missing argument"), "error");

        $this->add_form();
    } //add

    public function edit($usr_id_array) {
        $userId = NULL;
        if (array_key_exists('usr_id', $usr_id_array)) {
            $userId = $usr_id_array['usr_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }
        
        $acl = new AclLoader();
        if ($acl->checkACL('update', 'user_info', $userId)) {

            // instancia objeto do modelo
            $new_user = new user_info();
            $new_user->usr_id = $userId;
            $result = $new_user->fetch();

            $user = NULL;
            if ($result === FALSE) {
                $this->setFlash(_("User not found"), "fatal");
                $this->show();
                return;
            } else {
                $user = $result[0];
            }

            // busca grupos que o usuário não faz parte
            $allGroups = $user->fetchGroups(FALSE);
            $leftArray = array();

            if ($allGroups)
                foreach ($allGroups as $grp) {
                    $tmp = new group_info();
                    $tmp->grp_id = $grp->grp_id;
                    $result = $tmp->fetch();
                    
                    if ($result) {
                        $group = new stdClass();
                        $group->id = $grp->grp_id;
                        $group->name = $grp->grp_descr;
                        $group->editable = $acl->checkACL('update', 'group_info', $grp->grp_id);
                        
                        $leftArray[] = $group;
                    }
                }

            // busca grupos que o usuário faz parte
            $allUserGroups = $user->fetchGroups();
            $rightArray = array();

            if ($allUserGroups)
                foreach ($allUserGroups as $grp) {
                    $tmp = new group_info();
                    $tmp->grp_id = $grp->grp_id;
                    $result = $tmp->fetch();
                    
                    if ($result) {
                        $group = new stdClass();
                        $group->id = $grp->grp_id;
                        $group->name = $grp->grp_descr;
                        $group->editable = $acl->checkACL('update', 'group_info', $grp->grp_id);
                        
                        $rightArray[] = $group;
                    }
                }

            $this->setAction('edit');

            $args = new stdClass();
            $args->title = _('Groups');
            $args->left = $leftArray;
            $args->right = $rightArray;

            $args->user = $user;

            $this->setArgsToBody($args);

            $this->addScript("password");
            $this->addScript("select");
            //$this->setArgsToScript(array("nome" => $args));

            $this->render();
        } else {
            $this->setFlash(_("You don't have permission to edit this user"), "warning");
            $this->show();
        }
    } //edit

    public function update($usr_id_array) {
        $userId = NULL;
        if (array_key_exists('usr_id', $usr_id_array)) {
            $userId = $usr_id_array['usr_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $acl = new AclLoader();
        if ($acl->checkACL('update', 'user_info', $userId)) {

            $user = new user_info();
            $user->usr_id = $userId;

            $usedArray = Common::POST('usedArray');

            if (!$usedArray) {
                $this->setFlash(_("Select at least 1 group for the user"), "warning");
                $this->edit($usr_id_array);
                return;
            } else
                $resultGroup = $user->updateGroups($usedArray);

            $usr_name = Common::POST('usr_name');
            $usr_password = Common::POST('usr_password');
            $usr_email = Common::POST("usr_email");

            if ($usr_password) {
                $usr_repassword = Common::POST('retype_password');

                if ($usr_password != $usr_repassword) {
                    $this->setFlash(_("Passwords mismatch"), "error");
                    $this->edit($usr_id_array);
                    return;
                }

                $result = $user->updateTo(array('usr_name' => $usr_name, 'usr_email' => $usr_email, 'usr_password' => $usr_password));
            } else
                $result = $user->updateTo(array('usr_name' => $usr_name, 'usr_email' => $usr_email));

            if ($result) {
                $res = $user->fetch();
                $tmp = $res[0];
                $this->setFlash(_("User") . " '$tmp->usr_login' " . _("updated"), "success");
                $this->show();
            } else {
                $this->setFlash(_("No change has been made"), "warning");
                $this->edit($usr_id_array);
            }
        } else {
            $this->setFlash(_("You don't have permission to edit this user"), "warning");
            $this->show();
        }
    } //update

    /**
     * @todo : incluir mais opções de settings do usuário
     * 
     */
    public function edit_settings() {
        $new_user = new user_info();
        $new_user->usr_id = AuthSystem::getUserId();

        $result = $new_user->fetch();
        $user = NULL;
        if ($result === FALSE) {
            $this->setFlash(_("User not found"), "fatal");
            $this->show();
            return;
        } else {
            $user = $result[0];
        }

        $user->lang = Language::getLang();

        //explode a string com configurações de usuário para extrair
        //somente o formato da data
        $tmp1 = array();
        $tmp2 = array();
        $tmp1 = explode(";", $user->usr_settings);
        $tmp2 = explode('=', $tmp1[0]);

        $user->dateformat = $tmp2[1];

        $this->addScript('password');
        $this->setArgsToBody($user);
        $this->setAction('edit_settings');
        $this->render();
    }

    /**
     *
     * @return <type>
     * @todo: RESOLVER PROBLEMA COM O UPDATE
     */
    public function update_settings() {
        $user = new user_info();
        $user->usr_id = AuthSystem::getUserId();

        $usr_name = Common::POST('usr_name');
        
        if (!$usr_name) {
            $this->setFlash(_("Missing required argument"), "warning");
            $this->edit_settings();
            return;
        }
        
        $old_password = Common::POST('old_usr_password');

        if ($old_password) {
            $user->usr_password = $old_password;
            $result = $user->fetch();

            if ($result) { //senha antiga correta
                $new_password = Common::POST('usr_password');
                $new_repassword = Common::POST('retype_password');
                
                if ($new_password && $new_repassword) {
                    if ($new_password == $new_repassword) {
                        $user->usr_password = $new_password; //ok
                    } else {
                        $this->setFlash(_("Passwords mismatch"), "error");
                        $this->edit_settings();
                        return;
                    }
                } else {
                    $this->setFlash(_("Type the new password"),'error');
                    $this->edit_settings();
                    return;
                }
            } else {
                $this->setFlash(_("Current password does not match"),'error');
                $this->edit_settings();
                return;
            }
        }
        
        $user->usr_name = $usr_name;
        
        $dateFormat = Common::POST('dateformat');
        $lang = Common::POST('lang');

        $user->usr_settings = "date_format=$dateFormat;language=$lang";
        $result = $user->update();

        if ($result)
            $this->setFlash(_("User settings updated successfully"), "success");
        else
            $this->setFlash(_("No change has been made"), "warning");

        if (Language::getLang() != $lang) {
            Language::refreshLangSetting($lang);
            header('HTTP/1.1 405 Change Language');
        }
        $this->edit_settings();
    } //updateUserSettings

    public function delete() {
        $del_users = Common::POST('del_checkbox');

        if ($del_users) {
            foreach ($del_users as $userId) {
                $user = new user_info();
                $user->usr_id = $userId;
                $tmp = $user->fetch();
                $result = $tmp[0];
                if ($user->delete())
                    $this->setFlash(_("User") . " '$result->usr_login' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}

?>
