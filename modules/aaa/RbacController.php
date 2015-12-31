<?php

namespace meican\aaa;

use yii\filters\AccessControl;
use Yii;

use meican\base\BaseController;
use meican\aaa\models\UserDomainRole;
use meican\topology\models\Domain;

/**
 * RbacController
 * 
 * Controlador de Acesso Baseado em Perfil de Usuário.
 * 
 * Role Based Access Control
 * 
 * @author mqg
 * @since 2.0
 */

abstract class RbacController extends BaseController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],   //Visitantes - Acesso negado antes mesmo de passar pelo RBAC
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],   //Usuarios logados - acesso permitido.
                                            //Subcontrolador ou view deve chamar "can" para
                                            //permissoes mais especificas de cada perfil
                    ],
                ],
            ],
        ];
    }
    
    public function init() {
        parent::init();
    } 
    
    static private function checkPermission($permissions, $role) {
        if (!$role) {
            Yii::trace("Perfil inexistente?");
            return false;
        }
        $roleId = $role->id;
        
        $auth = Yii::$app->getAuthManager();
        $objective = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;
        $module = Yii::$app->controller->module->id;
        
        if ($permissions == null || count($permissions) < 1) {
            return $auth->checkAccess($roleId, $action.ucfirst($objective));
        }
        
        foreach ($permissions as $permission) {
            $permissionArray = explode("/", $permission);
            if(count($permissionArray) > 1) {
                $permission = $permissionArray[1];
                $objective = $permissionArray[0];
                if (strlen($objective) < 1) {
                    $objective = $module;
                }
                if (strlen($permission) < 1) {
                    $permission = $action;
                }
            } elseif(strlen($permission) < 1) {
                $permission = $action;
            } 
            Yii::trace("Required permission: ".$permission.ucfirst($objective));
            return $auth->checkAccess($roleId, $permission.ucfirst($objective));
        }
        return true;
    }
    
    /*
     * Utilização:
     * 
     * self::can('read', null, true);
     * 
     * @param $permissions  Array de permissoes
     * 
     *   'read'                 //permissao simples                     readUser
     *   ''                     //nome da permissao = nome da action.   readUser
     *   null                   //nome da permissao = nome da action.   readUser
     *   'topology/create',     //'topology' é o objetivo da permissao. createTopology
     *   
     * @param $domain       Id do dominio (Domain) a qual se quer obter acesso
     * @param $redirect     Booleano que indica se em caso de negação de acesso o usuário
     *                      deve ser redirecionado para a página de erro padrão (403).
     * 
     */
    static function can($permissions = null, $domain = null, $redirect = false) {
        if (!is_array($permissions)) {
            if ($permissions)
            $permissions = [$permissions];
        }
        
        $userId = Yii::$app->user->getId();
        
        if($domain != null) {
            $roles = UserDomainRole::find()->where([
                    'user_id' => $userId,
                    'domain' => $domain
                    ])->all();
            
            foreach($roles as $role){
                if (self::checkPermission($permissions, $role)) return true;
            }
            
            $roles = UserDomainRole::find()->where([
                    'user_id' => $userId,
                    'domain' => null])->all();
            
            foreach($roles as $role){
                if (self::checkPermission($permissions, $role)) return true;
            }
        } else {
            $roles = UserDomainRole::find()->where([
                    'user_id' => $userId])->all();
             
            foreach ($roles as $role) {
                if (self::checkPermission($permissions, $role)) return true;
            }
        }
             
            if ($redirect) self::redirectToErrorPage();
            return false;
    }
    
    static function redirectToErrorPage() {
        throw new \yii\web\HttpException(403);
    }
    
    static function canRedir($permissions = null, $domain = null) {
        self::can($permissions, $domain, true);
    }
    
    static function whichDomainsCan($permissions = null) {
        $domains = Domain::find()->orderBy(['name' => SORT_ASC])->all();
        $canDomains = [];
        foreach($domains as $domain){
            if(self::can($permissions, $domain->name)) $canDomains[] = $domain;
        }
        return $canDomains;
    }
}
