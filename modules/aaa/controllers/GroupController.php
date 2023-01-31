<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\controllers;

use yii\data\ActiveDataProvider;
use Yii;

use meican\aaa\models\Group;
use meican\aaa\RbacController;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class GroupController extends RbacController {
    
    public function actionIndex() {
        if(!self::can("group/read")){ //Se ele não tiver permissão
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to access Groups'));
            return $this->goHome();
        }
        
        $dataProvider = new ActiveDataProvider([
                'query' => Group::find(),
        ]);
        
        return $this->render('index', array(
                   'groups' => $dataProvider
        ));
    }
    
    public function actionCreate() {
        if(!self::can("group/create")){
            if(!self::can("group/read")) {
                Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to create groups'));
                return $this->goHome();
            }
            else{
                Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to create groups'));
                return $this->redirect(array('index'));
            }
        }

        $group = new Group;
    
        if($group->load($_POST)) {
            if($group->save()) {
                if($group->type == Group::TYPE_SYSTEM){
                    if (isset($_POST['Permissions1']) && $group->setPermissions($_POST['Permissions1'])) {
                        Yii::$app->getSession()->setFlash('success', Yii::t("aaa", 'Group created successfully'));
                    } else {
                        Yii::$app->getSession()->setFlash('warning', Yii::t("aaa", 'Group created. None permissions?'));
                    }
                }
                else{
                    if (isset($_POST['Permissions']) && $group->setPermissions($_POST['Permissions'])) {
                        Yii::$app->getSession()->setFlash('success', Yii::t("aaa", 'Group created successfully'));
                    } else {
                        Yii::$app->getSession()->setFlash('warning', Yii::t("aaa", 'Group created. None permissions?'));
                    }
                }
                            
                return $this->redirect(array('index'));
                        
            } else {
                foreach($group->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->setFlash("error", $error[0]);
                }
            }
        }
    
        return $this->render('create',array(
                'group' => $group,
                'apps' => array(
                    'reservation'=>Yii::t("aaa", 'Reservations'),
                    'waypoint'=>Yii::t("aaa", 'Reservations with Waypoints'),
                    'workflow'=>Yii::t("aaa", 'Workflows'), 
                    'domainTopology'=>Yii::t("aaa","Domain's Topology"),
                    'test'=>Yii::t("aaa", 'Automated Tests'),
                    'role'=>Yii::t("aaa", 'Roles'),
                    'authorization'=>Yii::t("aaa", 'Authorization'),
                ),
                'root' => array(
                    'configuration'=>Yii::t("aaa", 'Reservations Configuration'),
                    'synchronizer'=>Yii::t("aaa", 'Discovery'), 
                    'domain'=>Yii::t("aaa", 'Domains'),
                    'group'=>Yii::t("aaa", 'Groups'),
                    'user'=>Yii::t("aaa", 'Users'),
                ),
        ));
    }
    
    public function actionUpdate($id) {
        if(!self::can("group/update")){
            if(!self::can("group/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to update groups'));
                return $this->redirect(array('index'));
            }
        }
        
        $group = Group::findOne($id);
        
        if(!$group){
            if(!self::can("group/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('danger', Yii::t('topology', 'Group not found'));
                return $this->redirect(array('index'));
            }
        }
        
        $childsChecked = $group->getPermissions();
    
        if($group->load($_POST)) {
            if ($group->save()){
                if($group->type == Group::TYPE_SYSTEM){
                    if (isset($_POST['Permissions1']) && $group->setPermissions($_POST['Permissions1'])) {
                        Yii::$app->getSession()->setFlash('success', Yii::t("aaa", 'Group updated successfully'));
                    } else {
                        Yii::$app->getSession()->setFlash('warning', Yii::t("aaa", 'Group updated. None permissions?'));
                    }
                }
                else{
                    if (isset($_POST['Permissions']) && $group->setPermissions($_POST['Permissions'])) {
                        Yii::$app->getSession()->setFlash('success', Yii::t("aaa", 'Group updated successfully'));
                    } else {
                        Yii::$app->getSession()->setFlash('warning', Yii::t("aaa", 'Group updated. None permissions?'));
                    }
                }
                return $this->redirect(array('index'));
            }
            else {
                foreach($group->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->setFlash("error", $error[0]);
                }
            }
            
        }
    
        return $this->render('update',array(
                'group' => $group,
                'apps' => array(
                    'reservation'=>Yii::t("aaa", 'Reservations'),
                    'waypoint'=>Yii::t("aaa", 'Reservations with Waypoints'),
                    'workflow'=>Yii::t("aaa", 'Workflows'), 
                    'domainTopology'=>Yii::t("aaa","Domain's Topology"),
                    'test'=>Yii::t("aaa", 'Automated Tests'),
                    'role'=>Yii::t("aaa", 'Roles'),
                    'authorization'=>Yii::t("aaa", 'Authorization'),
                ),
                'root' => array(
                    'configuration'=>Yii::t("aaa", 'Reservations Configuration'),
                    'synchronizer'=>Yii::t("aaa", 'Discovery'), 
                    'domain'=>Yii::t("aaa", 'Domains'),
                    'group'=>Yii::t("aaa", 'Groups'),
                    'user'=>Yii::t("aaa", 'Users'),
                ),
                'childsChecked' => $childsChecked
        ));
    }
    
    public function actionDelete(){
        if(!self::can("group/delete")){
            Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete groups'));
            return $this->redirect(array('index'));
        }
            
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $groupId) {
                $group = Group::findOne($groupId);
                if ($group->delete()) {
                    Yii::$app->getSession()->addFlash('success', Yii::t("aaa", 'Group {name} deleted successfully', ['name'=> $group->name]));
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t("aaa", 'Error deleting group').' '.$group->name);
                }
            }
        }
         
        return $this->redirect(array('index'));
    }
}
