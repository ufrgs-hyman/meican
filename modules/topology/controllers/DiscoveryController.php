<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\DiscoverySource;
use meican\topology\forms\DiscoverySourceForm;
use meican\topology\models\Change;
use meican\topology\services\DiscoveryService;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryController extends RbacController {

    public function actionIndex() {
        $changeProvider = new ActiveDataProvider([
            'query' => Change::find()->groupBy(['domain']),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $sourceProvider = new ActiveDataProvider([
            'query' => DiscoverySource::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        return $this->render('index', array(
            'changeProvider' => $changeProvider,
            'sourceProvider' => $sourceProvider,
        ));
    }

    public function actionDiscover($id) { 
        $ds = new DiscoveryService;
        $ds->execute($id);
        
        $this->redirect("index");
    }

    public function actionCreateSource(){
        $form = new DiscoverySourceForm;
        
        if($form->load($_POST)) {
            if ($form->save()) {
                //$form->saveCron();
                Yii::$app->getSession()->addFlash("success", 
                    Yii::t("topology", "Source {name} added successfully", ['name'=>$form->name]));
                return $this->redirect(array('index'));
            } else {
                foreach($form->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
            }
        }
        
        return $this->render('source/create',[
                'model' => $form,
        ]);
    }
}
