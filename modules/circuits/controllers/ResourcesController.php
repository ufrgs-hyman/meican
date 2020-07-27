<?php
/**
 * @copyright Copyright (c) 2012-2020 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers;

use Yii;

use meican\aaa\RbacController;


/**
 * @author Leonardo Lauryel Batista dos Santos
 */
class ResourcesController extends RbacController {
    
    public function actionIndex(){
        return $this->render('index');
    }

}
