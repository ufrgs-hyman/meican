<?php
/**
 * @copyright Copyright (c) 2012-2020 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers;

use Yii;

use meican\aaa\RbacController;
use meican\circuits\forms\ResourcesForm;


/**
 * @author Leonardo Lauryel Batista dos Santos
 */
class ResourcesController extends RbacController {
    
    public function actionIndex(){

        $model = new ResourcesForm;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->render('create', ['model' => $model]);
        } else {
            return $this->render('index', ['model' => $model]);
        }

    }

    public function actionCreate(){
        return $this->render('create'); 
    }
}
