<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\DiscoveryRule;
use meican\topology\models\DiscoverySearch;
use meican\topology\forms\DiscoveryRuleForm;
use meican\topology\models\Change;
use meican\topology\services\DiscoveryService;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryController extends RbacController {

    public function actionIndex() {
        $changeProvider = new ActiveDataProvider([
            'query' => Change::find()->groupBy(['domain'])->select(['*,COUNT(*) AS count']),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $searchProvider = new ActiveDataProvider([
            'query' => DiscoverySearch::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $ruleProvider = new ActiveDataProvider([
            'query' => DiscoveryRule::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        return $this->render('index', array(
            'changeProvider' => $changeProvider,
            'ruleProvider' => $ruleProvider,
            'searchProvider' => $searchProvider,
        ));
    }

    public function actionDiscover($id) { 
        $ds = new DiscoveryService;
        $ds->execute($id);
        
        $this->redirect("index");
    }

    public function actionCreateRule(){
        $form = new DiscoveryRuleForm;
        
        if($form->load($_POST)) {
            if ($form->save()) {
                //$form->saveCron();
                Yii::$app->getSession()->addFlash("success", 
                    Yii::t("topology", "Rule {name} added successfully", ['name'=>$form->name]));
                return $this->redirect(array('index'));
            } else {
                foreach($form->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
            }
        }
        
        return $this->render('rule/create',[
                'model' => $form,
        ]);
    }
}
